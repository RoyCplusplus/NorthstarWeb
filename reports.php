<?php
include 'config.php';

// Pastikan player sudah login UCP
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Proses jika player mengirim laporan atau pertanyaan baru
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $title    = mysqli_real_escape_string($conn, $_POST['title']);
    $content  = mysqli_real_escape_string($conn, $_POST['content']);
    $proof_link = mysqli_real_escape_string($conn, $_POST['proof_link']); // Link eksternal (opsional)
    
    $proof_image = NULL; // Default jika tidak mengupload foto

    if (empty($title) || empty($content)) {
        $error = "Judul dan isi laporan/pertanyaan tidak boleh kosong!";
    } else {
        // Cek apakah ada file foto yang di-upload
        if (isset($_FILES['proof_file']) && $_FILES['proof_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp  = $_FILES['proof_file']['tmp_name'];
            $file_name = $_FILES['proof_file']['name'];
            $file_size = $_FILES['proof_file']['size'];
            
            // Ekstensi yang diperbolehkan
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (in_array($file_ext, $allowed_ext)) {
                // Batasi ukuran file max 2MB
                if ($file_size <= 2 * 1024 * 1024) {
                    // Buat nama file unik agar tidak bentrok
                    $new_file_name = 'proof_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
                    $upload_dir = 'uploads/';

                    // Pindahkan file ke folder uploads/
                    if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                        $proof_image = $new_file_name;
                    } else {
                        $error = "Gagal mengunggah file foto.";
                    }
                } else {
                    $error = "Ukuran foto terlalu besar! Maksimal 2MB.";
                }
            } else {
                $error = "Format foto tidak valid! Gunakan JPG, JPEG, PNG, atau WEBP.";
            }
        }

        // Jika tidak ada error upload, simpan ke database
        if (empty($error)) {
            // Gunakan file foto yang di-upload, atau fallback ke link eksternal jika ada
            $proof_final = $proof_image ? $proof_image : $proof_link;

            $query = "INSERT INTO forum_posts (user_id, category, title, content, proof, status) 
                      VALUES ('$user_id', '$category', '$title', '$content', '$proof_final', 'Open')";
            
            if (mysqli_query($conn, $query)) {
                $success = "Laporan / Pertanyaan berhasil dikirim!";
            } else {
                $error = "Terjadi kesalahan sistem saat menyimpan ke database.";
            }
        }
    }
}

// Ambil daftar laporan/pertanyaan
$reports = mysqli_query($conn, "SELECT forum_posts.*, users.username FROM forum_posts JOIN users ON forum_posts.user_id = users.id ORDER BY forum_posts.id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan & Tanya Admin - Northstar Roleplay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Northstar RP</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="ucp.php">UCP Panel</a>
                <a class="nav-link" href="index.php">Forum & Sosmed</a>
                <a class="nav-link active text-warning" href="report.php">Lapor Abuse / Tanya Admin</a>
                <a class="nav-link text-danger" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4" style="max-width: 800px;">
        <h2 class="mb-3">Pusat Laporan & Bantuan Admin</h2>
        <p class="text-muted">Gunakan halaman ini untuk melaporkan Player Abuse, Bug Exploit, atau bertanya seputar server kepada Admin.</p>
        <hr class="mb-4">

        <?php if($error): ?><div class="alert alert-danger"><?=$error?></div><?php endif; ?>
        <?php if($success): ?><div class="alert alert-success"><?=$success?></div><?php endif; ?>

        <!-- Form Buat Laporan / Tanya Admin (Tambahkan enctype untuk file upload) -->
        <div class="card bg-secondary text-white mb-5 shadow">
            <div class="card-body">
                <h5 class="card-title mb-3">Buat Tiket Baru</h5>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Kategori Tiket</label>
                        <select name="category" class="form-select bg-dark text-white" required>
                            <option value="Player Abuse">Lapor Player Abuse / Cheater</option>
                            <option value="Bug Exploit">Lapor Bug / Exploitasi Sistem</option>
                            <option value="Tanya Admin">Tanya Admin / Kendala Server</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Judul Kendala / Laporan</label>
                        <input type="text" name="title" class="form-control bg-dark text-white" placeholder="Contoh: Pasal Abuse / Tanya Fitur UCP" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload Bukti Foto (Opsional, Maks 2MB)</label>
                        <input type="file" name="proof_file" class="form-control bg-dark text-white" accept="image/png, image/jpeg, image/jpg, image/webp">
                        <small class="text-white-50">Atau masukkan link eksternal di bawah jika tidak upload file.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Atau Link Bukti Eksternal (Opsional)</label>
                        <input type="url" name="proof_link" class="form-control bg-dark text-white" placeholder="https://imgur.com/... atau https://youtube.com/...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Penjelasan Detail</label>
                        <textarea name="content" class="form-control bg-dark text-white" rows="4" placeholder="Jelaskan kronologi atau pertanyaan Anda secara rinci..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 fw-bold">Kirim Tiket Laporan</button>
                </form>
            </div>
        </div>

        <!-- Daftar Laporan / Tiket Aktif -->
        <h4 class="mb-3">Daftar Tiket & Laporan Komunitas</h4>
        <?php while($r = mysqli_fetch_assoc($reports)): ?>
            <div class="card bg-secondary text-white mb-3 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title text-warning mb-1"><?=htmlspecialchars($r['title'])?></h5>
                        <div>
                            <span class="badge bg-info text-dark"><?=$r['category']?></span>
                            <span class="badge bg-<?=($r['status'] == 'Open' ? 'success' : 'secondary')?>"><?=$r['status']?></span>
                        </div>
                    </div>
                    <small class="text-muted">Dilaporkan oleh: <b><?=$r['username']?></b> pada <?=$r['created_at']?></small>
                    <p class="mt-3 mb-2"><?=nl2br(htmlspecialchars($r['content']))?></p>
                    
                    <?php if(!empty($r['proof'])): ?>
                        <div class="mt-2">
                            <?php 
                                // Cek apakah bukti berupa file lokal di folder uploads/ atau link eksternal (http)
                                $proof_val = $r['proof'];
                                if(filter_var($proof_val, FILTER_VALIDATE_URL)): 
                            ?>
                                <a href="<?=$proof_val?>" target="_blank" class="btn btn-sm btn-outline-light">🔍 Buka Link Bukti</a>
                            <?php else: ?>
                                <a href="uploads/<?=$proof_val?>" target="_blank" class="btn btn-sm btn-outline-info">🖼️ Lihat Foto Bukti (Klik untuk memperbesar)</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
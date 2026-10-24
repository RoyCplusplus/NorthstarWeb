<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Proses posting status/thread baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['content'])) {
    $user_id = $_SESSION['user_id'];
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $content  = mysqli_real_escape_string($conn, $_POST['content']);

    mysqli_query($conn, "INSERT INTO forum_posts (user_id, category, content) VALUES ('$user_id', '$category', '$content')");
    header("Location: index.php");
    exit;
}

// Ambil semua postingan forum
$posts = mysqli_query($conn, "SELECT forum_posts.*, users.username FROM forum_posts JOIN users ON forum_posts.user_id = users.id ORDER BY forum_posts.id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Forum & Feed - Northstar Roleplay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
    <nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Northstar RP</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="ucp.php">UCP Panel</a>
                <a class="nav-link active" href="index.php">Forum & Sosmed</a>
                <a class="nav-link text-warning" href="report.php">Lapor Abuse / Tanya Admin</a>
                <a class="nav-link text-danger" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4" style="max-width: 700px;">
        <!-- Form Posting -->
        <div class="card bg-secondary text-white mb-4">
            <div class="card-body">
                <h5>Buat Postingan / Diskusi</h5>
                <form method="POST">
                    <div class="mb-2">
                        <select name="category" class="form-select bg-dark text-white">
                            <option value="Sosmed">Sosial Media (Feed Bebas)</option>
                            <option value="Tanya Admin">Tanya Admin / Server</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <textarea name="content" class="form-control bg-dark text-white" rows="3" placeholder="Apa yang sedang dibahas atau dibagikan..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Posting</button>
                </form>
            </div>
        </div>

        <!-- Feed List -->
        <h4>Timeline Komunitas</h4>
        <hr>
        <?php while($p = mysqli_fetch_assoc($posts)): ?>
            <div class="card bg-secondary text-white mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h6 class="text-info"><?=$p['username']?></h6>
                        <span class="badge bg-dark"><?=$p['category']?></span>
                    </div>
                    <p class="mt-2 mb-1"><?=nl2br(htmlspecialchars($p['content']))?></p>
                    <small class="text-muted"><?=$p['created_at']?></small>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
<?php
include 'config.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5($_POST['password']);
    
    // Generate kode verifikasi 6 digit angka acak
    $v_code   = rand(100000, 999999);

    // Cek apakah username UCP atau email sudah terdaftar
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' OR email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Username UCP atau Email sudah terdaftar!";
    } else {
        // Simpan data dengan status is_active = 0
        $query = "INSERT INTO users (username, email, password, verification_code, is_active) 
                  VALUES ('$username', '$email', '$password', '$v_code', 0)";
        
        // Di dalam file register.php, tepat setelah query INSERT berhasil:
        if (mysqli_query($conn, $query)) {
            // Panggil fungsi kirim email
            include 'send_email.php';
            $terkirim = kirimEmailVerifikasi($email, $username, $v_code);

            if ($terkirim) {
                $_SESSION['verify_email'] = $email;
                header("Location: verify.php");
                exit;
            } else {
                $error = "Gagal mengirim email verifikasi. Silakan hubungi admin.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register UCP - Northstar Roleplay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
    <div class="container mt-5" style="max-width: 400px;">
        <h3 class="text-center mb-4">Northstar Roleplay - Register</h3>
        <?php if($error): ?><div class="alert alert-danger"><?=$error?></div><?php endif; ?>
        <form method="POST" class="bg-secondary p-4 rounded">
            <div class="mb-3">
                <label>Username UCP</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email (Gmail)</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register & Kirim Kode</button>
            <p class="mt-3 text-center"><a href="login.php" class="text-white">Sudah punya akun? Login</a></p>
        </form>
    </div>
</body>
</html>
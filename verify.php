<?php
include 'config.php';
$error = '';
$success = '';

if (!isset($_SESSION['verify_email'])) {
    header("Location: register.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = mysqli_real_escape_string($conn, $_POST['code']);
    $email = $_SESSION['verify_email'];

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND verification_code = '$code'");
    if (mysqli_num_rows($check) == 1) {
        // Update status akun menjadi aktif (is_active = 1) dan hapus kode verifikasi
        mysqli_query($conn, "UPDATE users SET is_active = 1, verification_code = NULL WHERE email = '$email'");
        unset($_SESSION['verify_email']);
        $success = "Akun berhasil diverifikasi! Silakan <a href='login.php'>Login disini</a>.";
    } else {
        $error = "Kode verifikasi salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Akun - Northstar Roleplay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
    <div class="container mt-5" style="max-width: 400px;">
        <h3 class="text-center mb-4">Verifikasi Kode Gmail</h3>
        <p class="text-center text-muted">Kami telah mengirimkan kode verifikasi ke email Anda.</p>
        <?php if($error): ?><div class="alert alert-danger"><?=$error?></div><?php endif; ?>
        <?php if($success): ?><div class="alert alert-success"><?=$success?></div><?php endif; ?>
        <?php if(!$success): ?>
        <form method="POST" class="bg-secondary p-4 rounded">
            <div class="mb-3">
                <label>Masukkan 6 Digit Kode</label>
                <input type="text" name="code" class="form-control text-center fs-4" maxlength="6" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Verifikasi Akun</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
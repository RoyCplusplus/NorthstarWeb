<?php
include 'config.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']); // Sesuaikan enkripsinya

    $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' AND password = '$password'");
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['id']; // ID UCP
        $_SESSION['username'] = $row['username'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Akun belum diverifikasi atau Username/Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login UCP - Northstar Roleplay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
    <div class="container mt-5" style="max-width: 400px;">
        <h3 class="text-center mb-4">Northstar Roleplay - Login UCP</h3>
        <?php if($error): ?><div class="alert alert-danger"><?=$error?></div><?php endif; ?>
        <form method="POST" class="bg-secondary p-4 rounded">
            <div class="mb-3">
                <label>Username UCP</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Login</button>
            <p class="mt-3 text-center"><a href="register.php" class="text-white">Belum punya akun? Register</a></p>
        </form>
    </div>
</body>
</html>
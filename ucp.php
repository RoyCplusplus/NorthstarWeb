<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Ambil data karakter in-game yang terikat dengan UCP ID ini (sesuaikan nama kolom tabel di gamemode Anda, misal: ucp / ucp_id)
$chars = mysqli_query($conn, "SELECT * FROM characters WHERE ucp_id = '$user_id'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>UCP Panel - Northstar Roleplay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
    <nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Northstar RP</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link active" href="ucp.php">UCP Panel</a>
                <a class="nav-link" href="report.php">Forum / Laporan</a>
                <a class="nav-link text-danger" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>Dashboard UCP: <b><?=$username?></b></h2>
        <hr>
        <h4>Karakter In-Game Anda:</h4>
        <div class="row mt-3">
            <?php if(mysqli_num_rows($chars) > 0): ?>
                <?php while($c = mysqli_fetch_assoc($chars)): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-secondary text-white">
                            <div class="card-body">
                                <h5 class="card-title"><?=$c['character_name']?></h5>
                                <p class="card-text">
                                    Money: $<?=number_format($c['money'])?><br>
                                    Level: <?=$c['level']?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted">Belum ada karakter yang dibuat di dalam game.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
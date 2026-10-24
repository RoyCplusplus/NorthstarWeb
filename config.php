<?php
$host = 'localhost';
$user = 'root'; // Ganti dengan username database Anda
$pass = '';     // Ganti dengan password database Anda
$db   = 'northstar_web'; // Ganti dengan nama database SAMP Anda

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Mulai session untuk manajemen login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
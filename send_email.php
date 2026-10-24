<?php
// Panggil file autoloader Composer atau manual file PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function kirimEmailVerifikasi($tujuan, $username, $kode) {
    $mail = new PHPMailer(true);

    try {
        // Konfigurasi Server SMTP Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'northstarroleplay65@gmail.com';     // Ganti dengan Gmail Anda
        $mail->Password   = 'yfdv ghpj mkjg bftc';       // Ganti dengan 16 digit App Password dari Langkah 1
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Pengirim & Penerima
        $mail->setFrom('northstarroleplay65@gmail.com', 'Northstar Roleplay');
        $mail->addAddress($tujuan, $username);

        // Konten Email
        $mail->isHTML(true);
        $mail->Subject = 'Kode Verifikasi UCP - Northstar Roleplay';
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; color: #333;'>
                <h2>Halo, <b>$username</b>!</h2>
                <p>Terima kasih telah mendaftarkan akun UCP di <b>Northstar Roleplay</b>.</p>
                <p>Berikut adalah kode verifikasi Anda:</p>
                <h1 style='background: #f4f4f4; padding: 10px; width: fit-content; letter-spacing: 5px; color: #0d6efd;'>$kode</h1>
                <p>Masukkan kode ini di halaman website untuk mengaktifkan akun Anda.</p>
                <br>
                <p><i>Salam, <br>Tim Administrasi Northstar Roleplay</i></p>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
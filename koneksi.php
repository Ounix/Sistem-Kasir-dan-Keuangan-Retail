<?php
$host = "localhost";
$user = "root";       // Username default XAMPP
$pass = "";           // Password default XAMPP biasanya kosong
$db   = "db_kasir_retail"; // Nama database yang tadi kita buat

// Perintah untuk konek ke database
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek apakah koneksi berhasil?
if (!$koneksi) {
    die("Gagal konek ke database: " . mysqli_connect_error());
}
?>
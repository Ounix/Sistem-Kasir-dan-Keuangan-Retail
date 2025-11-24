<?php
session_start();
include 'koneksi.php';

// CEK: Hanya Admin yang boleh akses
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin'){ 
    echo "Hanya Admin yang boleh reset database!";
    exit; 
}

// 1. Matikan pengecekan Foreign Key (Supaya bisa kosongkan tabel tanpa error)
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 0");

// 2. KOSONGKAN TABEL & RESET ID KE 1 (TRUNCATE)
// TRUNCATE otomatis menghapus semua data DAN mereset ID kembali ke 1
$reset_detail = mysqli_query($koneksi, "TRUNCATE TABLE detail_transaksi");
$reset_header = mysqli_query($koneksi, "TRUNCATE TABLE transaksi");

// 3. Hidupkan kembali pengecekan Foreign Key
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 1");

if ($reset_header && $reset_detail) {
    echo "<h1>BERHASIL!</h1>";
    echo "<p>Semua riwayat transaksi sudah dihapus bersih.</p>";
    echo "<p>ID Transaksi sudah di-reset kembali ke nomor 1.</p>";
    echo "<a href='dashboard.php'>Kembali ke Dashboard</a>";
} else {
    echo "Gagal reset: " . mysqli_error($koneksi);
}
?>
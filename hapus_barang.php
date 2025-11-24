<?php
// Pastikan session dimulai untuk menyimpan pesan sukses/error
session_start();

include 'cek_admin.php'; // Menggunakan access control
include 'koneksi.php';

if (isset($_GET['id'])) {
    $produk_id = $_GET['id'];
    
    // 1. Ambil nama produk dulu (untuk pesan notifikasi nanti)
    $query_nama = mysqli_query($koneksi, "SELECT nama_produk FROM produk WHERE produk_id=$produk_id");
    $data_nama  = mysqli_fetch_assoc($query_nama);
    $nama_produk = $data_nama ? $data_nama['nama_produk'] : 'Barang';

    // -----------------------------------------------------------
    // PERBAIKAN ERROR DI SINI
    // Langkah 1: Hapus dulu riwayat transaksi barang ini (Tabel Anak)
    // -----------------------------------------------------------
    $query_hapus_anak = "DELETE FROM detail_transaksi WHERE produk_id=$produk_id";
    mysqli_query($koneksi, $query_hapus_anak);

    // -----------------------------------------------------------
    // Langkah 2: Baru boleh hapus Data Barangnya (Tabel Induk)
    // -----------------------------------------------------------
    $query_hapus_induk = "DELETE FROM produk WHERE produk_id=$produk_id";
    
    if (mysqli_query($koneksi, $query_hapus_induk)) {
        // Jika berhasil
        $_SESSION['pesan'] = "Barang **$nama_produk** berhasil dihapus (beserta riwayat penjualannya).";
    } else {
        // Jika masih gagal
        $_SESSION['pesan_error'] = "Gagal menghapus barang: " . mysqli_error($koneksi);
    }

} else {
    $_SESSION['pesan_error'] = "ID barang tidak valid.";
}

// Kembali ke dashboard
header("Location: dashboard.php");
exit;
?>
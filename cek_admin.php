<?php
session_start();
// Pastikan sudah login dan role adalah Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    // Redirect ke dashboard (Implementasi Error Handling dokumen)
    $_SESSION['pesan_error'] = "Akses ditolak! Anda tidak memiliki izin Admin.";
    header("Location: dashboard.php");
    exit;
}
?>
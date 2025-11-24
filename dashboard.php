<?php
session_start();
include 'koneksi.php';

// CEK KEAMANAN: Jika belum login, tendang balik ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$role = $_SESSION['role'];     // Admin atau Kasir
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-4 shadow-sm">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            <i class="fa-solid fa-shop me-2"></i>RETAIL SYSTEM | <?php echo $role; ?>
        </a>
        
        <div class="ms-auto d-flex align-items-center">
            <span class="text-white me-3">Halo, <b><?php echo $username; ?></b></span>
            <a href="logout.php" class="btn btn-danger btn-sm">
                <i class="fa-solid fa-right-from-bracket"></i> Keluar
            </a>
        </div>
    </nav>

    <div class="container mt-4">
        
        <?php if(isset($_SESSION['pesan'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['pesan']; unset($_SESSION['pesan']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php 
        if ($role == 'Admin') {
            include 'dashboard_admin.php'; // Panggil tampilan Admin
        } else {
            include 'dashboard_kasir.php'; // Panggil tampilan Kasir
        }
        ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
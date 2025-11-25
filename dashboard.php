<?php
session_start();
include 'koneksi.php';

// === PERBAIKAN ZONA WAKTU AGAR SESUAI WAKTU INDONESIA (WIB/Jakarta) ===
date_default_timezone_set('Asia/Jakarta');

// CEK KEAMANAN
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$role = $_SESSION['role'];
$username = $_SESSION['username'];

// Tentukan sapaan berdasarkan jam
$jam = date('H');
if ($jam < 12) { $sapaan = "Selamat Pagi"; }
elseif ($jam < 15) { $sapaan = "Selamat Siang"; }
elseif ($jam < 18) { $sapaan = "Selamat Sore"; }
else { $sapaan = "Selamat Malam"; }
?>

<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Modern</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Sedikit CSS tambahan agar makin cantik */
        .navbar-gradasi {
            background: linear-gradient(90deg, #0d6efd, #0a58ca);
        }
        .card-welcome {
            background: #ffffff;
            border-left: 5px solid #0d6efd; /* Garis biru di kiri */
        }
        /* Agar kartu welcome berubah warna saat dark mode */
        [data-bs-theme="dark"] .card-welcome {
            background: #2b3035;
            color: white;
        }
    </style>
</head>
<body class="bg-body-tertiary"> <nav class="navbar navbar-expand-lg navbar-dark navbar-gradasi shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="fa-solid fa-store me-2"></i>RETAIL SYSTEM <span class="badge bg-white text-primary ms-2 fs-6"><?php echo $role; ?></span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    
                    <li class="nav-item me-3">
                        <button class="btn btn-outline-light btn-sm rounded-circle" id="btnMode" onclick="ubahMode()" title="Ganti Tema">
                            <i class="fa-solid fa-moon"></i>
                        </button>
                    </li>

                    <li class="nav-item me-3 text-white">
                        <div class="d-flex align-items-center">
                            <div class="text-end me-2 d-none d-lg-block">
                                <small class="d-block text-white-50" style="font-size: 0.8rem;">Login sebagai</small>
                                <span class="fw-bold"><?php echo $username; ?></span>
                            </div>
                            <i class="fa-solid fa-circle-user fa-2x"></i>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a href="logout.php" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm">
                            <i class="fa-solid fa-power-off me-1"></i> Keluar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">

        <div class="card card-welcome shadow-sm border-0 rounded-4 mb-4 p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="fw-bold mb-1"><?php echo $sapaan; ?>, <?php echo $username; ?>! 👋</h2>
                    <p class="text-muted mb-0">Selamat datang kembali di sistem kasir. Semoga harimu menyenangkan.</p>
                </div>
                <div class="d-none d-md-block text-primary opacity-25">
                    <i class="fa-solid fa-cash-register fa-4x"></i>
                </div>
            </div>
        </div>

        <?php if(isset($_SESSION['pesan'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> <?php echo $_SESSION['pesan']; unset($_SESSION['pesan']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="fade-in-up">
            <?php 
            if ($role == 'Admin') {
                include 'dashboard_admin.php'; // Panggil file Admin
            } else {
                include 'dashboard_kasir.php'; // Panggil file Kasir
            }
            ?>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Cek apakah user pernah simpan mode sebelumnya di memori browser
        if (localStorage.getItem('tema') === 'dark') {
            document.documentElement.setAttribute('data-bs-theme', 'dark');
            document.getElementById('btnMode').innerHTML = '<i class="fa-solid fa-sun"></i>';
        }

        function ubahMode() {
            let html = document.documentElement;
            let tombol = document.getElementById('btnMode');

            if (html.getAttribute('data-bs-theme') === 'light') {
                html.setAttribute('data-bs-theme', 'dark');
                tombol.innerHTML = '<i class="fa-solid fa-sun"></i>'; // Ikon Matahari
                localStorage.setItem('tema', 'dark'); // Simpan pilihan
            } else {
                html.setAttribute('data-bs-theme', 'light');
                tombol.innerHTML = '<i class="fa-solid fa-moon"></i>'; // Ikon Bulan
                localStorage.setItem('tema', 'light'); // Simpan pilihan
            }
        }
    </script>
</body>
</html>
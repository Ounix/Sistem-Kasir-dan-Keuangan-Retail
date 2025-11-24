<?php
session_start();
include 'koneksi.php';

// Cek jika sudah login, arahkan ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    if ($password !== $konfirmasi_password) {
        $error_message = "Konfirmasi password tidak cocok.";
    } else {
        // Cek jika username sudah ada
        $check_query = "SELECT * FROM user WHERE username='$username'";
        $check_result = mysqli_query($koneksi, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $error_message = "Username sudah digunakan. Silakan pilih yang lain.";
        } else {
            // Role default: Kasir
            $role = 'Kasir'; 
            
            // Simpan ke database (DIASUMSIKAN password belum di-hash)
            $insert_query = "INSERT INTO user (username, password, role) VALUES ('$username', '$password', '$role')";
            
            if (mysqli_query($koneksi, $insert_query)) {
                $success_message = "Registrasi berhasil! Silakan login.";
            } else {
                $error_message = "Registrasi gagal: " . mysqli_error($koneksi);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-info d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-lg">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4 text-info">REGISTRASI PENGGUNA</h2>
                        <?php if ($success_message): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $success_message; ?> <a href="index.php">Login Sekarang</a>
                            </div>
                        <?php endif; ?>
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="register.php">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-4">
                                <label for="konfirmasi_password" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" required>
                            </div>
                            <button type="submit" class="btn btn-info w-100 mb-3 text-white">Daftar</button>
                            <p class="text-center">Sudah punya akun? <a href="index.php">Login di sini</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
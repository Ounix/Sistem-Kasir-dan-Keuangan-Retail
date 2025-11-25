<?php
include 'cek_admin.php';
include 'koneksi.php';

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode_produk = $_POST['kode_produk'];
    $nama_produk = $_POST['nama_produk'];
    $harga_beli = $_POST['harga_beli'];
    $harga_jual = $_POST['harga_jual'];
    $stok = $_POST['stok'];

    $check = mysqli_query($koneksi, "SELECT * FROM produk WHERE kode_produk='$kode_produk'");
    if (mysqli_num_rows($check) > 0) {
        $error_message = "Kode Produk sudah ada. Gunakan kode lain.";
    } else {
        $query = "INSERT INTO produk (kode_produk, nama_produk, harga_beli, harga_jual, stok) VALUES ('$kode_produk', '$nama_produk', $harga_beli, $harga_jual, $stok)";
        
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['pesan'] = "Barang **$nama_produk** berhasil ditambahkan.";
            header("Location: dashboard.php");
            exit;
        } else {
            $error_message = "Gagal menambah barang: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        [data-bs-theme="dark"] .card { background-color: #2b3035 !important; border-color: #495057; }
        [data-bs-theme="dark"] .form-control { background-color: #212529; border-color: #495057; color: #fff; }
        [data-bs-theme="dark"] .form-control:focus { background-color: #212529; color: #fff; }
    </style>
</head>
<body class="bg-body-tertiary">
    <div class="container mt-5">
        <h2 class="mb-4 text-primary">Tambah Barang Baru</h2>
        <a href="dashboard.php" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="card p-4 shadow-sm border-0">
            <form method="POST" action="tambah_barang.php">
                <div class="mb-3">
                    <label for="kode_produk" class="form-label">Kode Produk (SKU)</label>
                    <input type="text" class="form-control" id="kode_produk" name="kode_produk" required>
                </div>
                <div class="mb-3">
                    <label for="nama_produk" class="form-label">Nama Produk</label>
                    <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="harga_beli" class="form-label">Harga Beli (Modal)</label>
                        <input type="number" class="form-control" id="harga_beli" name="harga_beli" required min="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="harga_jual" class="form-label">Harga Jual (Retail)</label>
                        <input type="number" class="form-control" id="harga_jual" name="harga_jual" required min="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="stok" class="form-label">Stok Awal</label>
                    <input type="number" class="form-control" id="stok" name="stok" required min="0">
                </div>
                <button type="submit" class="btn btn-primary w-100">Simpan Barang</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        if (localStorage.getItem('tema') === 'dark') {
            document.documentElement.setAttribute('data-bs-theme', 'dark');
        }
    </script>
</body>
</html>
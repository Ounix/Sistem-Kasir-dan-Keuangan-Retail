<?php
include 'cek_admin.php'; // Menggunakan access control
include 'koneksi.php';

$error_message = '';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['pesan_error'] = "ID barang tidak valid.";
    header("Location: dashboard.php");
    exit;
}

$produk_id = $_GET['id'];
$data = mysqli_query($koneksi, "SELECT * FROM produk WHERE produk_id=$produk_id");
if (mysqli_num_rows($data) == 0) {
    $_SESSION['pesan_error'] = "Data barang tidak ditemukan.";
    header("Location: dashboard.php");
    exit;
}
$produk = mysqli_fetch_assoc($data);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_produk = $_POST['nama_produk'];
    $harga_beli = $_POST['harga_beli'];
    $harga_jual = $_POST['harga_jual'];
    $stok = $_POST['stok'];

    $query = "UPDATE produk SET nama_produk='$nama_produk', harga_beli=$harga_beli, harga_jual=$harga_jual, stok=$stok WHERE produk_id=$produk_id";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['pesan_sukses'] = "Barang **$nama_produk** berhasil diupdate.";
        header("Location: dashboard.php");
        exit;
    } else {
        $error_message = "Gagal mengupdate barang: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-warning">Edit Data Barang</h2>
        <a href="dashboard.php" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="card p-4">
            <form method="POST" action="edit_barang.php?id=<?php echo $produk_id; ?>">
                <div class="mb-3">
                    <label for="kode_produk" class="form-label">Kode Produk (Read-only)</label>
                    <input type="text" class="form-control" value="<?php echo $produk['kode_produk']; ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="nama_produk" class="form-label">Nama Produk</label>
                    <input type="text" class="form-control" id="nama_produk" name="nama_produk" value="<?php echo $produk['nama_produk']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="harga_beli" class="form-label">Harga Beli (Modal)</label>
                    <input type="number" class="form-control" id="harga_beli" name="harga_beli" value="<?php echo $produk['harga_beli']; ?>" required min="0">
                </div>
                <div class="mb-3">
                    <label for="harga_jual" class="form-label">Harga Jual (Retail)</label>
                    <input type="number" class="form-control" id="harga_jual" name="harga_jual" value="<?php echo $produk['harga_jual']; ?>" required min="0">
                </div>
                <div class="mb-3">
                    <label for="stok" class="form-label">Stok Saat Ini</label>
                    <input type="number" class="form-control" id="stok" name="stok" value="<?php echo $produk['stok']; ?>" required min="0">
                </div>
                <button type="submit" class="btn btn-warning text-white">Simpan Perubahan</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
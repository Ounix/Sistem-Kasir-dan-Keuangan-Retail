<?php
session_start();
include 'koneksi.php';

if (!isset($_GET['id']) || !isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$transaksi_id = $_GET['id'];

// Ambil data header transaksi
$header_res = mysqli_query($koneksi, "SELECT t.*, u.username FROM transaksi t JOIN user u ON t.kasir_id = u.user_id WHERE t.transaksi_id = $transaksi_id");
if (mysqli_num_rows($header_res) == 0) {
    $_SESSION['pesan_error'] = "Transaksi tidak ditemukan.";
    header("Location: dashboard.php");
    exit;
}
$header = mysqli_fetch_assoc($header_res);

// Ambil data detail transaksi
$detail_res = mysqli_query($koneksi, "SELECT dt.*, p.nama_produk FROM detail_transaksi dt JOIN produk p ON dt.produk_id = p.produk_id WHERE dt.transaksi_id = $transaksi_id");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi #<?php echo $transaksi_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-info">Detail Transaksi #<?php echo $transaksi_id; ?></h2>
        <a href="dashboard.php" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>

        <div class="card mb-4 p-4">
            <p><strong>Kasir:</strong> <?php echo $header['username']; ?></p>
            <p><strong>Tanggal:</strong> <?php echo date('d M Y H:i:s', strtotime($header['tanggal_transaksi'])); ?></p>
            <p><strong>Total Belanja:</strong> Rp <?php echo number_format($header['total_harga'], 0, ',', '.'); ?></p>
            <p><strong>Uang Bayar:</strong> Rp <?php echo number_format($header['total_bayar'], 0, ',', '.'); ?></p>
            <p class="h4 text-primary"><strong>Kembalian:</strong> Rp <?php echo number_format($header['kembalian'], 0, ',', '.'); ?></p>
        </div>

        <h4 class="mt-4 text-info">Detail Barang Terjual</h4>
        <table class="table table-bordered table-striped">
            <thead class="bg-light">
                <tr>
                    <th>Nama Produk</th>
                    <th>Harga Satuan (Rp)</th>
                    <th>Kuantitas</th>
                    <th>Subtotal (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($detail = mysqli_fetch_assoc($detail_res)): ?>
                <tr>
                    <td><?php echo $detail['nama_produk']; ?></td>
                    <td><?php echo number_format($detail['harga_satuan'], 0, ',', '.'); ?></td>
                    <td><?php echo $detail['jumlah_jual']; ?></td>
                    <td><?php echo number_format($detail['subtotal'], 0, ',', '.'); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
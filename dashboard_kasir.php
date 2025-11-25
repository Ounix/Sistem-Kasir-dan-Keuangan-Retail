<?php
// Pastikan file ini di-include setelah session dan koneksi dibuat di dashboard.php

// Ambil riwayat transaksi yang dilakukan oleh kasir ini
$kasir_id = $_SESSION['user_id'];
$riwayat_transaksi = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE kasir_id = $kasir_id ORDER BY tanggal_transaksi DESC LIMIT 10");
?>

<div class="alert alert-info" role="alert">
    Anda adalah Kasir. Tugas utama Anda adalah melakukan Transaksi Penjualan.
</div>

<h3 class="mb-3 text-primary">Transaksi Penjualan Baru</h3>
<a href="transaksi_penjualan.php" class="btn btn-success btn-lg"><i class="fas fa-cash-register"></i> Mulai Transaksi Sekarang</a>

<h3 class="mt-5 mb-3 text-primary">Riwayat Transaksi</h3>

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="bg-light">
            <tr>
                <th>ID Transaksi</th>
                <th>Tanggal</th>
                <th>Total Harga (Rp)</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($riwayat_transaksi) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($riwayat_transaksi)): ?>
                <tr>
                    <td><?php echo $row['transaksi_id']; ?></td>
                    <td><?php echo date('d M Y, H:i', strtotime($row['tanggal_transaksi'])); ?></td>
                    <td><?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                    <td><span class="badge bg-primary">Selesai</span></td>
                    <td><a href="detail_transaksi.php?id=<?php echo $row['transaksi_id']; ?>" class="btn btn-info btn-sm text-white">Detail</a></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Anda belum memiliki riwayat transaksi.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
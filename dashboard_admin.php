<?php
// HITUNG-HITUNGAN DATA UNTUK LAPORAN
$today = date('Y-m-d');

// 1. Total Barang
$total_barang = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jum FROM produk"))['jum'];

// 2. Transaksi Hari Ini
$transaksi_hari = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jum FROM transaksi WHERE DATE(tanggal_transaksi) = '$today'"))['jum'];

// 3. Pendapatan Hari Ini (Omset)
$pendapatan_hari = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total_harga) AS jum FROM transaksi WHERE DATE(tanggal_transaksi) = '$today'"))['jum'];

// 4. KEUNTUNGAN HARI INI (Profit = Harga Jual - Modal)
// Rumus Query: Jumlahkan ( (Harga Jual - Harga Beli) * Jumlah Barang )
$query_untung = "SELECT SUM( (dt.harga_satuan - p.harga_beli) * dt.jumlah_jual ) AS keuntungan 
                 FROM detail_transaksi dt 
                 JOIN produk p ON dt.produk_id = p.produk_id 
                 JOIN transaksi t ON dt.transaksi_id = t.transaksi_id 
                 WHERE DATE(t.tanggal_transaksi) = '$today'";
$keuntungan_hari = mysqli_fetch_assoc(mysqli_query($koneksi, $query_untung))['keuntungan'];
?>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3 h-100 shadow">
            <div class="card-body">
                <h5 class="card-title">Total Barang</h5>
                <h2 class="fw-bold"><?php echo $total_barang; ?></h2>
                <small>Item tersedia di gudang</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3 h-100 shadow">
            <div class="card-body">
                <h5 class="card-title">Transaksi Hari Ini</h5>
                <h2 class="fw-bold"><?php echo $transaksi_hari; ?></h2>
                <small>Nota diterbitkan</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3 h-100 shadow">
            <div class="card-body">
                <h5 class="card-title">Pendapatan (Omset)</h5>
                <h2 class="fw-bold">Rp <?php echo number_format($pendapatan_hari); ?></h2>
                <small>Total uang masuk</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3 h-100 shadow">
            <div class="card-body">
                <h5 class="card-title">Keuntungan Bersih</h5>
                <h2 class="fw-bold">Rp <?php echo number_format($keuntungan_hari); ?></h2>
                <small>Profit hari ini</small>
            </div>
        </div>
    </div>
</div>

<div class="card shadow">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 text-primary fw-bold"><i class="fa-solid fa-box"></i> Manajemen Data Barang</h5>
        <a href="tambah_barang.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Barang</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Produk</th>
                        <th>Harga Beli (Modal)</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Ambil semua data barang dari database
                    $produk = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY produk_id DESC");
                    
                    if(mysqli_num_rows($produk) > 0):
                        while($p = mysqli_fetch_assoc($produk)):
                    ?>
                    <tr>
                        <td><span class="badge bg-secondary"><?php echo $p['kode_produk']; ?></span></td>
                        <td><?php echo $p['nama_produk']; ?></td>
                        <td>Rp <?php echo number_format($p['harga_beli']); ?></td>
                        <td>Rp <?php echo number_format($p['harga_jual']); ?></td>
                        <td>
                            <?php if($p['stok'] < 10) echo '<span class="text-danger fw-bold">'.$p['stok'].'</span>'; else echo $p['stok']; ?>
                        </td>
                        <td class="text-center">
                            <a href="edit_barang.php?id=<?php echo $p['produk_id']; ?>" class="btn btn-sm btn-warning text-white"><i class="fa-solid fa-pen"></i></a>
                            <a href="hapus_barang.php?id=<?php echo $p['produk_id']; ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Yakin hapus? Riwayat transaksi barang ini juga akan hilang.')">
                               <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data barang. Silakan tambah dulu.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
session_start();
include 'koneksi.php';

// PENGENDALIAN AKSES
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $produk_id   = $_POST['produk_id'];
    $jumlah_jual = (int)$_POST['jumlah_jual']; 
    $harga_jual  = (float)$_POST['harga_jual_hidden']; 
    $total_bayar = (float)$_POST['total_bayar'];
    
    $total_harga = $jumlah_jual * $harga_jual;
    $kembalian   = $total_bayar - $total_harga;

    // Cek Stok Database (HARUS DILAKUKAN SEBELUM INSERT)
    $produk_res_cek = mysqli_query($koneksi, "SELECT stok FROM produk WHERE produk_id='$produk_id'");
    $data_stok      = mysqli_fetch_assoc($produk_res_cek);
    $stok_saat_ini  = $data_stok['stok'];
    
    // Validasi
    if (empty($produk_id)) {
        $error_message = "Silakan pilih barang terlebih dahulu.";
    } elseif ($jumlah_jual <= 0) {
        $error_message = "Kuantitas jual harus lebih dari 0.";
    } elseif ($stok_saat_ini < $jumlah_jual) {
        $error_message = "Stok tidak cukup! Tersedia: " . $stok_saat_ini;
    } elseif ($total_bayar < $total_harga) {
        $error_message = "Uang pembayaran kurang! Total belanja: " . number_format($total_harga);
    } else {
        // Simpan Header Transaksi
        $kasir_id = $_SESSION['user_id'];
        $tanggal  = date('Y-m-d H:i:s');

        $query_transaksi = "INSERT INTO transaksi (tanggal_transaksi, kasir_id, total_harga, total_bayar, kembalian) 
                            VALUES ('$tanggal', '$kasir_id', '$total_harga', '$total_bayar', '$kembalian')";
        
        if (mysqli_query($koneksi, $query_transaksi)) {
            $transaksi_id = mysqli_insert_id($koneksi); 

            // Simpan Detail Transaksi
            $query_detail = "INSERT INTO detail_transaksi (transaksi_id, produk_id, jumlah_jual, harga_satuan, subtotal)
                             VALUES ('$transaksi_id', '$produk_id', '$jumlah_jual', '$harga_jual', '$total_harga')";
            
            if(mysqli_query($koneksi, $query_detail)) {
                // Update Stok
                $query_update_stok = "UPDATE produk SET stok = stok - $jumlah_jual WHERE produk_id = '$produk_id'";
                mysqli_query($koneksi, $query_update_stok);
                
                // PEMBERITAHUAN SUKSES
                $success_message = "Transaksi Berhasil! Kembalian: Rp " . number_format($kembalian, 0, ',', '.');
            } else {
                $error_db = mysqli_error($koneksi);
                $error_message = "Gagal menyimpan detail transaksi: $error_db";
                // Hapus Transaksi Header jika detail gagal
                mysqli_query($koneksi, "DELETE FROM transaksi WHERE transaksi_id = '$transaksi_id'"); 
            }
        } else {
            $error_message = "Gagal menyimpan transaksi: " . mysqli_error($koneksi);
        }
    }
}

// === PERBAIKAN STOK: Panggil query ini DI AKHIR agar selalu memuat data terbaru ===
// Jika ada transaksi yang baru berhasil, $daftar_produk akan mengambil stok yang sudah diperbarui.
$daftar_produk = mysqli_query($koneksi, "SELECT produk_id, nama_produk, harga_jual, stok FROM produk WHERE stok > 0 ORDER BY nama_produk ASC");

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        [data-bs-theme="dark"] .card {
            background-color: #2b3035 !important;
            border-color: #495057;
        }
        [data-bs-theme="dark"] .form-control, [data-bs-theme="dark"] .form-select {
            background-color: #212529;
            border-color: #495057;
            color: #fff;
        }
        [data-bs-theme="dark"] .form-control:focus, [data-bs-theme="dark"] .form-select:focus {
            background-color: #212529;
            color: #fff;
        }
    </style>
</head>
<body class="bg-body-tertiary">
    <div class="container mt-5">
        <h2 class="mb-4 text-success fw-bold">Form Transaksi Penjualan</h2>
        <a href="dashboard.php" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success fw-bold"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-danger fw-bold"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="card p-4 shadow border-0">
            <h5 class="card-title text-success mb-4 border-bottom pb-2">Input Barang dan Pembayaran</h5>
            
            <form method="POST" action="transaksi_penjualan.php">
                <div class="mb-3">
                    <label for="produk_id" class="form-label fw-bold">Pilih Barang yang Dijual</label>
                    <select class="form-select" id="produk_id" name="produk_id" required>
                        <option value="">-- Pilih Barang --</option>
                        <?php while ($p = mysqli_fetch_assoc($daftar_produk)): ?>
                            <option value="<?php echo $p['produk_id']; ?>" data-harga="<?php echo $p['harga_jual']; ?>" data-stok="<?php echo $p['stok']; ?>">
                                <?php echo $p['nama_produk']; ?> (Stok: <?php echo $p['stok']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="alert alert-info py-2" id="detail_info">Harga: Rp 0 | Stok Tersedia: 0</div>
                <input type="hidden" id="harga_jual_hidden" name="harga_jual_hidden" value="0">

                <div class="mb-3">
                    <label for="jumlah_jual" class="form-label fw-bold">Kuantitas Jual</label>
                    <input type="number" class="form-control" id="jumlah_jual" name="jumlah_jual" required min="1" value="1">
                    <div class="form-text text-danger fw-bold" id="stok_error" style="display:none;">Kuantitas melebihi stok yang tersedia!</div>
                </div>

                <div class="mb-4 p-3 bg-light rounded text-end" id="totalBox">
                    <h4 class="mb-0 text-muted">Total Harga:</h4>
                    <h2 class="text-primary fw-bold" id="total_harga_display">Rp 0</h2>
                </div>

                <div class="mb-4">
                    <label for="total_bayar" class="form-label fw-bold">Uang Pembayaran (Rp)</label>
                    <input type="number" class="form-control form-control-lg" id="total_bayar" name="total_bayar" required min="0" placeholder="Masukkan jumlah uang">
                    <div class="mt-2 text-success fw-bold h5" id="kembalian_display">Kembalian: Rp 0</div>
                </div>
                
                <button type="submit" class="btn btn-success w-100 py-3 fw-bold">KONFIRMASI PENJUALAN</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // 1. AUTO DARK MODE
        if (localStorage.getItem('tema') === 'dark') {
            document.documentElement.setAttribute('data-bs-theme', 'dark');
            // Fix khusus untuk kotak Total agar tidak terlalu terang di mode gelap
            document.getElementById('totalBox').classList.remove('bg-light');
            document.getElementById('totalBox').style.backgroundColor = '#343a40';
        }

        // 2. LOGIKA HITUNG HARGA
        document.addEventListener('DOMContentLoaded', function () {
            const produkId = document.getElementById('produk_id');
            const jumlahJual = document.getElementById('jumlah_jual');
            const totalBayar = document.getElementById('total_bayar');
            const detailInfo = document.getElementById('detail_info');
            const hargaJualHidden = document.getElementById('harga_jual_hidden');
            const totalHargaDisplay = document.getElementById('total_harga_display');
            const kembalianDisplay = document.getElementById('kembalian_display');
            const stokError = document.getElementById('stok_error');

            function updateKalkulasi() {
                const selectedOption = produkId.options[produkId.selectedIndex];
                const harga = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
                const stok = parseInt(selectedOption.getAttribute('data-stok')) || 0;
                const kuantitas = parseInt(jumlahJual.value) || 0;
                const bayar = parseFloat(totalBayar.value) || 0;

                detailInfo.innerHTML = `Harga: Rp ${formatRupiah(harga)} | Stok Tersedia: ${stok}`;
                hargaJualHidden.value = harga;

                const totalHarga = harga * kuantitas;
                totalHargaDisplay.textContent = `Rp ${formatRupiah(totalHarga)}`;

                let kembalian = 0;
                if(bayar >= totalHarga) {
                    kembalian = bayar - totalHarga;
                    kembalianDisplay.classList.remove('text-danger');
                    kembalianDisplay.classList.add('text-success');
                    kembalianDisplay.textContent = `Kembalian: Rp ${formatRupiah(kembalian)}`;
                } else {
                    kembalianDisplay.classList.remove('text-success');
                    kembalianDisplay.classList.add('text-danger');
                    kembalianDisplay.textContent = `Uang Kurang: Rp ${formatRupiah(totalHarga - bayar)}`;
                }

                // Cek Stok di sisi klien
                if (kuantitas > stok) {
                    stokError.style.display = 'block';
                    document.querySelector('button[type="submit"]').disabled = true; 
                } else {
                    stokError.style.display = 'none';
                    document.querySelector('button[type="submit"]').disabled = false;
                }
            }

            function formatRupiah(angka) {
                if (isNaN(angka)) return '0';
                return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            produkId.addEventListener('change', updateKalkulasi);
            jumlahJual.addEventListener('input', updateKalkulasi);
            totalBayar.addEventListener('input', updateKalkulasi);
            
            // Panggil updateKalkulasi saat halaman dimuat untuk inisialisasi
            updateKalkulasi(); 
        });
    </script>
</body>
</html>
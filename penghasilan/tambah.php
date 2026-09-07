<?php
include "../config/auth.php";
include "../config/database.php";

$cek = mysqli_query($conn, "SHOW COLUMNS FROM penghasilan LIKE 'barang_id'");
if ($cek && mysqli_num_rows($cek) === 0) mysqli_query($conn, "ALTER TABLE penghasilan ADD COLUMN barang_id INT NULL AFTER id");

if (isset($_POST['simpan'])) {
    $tanggal = date('Y-m-d');
    $barang_id = (int)($_POST['barang_id'] ?? 0);
    $jumlah = max(1, (int)($_POST['jumlah'] ?? 1));
    $harga = (float)($_POST['harga'] ?? 0);

    if ($barang_id > 0) {
        $stmt = mysqli_prepare($conn, "SELECT nama_barang, harga_jual, stok FROM barang WHERE id=? FOR UPDATE");
        mysqli_stmt_bind_param($stmt,'i',$barang_id); mysqli_stmt_execute($stmt);
        $barang = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); mysqli_stmt_close($stmt);
        if (!$barang) die('Barang tidak ditemukan.');
        if ($harga <= 0) $harga=(float)$barang['harga_jual'];
        if ((int)$barang['stok'] < $jumlah) die('Stok barang tidak mencukupi. Stok tersedia: '.(int)$barang['stok']);
        $nama_transaksi=$barang['nama_barang'];
    } else {
        $nama_transaksi='Pemasukan Lainnya';
    }
    if ($harga <= 0) die('Nominal/harga harus lebih dari Rp0.');
    $total=$jumlah*$harga;

    mysqli_begin_transaction($conn);
    try {
        $stmt=mysqli_prepare($conn,"INSERT INTO penghasilan (barang_id,tanggal,nama_transaksi,jumlah,harga,total) VALUES (?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt,'issidd',$barang_id,$tanggal,$nama_transaksi,$jumlah,$harga,$total);
        mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt);
        if ($barang_id>0) {
            $stmt=mysqli_prepare($conn,"UPDATE barang SET stok=stok-? WHERE id=?");
            mysqli_stmt_bind_param($stmt,'ii',$jumlah,$barang_id); mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt);
        }
        mysqli_commit($conn);
    } catch(Throwable $e) { mysqli_rollback($conn); die('Gagal menyimpan pemasukan: '.$e->getMessage()); }
    header('Location: index.php'); exit;
}
$barang_q=mysqli_query($conn,"SELECT id,nama_barang,harga_jual,stok,satuan FROM barang ORDER BY nama_barang ASC");
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Tambah Penghasilan - Toko Income</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"><link rel="stylesheet" href="../assets/css/style.css"></head>
<body><div class="sidebar">
<div class="brand"><i class="bi bi-shop"></i> TOKO INCOME</div>
<div class="menu-title">Menu Utama</div>
<a href="../dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
<a href="../penghasilan/index.php" class="active"><i class="bi bi-cash-stack me-2"></i> Penghasilan</a>
<a href="../pengeluaran/index.php"><i class="bi bi-wallet2 me-2"></i> Pengeluaran</a>
<a href="../barang/index.php"><i class="bi bi-box-seam me-2"></i> Barang</a>
<a href="../barang/stok.php"><i class="bi bi-clipboard-check me-2"></i> Pengecekan Stok</a>

<div class="menu-title">Laporan</div>
<a href="../laporan/harian.php"><i class="bi bi-calendar-day me-2"></i> Laporan Harian</a>
<a href="../laporan/bulanan.php"><i class="bi bi-calendar-month me-2"></i> Laporan Bulanan</a>
<a href="../laporan/tahunan.php"><i class="bi bi-calendar3 me-2"></i> Laporan Tahunan</a>
<div class="menu-title">Sistem</div>
<a href="../pengaturan/index.php"><i class="bi bi-gear me-2"></i> Pengaturan</a>
</div><div class="main-content"><div class="top-navbar d-flex justify-content-between"><div><h5 class="mb-0 fw-bold">Tambah Penghasilan</h5><small class="text-muted">Catat penjualan barang atau pemasukan lainnya</small></div><span class="text-muted"><i class="bi bi-person-circle"></i> Admin</span></div><div class="container-fluid py-4"><div class="card border-0 shadow-sm"><div class="card-body p-4"><form method="post"><div class="mb-3"><label class="form-label fw-semibold">Barang</label><select name="barang_id" id="barang_id" class="form-select"><option value="0">Pemasukan lainnya</option><?php while($b=mysqli_fetch_assoc($barang_q)): ?><option value="<?= (int)$b['id'] ?>" data-harga="<?= htmlspecialchars($b['harga_jual']) ?>" data-stok="<?= (int)$b['stok'] ?>"><?= htmlspecialchars($b['nama_barang']) ?> — stok <?= (int)$b['stok'].' '.htmlspecialchars($b['satuan']) ?></option><?php endwhile; ?></select><div class="form-text">Pilih barang jika transaksi merupakan penjualan barang.</div></div><div class="row g-3"><div class="col-md-6"><label class="form-label fw-semibold">Jumlah Terjual</label><input type="number" name="jumlah" id="jumlah" class="form-control" value="1" min="1" required></div><div class="col-md-6"><label class="form-label fw-semibold">Harga Satuan</label><input type="number" name="harga" id="harga" class="form-control" min="1" step="0.01" required></div></div><div class="alert alert-light border mt-3">Total: <b id="totalPreview">Rp 0</b></div><div class="d-flex gap-2"><button name="simpan" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button><a href="index.php" class="btn btn-secondary">Batal</a></div></form></div></div></div></div>
<script>const s=document.getElementById('barang_id'),h=document.getElementById('harga'),q=document.getElementById('jumlah'),t=document.getElementById('totalPreview');function calc(){t.textContent='Rp '+((parseFloat(h.value)||0)*(parseInt(q.value)||0)).toLocaleString('id-ID')}s.addEventListener('change',()=>{const o=s.options[s.selectedIndex]; if(s.value!=='0') h.value=o.dataset.harga||0; calc()});h.addEventListener('input',calc);q.addEventListener('input',calc);calc();</script></body></html>

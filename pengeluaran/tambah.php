<?php
include "../config/auth.php"; include "../config/database.php";
mysqli_query($conn,"CREATE TABLE IF NOT EXISTS pengeluaran (id INT AUTO_INCREMENT PRIMARY KEY,tanggal DATE NOT NULL,nama_pengeluaran VARCHAR(255) NOT NULL,jumlah DECIMAL(15,2) NOT NULL DEFAULT 0,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
if(isset($_POST['simpan'])){ $tanggal=$_POST['tanggal']??date('Y-m-d'); $nama=trim($_POST['nama_pengeluaran']??''); $jumlah=(float)($_POST['jumlah']??0); if($nama===''||$jumlah<=0){$error='Nama pengeluaran dan nominal wajib diisi.';}else{$st=mysqli_prepare($conn,"INSERT INTO pengeluaran(tanggal,nama_pengeluaran,jumlah) VALUES(?,?,?)");mysqli_stmt_bind_param($st,'ssd',$tanggal,$nama,$jumlah);mysqli_stmt_execute($st);header('Location:index.php');exit;}}
?><!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Tambah Pengeluaran</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>

<div class="sidebar">
    <div class="brand"><i class="bi bi-shop"></i><span>TOKO INCOME</span></div>
    <div class="menu-title">Menu Utama</div>
    <a href="../dashboard.php"><i class="bi bi-speedometer2"></i>Dashboard</a>
    <a href="../penghasilan/index.php"><i class="bi bi-cash-stack"></i>Penghasilan</a>
    <a href="../pengeluaran/index.php"><i class="bi bi-wallet2"></i>Pengeluaran</a>
    <a href="../barang/index.php"><i class="bi bi-box-seam"></i>Barang</a>
    <a href="../barang/stok.php"><i class="bi bi-clipboard-check"></i>Pengecekan Stok</a>
    <div class="menu-title">Laporan</div>
    <a href="../laporan/harian.php" class=""><i class="bi bi-calendar-day"></i>Laporan Harian</a>
    <a href="../laporan/bulanan.php" class=""><i class="bi bi-calendar-month"></i>Laporan Bulanan</a>
    <a href="../laporan/tahunan.php" class=""><i class="bi bi-calendar3"></i>Laporan Tahunan</a>
    <div class="menu-title">Sistem</div>
    <a href="../pengaturan/index.php"><i class="bi bi-gear"></i>Pengaturan</a>
</div>
<div class="main-content">
<div class="container py-5" style="max-width:650px"><h2 class="mb-4">Tambah Pengeluaran</h2><?php if(isset($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?><form method="post"><div class="mb-3"><label class="form-label">Tanggal</label><input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required></div><div class="mb-3"><label class="form-label">Nama Pengeluaran</label><input type="text" name="nama_pengeluaran" class="form-control" placeholder="Contoh: Belanja stok barang" required></div><div class="mb-3"><label class="form-label">Nominal</label><input type="number" name="jumlah" class="form-control" min="1" step="1" required></div><button name="simpan" class="btn btn-primary">Simpan</button> <a href="index.php" class="btn btn-secondary">Batal</a></form></div></div>
<script src="../assets/js/mobile-menu.js"></script>
</body></html>

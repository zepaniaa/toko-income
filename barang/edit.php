<?php
include "../config/auth.php";
include "../config/database.php";
$id=(int)($_GET['id']??0); $q=mysqli_query($conn,"SELECT * FROM barang WHERE id=$id LIMIT 1"); $data=mysqli_fetch_assoc($q); if(!$data) die('Barang tidak ditemukan.');
if(isset($_POST['simpan'])){ $nama=trim($_POST['nama_barang']); $stok=(int)$_POST['stok']; $beli=(float)$_POST['harga_beli']; $jual=(float)$_POST['harga_jual']; $satuan=trim($_POST['satuan']); $stmt=mysqli_prepare($conn,"UPDATE barang SET nama_barang=?,stok=?,harga_beli=?,harga_jual=?,satuan=? WHERE id=?"); mysqli_stmt_bind_param($stmt,'sidssi',$nama,$stok,$beli,$jual,$satuan,$id); mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt); header('Location: index.php'); exit; }
?><!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Edit Barang</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"><link rel="stylesheet" href="../assets/css/style.css"></head><body><div class="sidebar">
<div class="brand"><i class="bi bi-shop"></i> TOKO INCOME</div>
<div class="menu-title">Menu Utama</div>
<a href="../dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
<a href="../penghasilan/index.php"><i class="bi bi-cash-stack me-2"></i> Penghasilan</a>
<a href="../pengeluaran/index.php"><i class="bi bi-wallet2 me-2"></i> Pengeluaran</a>
<a href="../barang/index.php" class="active"><i class="bi bi-box-seam me-2"></i> Barang</a>
<a href="../barang/stok.php"><i class="bi bi-clipboard-check me-2"></i> Pengecekan Stok</a>

<div class="menu-title">Laporan</div>
<a href="../laporan/harian.php"><i class="bi bi-calendar-day me-2"></i> Laporan Harian</a>
<a href="../laporan/bulanan.php"><i class="bi bi-calendar-month me-2"></i> Laporan Bulanan</a>
<a href="../laporan/tahunan.php"><i class="bi bi-calendar3 me-2"></i> Laporan Tahunan</a>
<div class="menu-title">Sistem</div>
<a href="../pengaturan/index.php"><i class="bi bi-gear me-2"></i> Pengaturan</a>
</div><div class="main-content"><div class="top-navbar"><h5 class="mb-0 fw-bold">Edit Barang</h5></div><div class="row justify-content-center"><div class="col-lg-7"><div class="card border-0 shadow-sm"><div class="card-body p-4"><h4 class="fw-bold mb-4">Edit Data Barang</h4><form method="POST"><div class="mb-3"><label class="form-label fw-semibold">Nama Barang</label><input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($data['nama_barang']); ?>" required></div><div class="row"><div class="col-md-6 mb-3"><label class="form-label fw-semibold">Stok</label><input type="number" name="stok" class="form-control" min="0" value="<?= (int)$data['stok']; ?>" required></div><div class="col-md-6 mb-3"><label class="form-label fw-semibold">Satuan</label><input type="text" name="satuan" class="form-control" value="<?= htmlspecialchars($data['satuan']); ?>"></div></div><div class="row"><div class="col-md-6 mb-3"><label class="form-label fw-semibold">Harga Beli</label><input type="number" name="harga_beli" class="form-control" min="0" value="<?= (float)$data['harga_beli']; ?>"></div><div class="col-md-6 mb-3"><label class="form-label fw-semibold">Harga Jual</label><input type="number" name="harga_jual" class="form-control" min="0" value="<?= (float)$data['harga_jual']; ?>"></div></div><div class="d-grid gap-2"><button name="simpan" class="btn btn-primary">Simpan Perubahan</button><a href="index.php" class="btn btn-light">Kembali</a></div></form></div></div></div></div></div></body></html>

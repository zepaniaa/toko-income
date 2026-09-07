<?php
include "../config/auth.php";
include "../config/database.php";

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS pengeluaran (id INT AUTO_INCREMENT PRIMARY KEY, tanggal DATE NOT NULL, nama_pengeluaran VARCHAR(255) NOT NULL, jumlah DECIMAL(15,2) NOT NULL DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
$query = mysqli_query($conn, "SELECT * FROM pengeluaran ORDER BY tanggal DESC, id DESC");
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(jumlah),0) total FROM pengeluaran"))['total'];
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Pengeluaran - Toko Income</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="sidebar">
<div class="brand"><i class="bi bi-shop"></i> TOKO INCOME</div>
<div class="menu-title">Menu Utama</div>
<a href="../dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
<a href="../penghasilan/index.php"><i class="bi bi-cash-stack me-2"></i> Penghasilan</a>
<a href="../pengeluaran/index.php" class="active"><i class="bi bi-wallet2 me-2"></i> Pengeluaran</a>
<a href="../barang/index.php"><i class="bi bi-box-seam me-2"></i> Barang</a>
<a href="../barang/stok.php"><i class="bi bi-clipboard-check me-2"></i> Pengecekan Stok</a>

<div class="menu-title">Laporan</div>
<a href="../laporan/harian.php"><i class="bi bi-calendar-day me-2"></i> Laporan Harian</a>
<a href="../laporan/bulanan.php"><i class="bi bi-calendar-month me-2"></i> Laporan Bulanan</a>
<a href="../laporan/tahunan.php"><i class="bi bi-calendar3 me-2"></i> Laporan Tahunan</a>
<div class="menu-title">Sistem</div>
<a href="../pengaturan/index.php"><i class="bi bi-gear me-2"></i> Pengaturan</a>
</div><div class="main-content"><div class="container-fluid py-4"><div class="d-flex justify-content-between align-items-center mb-4"><div><h2>Pengeluaran</h2><p class="text-muted mb-0">Catat dan cek total uang yang keluar.</p></div><a href="tambah.php" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Tambah Pengeluaran</a></div><div class="card mb-4"><div class="card-body"><small class="text-muted">TOTAL PENGELUARAN</small><h2 class="mb-0">Rp <?= number_format($total,0,',','.') ?></h2></div></div><div class="card"><div class="card-body"><div class="table-responsive"><table class="table table-hover align-middle"><thead><tr><th>Tanggal</th><th>Pengeluaran</th><th>Nominal</th><th>Aksi</th></tr></thead><tbody><?php if(mysqli_num_rows($query)>0): while($row=mysqli_fetch_assoc($query)): ?><tr><td><?= date('d/m/Y',strtotime($row['tanggal'])) ?></td><td><?= htmlspecialchars($row['nama_pengeluaran']) ?></td><td>Rp <?= number_format($row['jumlah'],0,',','.') ?></td><td><?= htmlspecialchars($row['keterangan'] ?? '') ?></td><td><a class="btn btn-sm btn-outline-primary" href="edit.php?id=<?= $row['id'] ?>">Edit</a> <a class="btn btn-sm btn-outline-danger" href="hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus pengeluaran ini?')">Hapus</a></td></tr><?php endwhile; else: ?><tr><td colspan="4" class="text-center text-muted py-4">Belum ada pengeluaran.</td></tr><?php endif; ?></tbody></table></div></div></div></div></div><script src="../assets/js/mobile-menu.js"></script>
</body></html>

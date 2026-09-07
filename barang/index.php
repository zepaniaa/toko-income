<?php
include "../config/auth.php";
include "../config/database.php";

mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS barang (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama_barang VARCHAR(150) NOT NULL,
        stok INT NOT NULL DEFAULT 0,
        harga_beli DECIMAL(15,2) NOT NULL DEFAULT 0,
        harga_jual DECIMAL(15,2) NOT NULL DEFAULT 0,
        satuan VARCHAR(50) NOT NULL DEFAULT 'pcs',
        
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

$query = mysqli_query($conn, "SELECT * FROM barang ORDER BY nama_barang ASC");
function rupiah($angka) { return 'Rp' . number_format((float)$angka, 0, ',', '.'); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Barang - Toko Income</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
<style>.status{font-weight:600}.stok-habis{color:#dc3545}.stok-tipis{color:#fd7e14}.stok-aman{color:#198754}</style>
</head>
<body>
<div class="sidebar">
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
</div><div class="main-content">
<div class="top-navbar d-flex justify-content-between align-items-center"><div><h5 class="mb-0 fw-bold">Barang</h5><small class="text-muted">Kelola barang dan stok toko</small></div><div><span class="text-muted"><i class="bi bi-person-circle"></i> Admin</span></div></div>
<div class="d-flex justify-content-between align-items-center mb-4"><div><h3 class="fw-bold mb-1">Barang Tersedia</h3><p class="text-muted mb-0">Daftar barang dan jumlah stok saat ini.</p></div><a href="tambah.php" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Tambah Barang</a></div>
<div class="card border-0 shadow-sm"><div class="card-body"><div class="table-responsive"><table class="table table-hover align-middle"><thead><tr><th>No</th><th>Nama Barang</th><th>Stok</th><th>Satuan</th><th>Harga Beli</th><th>Harga Jual</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php $no=1; while($row=mysqli_fetch_assoc($query)): ?>
<tr><td><?= $no++; ?></td><td class="fw-semibold"><?= htmlspecialchars($row['nama_barang']); ?></td><td><?= (int)$row['stok']; ?></td><td><?= htmlspecialchars($row['satuan']); ?></td><td><?= rupiah($row['harga_beli']); ?></td><td><?= rupiah($row['harga_jual']); ?></td><td><?php if($row['stok']<=0): ?><span class="status stok-habis"><i class="bi bi-x-circle-fill"></i> Habis</span><?php elseif($row['stok']<=5): ?><span class="status stok-tipis"><i class="bi bi-exclamation-triangle-fill"></i> Menipis</span><?php else: ?><span class="status stok-aman"><i class="bi bi-check-circle-fill"></i> Tersedia</span><?php endif; ?></td><td><a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a> <a href="hapus.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus barang ini?');"><i class="bi bi-trash"></i></a></td></tr>
<?php endwhile; if(mysqli_num_rows($query)===0): ?><tr><td colspan="8" class="text-center py-5"><i class="bi bi-box-seam fs-1 text-muted"></i><p class="text-muted mt-2">Belum ada barang.</p><a href="tambah.php" class="btn btn-primary">Tambah Barang</a></td></tr><?php endif; ?>
</tbody></table></div></div></div>
</div>
<script src="../assets/js/mobile-menu.js"></script>
</body></html>

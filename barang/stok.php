<?php
require_once "../config/auth.php";
require_once "../config/database.php";

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS stok_opname (
    id INT AUTO_INCREMENT PRIMARY KEY,
    barang_id INT NOT NULL,
    stok_sistem INT NOT NULL DEFAULT 0,
    stok_fisik INT NOT NULL DEFAULT 0,
    selisih INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$pesan = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barang_id = (int)($_POST['barang_id'] ?? 0);
    $stok_fisik = (int)($_POST['stok_fisik'] ?? 0);

    if ($barang_id <= 0 || $stok_fisik < 0) {
        $error = 'Data pengecekan stok tidak valid.';
    } else {
        mysqli_begin_transaction($conn);
        try {
            $stmt = mysqli_prepare($conn, "SELECT stok FROM barang WHERE id = ? FOR UPDATE");
            mysqli_stmt_bind_param($stmt, 'i', $barang_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $barang = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if (!$barang) {
                throw new Exception('Barang tidak ditemukan.');
            }

            $stok_sistem = (int)$barang['stok'];
            $selisih = $stok_fisik - $stok_sistem;

            $stmt = mysqli_prepare($conn, "UPDATE barang SET stok = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'ii', $stok_fisik, $barang_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $stmt = mysqli_prepare($conn, "INSERT INTO stok_opname (barang_id, stok_sistem, stok_fisik, selisih) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'iiii', $barang_id, $stok_sistem, $stok_fisik, $selisih);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            mysqli_commit($conn);
            $pesan = 'Pengecekan stok berhasil disimpan. Stok sistem sudah disesuaikan dengan stok fisik.';
        } catch (Throwable $e) {
            mysqli_rollback($conn);
            $error = $e->getMessage();
        }
    }
}

$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? 'semua';

$where = [];
if ($search !== '') {
    $safe = mysqli_real_escape_string($conn, $search);
    $where[] = "nama_barang LIKE '%$safe%'";
}
if ($status === 'habis') {
    $where[] = 'stok <= 0';
} elseif ($status === 'menipis') {
    $where[] = 'stok BETWEEN 1 AND 5';
} elseif ($status === 'aman') {
    $where[] = 'stok > 5';
}

$sql = "SELECT * FROM barang";
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY nama_barang ASC';
$data = mysqli_query($conn, $sql);

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM barang"))['total'] ?? 0;
$habis = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM barang WHERE stok <= 0"))['total'] ?? 0;
$menipis = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM barang WHERE stok BETWEEN 1 AND 5"))['total'] ?? 0;
$aman = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM barang WHERE stok > 5"))['total'] ?? 0;

function rupiah($angka) {
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pengecekan Stok - Toko Income</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.status{font-weight:600}.stok-habis{color:#dc3545}.stok-tipis{color:#fd7e14}.stok-aman{color:#198754}
.stat-card{border:0;border-radius:14px}.stat-number{font-size:28px;font-weight:800}.table input[type=number]{max-width:110px}
</style>
</head>
<body>
<div class="sidebar">
<div class="brand"><i class="bi bi-shop"></i> TOKO INCOME</div>
<div class="menu-title">Menu Utama</div>
<a href="../dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
<a href="../penghasilan/index.php"><i class="bi bi-cash-stack me-2"></i> Penghasilan</a>
<a href="../pengeluaran/index.php"><i class="bi bi-wallet2 me-2"></i> Pengeluaran</a>
<a href="../barang/index.php"><i class="bi bi-box-seam me-2"></i> Barang</a>
<a href="../barang/stok.php" class="active"><i class="bi bi-clipboard-check me-2"></i> Pengecekan Stok</a>

<div class="menu-title">Laporan</div>
<a href="../laporan/harian.php"><i class="bi bi-calendar-day me-2"></i> Laporan Harian</a>
<a href="../laporan/bulanan.php"><i class="bi bi-calendar-month me-2"></i> Laporan Bulanan</a>
<a href="../laporan/tahunan.php"><i class="bi bi-calendar3 me-2"></i> Laporan Tahunan</a>
<div class="menu-title">Sistem</div>
<a href="../pengaturan/index.php"><i class="bi bi-gear me-2"></i> Pengaturan</a>
</div><div class="main-content">
<div class="top-navbar d-flex justify-content-between align-items-center">
<div><h5 class="mb-0 fw-bold">Pengecekan Stok</h5><small class="text-muted">Cocokkan stok fisik dengan stok di sistem</small></div>
<div><span class="text-muted"><i class="bi bi-person-circle"></i> Admin</span></div>
</div>

<div class="container-fluid py-4">
<div class="mb-4"><h3 class="fw-bold mb-1">Pengecekan Stok Barang</h3><p class="text-muted mb-0">Masukkan jumlah barang yang benar-benar ada di toko. Sistem akan menghitung selisih dan menyesuaikan stok.</p></div>

<?php if ($pesan): ?><div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($pesan) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="row g-3 mb-4">
<div class="col-md-3"><div class="card stat-card shadow-sm"><div class="card-body"><small class="text-muted">Total Barang</small><div class="stat-number"><?= (int)$total ?></div></div></div></div>
<div class="col-md-3"><div class="card stat-card shadow-sm"><div class="card-body"><small class="text-danger">Stok Habis</small><div class="stat-number text-danger"><?= (int)$habis ?></div></div></div></div>
<div class="col-md-3"><div class="card stat-card shadow-sm"><div class="card-body"><small class="text-warning">Stok Menipis</small><div class="stat-number text-warning"><?= (int)$menipis ?></div></div></div></div>
<div class="col-md-3"><div class="card stat-card shadow-sm"><div class="card-body"><small class="text-success">Stok Aman</small><div class="stat-number text-success"><?= (int)$aman ?></div></div></div></div>
</div>

<div class="card border-0 shadow-sm">
<div class="card-body">
<form method="get" class="row g-2 mb-3">
<div class="col-md-6"><input type="text" name="search" class="form-control" placeholder="Cari nama barang..." value="<?= htmlspecialchars($search) ?>"></div>
<div class="col-md-3"><select name="status" class="form-select"><option value="semua" <?= $status==='semua'?'selected':'' ?>>Semua Status</option><option value="habis" <?= $status==='habis'?'selected':'' ?>>Habis</option><option value="menipis" <?= $status==='menipis'?'selected':'' ?>>Menipis</option><option value="aman" <?= $status==='aman'?'selected':'' ?>>Aman</option></select></div>
<div class="col-md-3"><button class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> Cek Barang</button></div>
</form>

<div class="alert alert-info py-2"><i class="bi bi-info-circle me-2"></i><b>Cara pakai:</b> lihat stok sistem, hitung barang secara fisik, isi kolom <b>Stok Fisik</b>, lalu klik <b>Simpan Cek</b>.</div>

<div class="table-responsive">
<table class="table table-hover align-middle">
<thead><tr><th>No</th><th>Nama Barang</th><th>Stok Sistem</th><th>Stok Fisik</th><th>Selisih</th><th>Status</th><th>Aksi</th></tr></thead>
<tbody>
<?php $no=1; while($row=mysqli_fetch_assoc($data)): ?>
<tr>
<form method="post">
<td><?= $no++ ?></td>
<td><div class="fw-semibold"><?= htmlspecialchars($row['nama_barang']) ?></div><small class="text-muted"><?= htmlspecialchars($row['satuan']) ?></small><input type="hidden" name="barang_id" value="<?= (int)$row['id'] ?>"></td>
<td><span class="badge text-bg-secondary fs-6"><?= (int)$row['stok'] ?></span></td>
<td><input type="number" name="stok_fisik" min="0" value="<?= (int)$row['stok'] ?>" class="form-control" required></td>
<td class="selisih-cell text-muted">0</td>
<td><?php if($row['stok']<=0): ?><span class="status stok-habis"><i class="bi bi-x-circle-fill"></i> Habis</span><?php elseif($row['stok']<=5): ?><span class="status stok-tipis"><i class="bi bi-exclamation-triangle-fill"></i> Menipis</span><?php else: ?><span class="status stok-aman"><i class="bi bi-check-circle-fill"></i> Aman</span><?php endif; ?></td>
<td><button type="submit" class="btn btn-sm btn-success"><i class="bi bi-check2-circle me-1"></i>Simpan Cek</button></td>
</form>
</tr>
<?php endwhile; if(mysqli_num_rows($data)===0): ?><tr><td colspan="7" class="text-center py-5 text-muted">Belum ada barang yang sesuai.</td></tr><?php endif; ?>
</tbody>
</table>
</div>
</div></div>
</div></div>
<script>
document.querySelectorAll('tr').forEach(function(row){
 const input=row.querySelector('input[name="stok_fisik"]');
 const sistem=row.querySelector('.badge.text-bg-secondary');
 const cell=row.querySelector('.selisih-cell');
 if(input&&sistem&&cell){
  const update=()=>{const s=parseInt(sistem.textContent)||0; const f=parseInt(input.value)||0; const d=f-s; cell.textContent=(d>0?'+':'')+d;};
  input.addEventListener('input',update); update();
 }
});
</script>
</body></html>

<?php

include "../config/auth.php";
include "../config/database.php";


// Ambil semua data penghasilan
$query = mysqli_query($conn, "
    SELECT *
    FROM penghasilan
    ORDER BY tanggal DESC, id DESC
");

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Data Penghasilan - Toko Income
    </title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Bootstrap Icons -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <!-- CSS -->

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>


<body>


<!-- =====================================================
     SIDEBAR
===================================================== -->

<div class="sidebar">
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
</div><div class="main-content">


    <!-- NAVBAR -->

    <div class="top-navbar d-flex justify-content-between align-items-center">


        <div>

            <h5 class="mb-0 fw-bold">

                Data Penghasilan

            </h5>

            <small class="text-muted">

                Kelola seluruh transaksi toko

            </small>

        </div>


        <div>

            <span class="text-muted">

                <i class="bi bi-person-circle"></i>

                Admin

            </span>

        </div>


    </div>


    <!-- =================================================
         HEADER HALAMAN
    ================================================== -->

    <div class="d-flex justify-content-between align-items-center mb-4">


        <div>

            <h3 class="fw-bold mb-1">

                Penghasilan

            </h3>

            <p class="text-muted mb-0">

                Daftar seluruh transaksi penghasilan toko

            </p>

        </div>


        <a
            href="tambah.php"
            class="btn btn-primary"
        >

            <i class="bi bi-plus-circle me-1"></i>

            Tambah Penghasilan

        </a>


    </div>


    <!-- =================================================
         TABEL
    ================================================== -->

    <div class="table-card">


        <div class="table-responsive">


            <table class="table table-hover align-middle">


                <thead class="table-light">

                    <tr>

                        <th>
                            No
                        </th>

                        <th>
                            Tanggal
                        </th>

                        <th>
                            Produk / Transaksi
                        </th>

                        <th class="text-center">
                            Jumlah
                        </th>

                        <th class="text-end">
                            Harga
                        </th>

                        <th class="text-end">
                            Total
                        </th>

                        <th class="text-center">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>
                <?php
                $no = 1;

                if (mysqli_num_rows($query) > 0) {
                    while ($data = mysqli_fetch_assoc($query)) {
                ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= date('d/m/Y', strtotime($data['tanggal'])); ?></td>
                        <td><?= htmlspecialchars($data['nama_transaksi']); ?></td>
                        <td class="text-center"><?= (int)$data['jumlah']; ?></td>
                        <td class="text-end">Rp <?= number_format($data['harga'], 0, ',', '.'); ?></td>
                        <td class="text-end fw-semibold">Rp <?= number_format($data['total'], 0, ',', '.'); ?></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="edit.php?id=<?= $data['id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="hapus.php?id=<?= $data['id']; ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus transaksi ini?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php
                    }
                } else {
                ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada data penghasilan.</p>
                            <a href="tambah.php" class="btn btn-primary mt-3">
                                <i class="bi bi-plus-circle"></i>
                                Tambah Penghasilan
                            </a>
                        </td>
                    </tr>
                <?php } ?>

                </tbody>


            </table>


        </div>


    </div>


</div>


</body>

</html>

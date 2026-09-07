<?php

include "../config/auth.php";
include "../config/database.php";

/*
|--------------------------------------------------------------------------
| PENGATURAN TOKO
|--------------------------------------------------------------------------
*/

$query_pengaturan = mysqli_query(
    $conn,
    "SELECT * FROM pengaturan WHERE id = 1 LIMIT 1"
);

$pengaturan = mysqli_fetch_assoc(
    $query_pengaturan
);

$nama_toko = $pengaturan['nama_toko'] ?? 'Toko Income';
$nama_aplikasi = $pengaturan['nama_aplikasi'] ?? 'Management System';

/*
|--------------------------------------------------------------------------
| TENTUKAN TANGGAL
|--------------------------------------------------------------------------
*/

$tanggal = isset($_GET['tanggal'])
    ? $_GET['tanggal']
    : date('Y-m-d');


/*
|--------------------------------------------------------------------------
| AMBIL DATA TRANSAKSI BERDASARKAN TANGGAL
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare($conn, "
    SELECT *
    FROM penghasilan
    WHERE tanggal = ?
    ORDER BY id DESC
");

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $tanggal
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


/*
|--------------------------------------------------------------------------
| HITUNG TOTAL PENGHASILAN
|--------------------------------------------------------------------------
*/

$stmt_total = mysqli_prepare($conn, "
    SELECT
        COALESCE(SUM(total), 0) AS total_penghasilan,
        COUNT(*) AS jumlah_transaksi
    FROM penghasilan
    WHERE tanggal = ?
");

mysqli_stmt_bind_param(
    $stmt_total,
    "s",
    $tanggal
);

mysqli_stmt_execute($stmt_total);

$result_total = mysqli_stmt_get_result($stmt_total);

$data_total = mysqli_fetch_assoc($result_total);

$total_penghasilan = $data_total['total_penghasilan'];
$jumlah_transaksi = $data_total['jumlah_transaksi'];

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
        Laporan Harian - <?= htmlspecialchars($nama_toko); ?>
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


    <!-- Google Font -->

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >


    

<link rel="stylesheet" href="../assets/css/style.css">
</head>


<body>

<div class="sidebar">
    <div class="brand"><i class="bi bi-shop"></i><span>TOKO INCOME</span></div>
    <div class="menu-title">Menu Utama</div>
    <a href="../dashboard.php"><i class="bi bi-speedometer2"></i>Dashboard</a>
    <a href="../penghasilan/index.php"><i class="bi bi-cash-stack"></i>Penghasilan</a>
    <a href="../pengeluaran/index.php"><i class="bi bi-wallet2"></i>Pengeluaran</a>
    <a href="../barang/index.php"><i class="bi bi-box-seam"></i>Barang</a>
    <a href="../barang/stok.php"><i class="bi bi-clipboard-check"></i>Pengecekan Stok</a>
    <div class="menu-title">Laporan</div>
    <a href="../laporan/harian.php" class="active"><i class="bi bi-calendar-day"></i>Laporan Harian</a>
    <a href="../laporan/bulanan.php" class=""><i class="bi bi-calendar-month"></i>Laporan Bulanan</a>
    <a href="../laporan/tahunan.php" class=""><i class="bi bi-calendar3"></i>Laporan Tahunan</a>
    <div class="menu-title">Sistem</div>
    <a href="../pengaturan/index.php"><i class="bi bi-gear"></i>Pengaturan</a>
</div>
<div class="main-content">



<!-- =====================================================
     MAIN CONTENT
===================================================== -->

<div class="page-container">


    <!-- =================================================
         HEADER
    ================================================== -->

    <div class="page-header">

        <div>

            <h1 class="page-title">

                <span class="page-title-icon">

                    <i class="bi bi-calendar-day"></i>

                </span>

                Laporan Harian

            </h1>


            <p class="page-description">

                Lihat dan pantau penghasilan toko berdasarkan tanggal.

            </p>

        </div>


        <a
            href="../dashboard.php"
            class="btn-dashboard"
        >

            <i class="bi bi-arrow-left"></i>

            Kembali ke Dashboard

        </a>

    </div>



    <!-- =================================================
         FILTER TANGGAL
    ================================================== -->

    <div class="custom-card filter-card">

        <div class="custom-card-body">

            <form
                method="GET"
                class="row g-3 align-items-end"
            >

                <div class="col-md-5">

                    <label class="filter-title">

                        <i class="bi bi-calendar3 me-1"></i>

                        Pilih Tanggal

                    </label>


                    <input
                        type="date"
                        name="tanggal"
                        class="form-control"
                        value="<?= htmlspecialchars($tanggal); ?>"
                        required
                    >

                </div>


                <div class="col-md-auto">

                    <button
                        type="submit"
                        class="btn-main"
                    >

                        <i class="bi bi-search"></i>

                        Tampilkan

                    </button>

                </div>


                <div class="col-md-auto">

                    <button
                        type="button"
                        onclick="window.print()"
                        class="btn-print"
                    >

                        <i class="bi bi-printer"></i>

                        Cetak Laporan

                    </button>

                </div>

            </form>

        </div>

    </div>



    <!-- =================================================
         RINGKASAN
    ================================================== -->

    <div class="row g-4 summary-grid">


        <!-- TOTAL PENGHASILAN -->

        <div class="col-md-6">

            <div class="summary-card">

                <div class="summary-icon">

                    <i class="bi bi-cash-stack"></i>

                </div>


                <div class="summary-label">

                    Total Penghasilan

                </div>


                <h2 class="summary-value">

                    Rp <?= number_format(
                        $total_penghasilan,
                        0,
                        ',',
                        '.'
                    ); ?>

                </h2>


                <div class="summary-subtitle">

                    Penghasilan pada tanggal
                    <?= date('d F Y', strtotime($tanggal)); ?>

                </div>

            </div>

        </div>



        <!-- JUMLAH TRANSAKSI -->

        <div class="col-md-6">

            <div class="summary-card">

                <div class="summary-icon">

                    <i class="bi bi-receipt"></i>

                </div>


                <div class="summary-label">

                    Jumlah Transaksi

                </div>


                <h2 class="summary-value">

                    <?= number_format(
                        $jumlah_transaksi,
                        0,
                        ',',
                        '.'
                    ); ?>

                </h2>


                <div class="summary-subtitle">

                    Total transaksi pada hari tersebut

                </div>

            </div>

        </div>


    </div>



    <!-- =================================================
         DETAIL TRANSAKSI
    ================================================== -->

    <div class="report-card">


        <!-- HEADER LAPORAN -->

        <div class="report-header">

            <div>

                <h3 class="report-title">

                    <i class="bi bi-file-earmark-text me-2"></i>

                    Detail Transaksi

                </h3>


                <p class="report-date">

                    Tanggal:

                    <strong>

                        <?= date(
                            'd F Y',
                            strtotime($tanggal)
                        ); ?>

                    </strong>

                </p>

            </div>


            <div class="report-status">

                Laporan Harian

            </div>

        </div>



        <!-- TABEL -->

        <div class="table-wrapper">

            <table class="custom-table">


                <thead>

                    <tr>

                        <th style="width: 70px;">
                            No
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

                    </tr>

                </thead>



                <tbody>

                <?php

                if (mysqli_num_rows($result) > 0) {

                    $no = 1;

                    while ($data = mysqli_fetch_assoc($result)) {

                ?>

                    <tr>


                        <td>

                            <span class="number-badge">

                                <?= $no++; ?>

                            </span>

                        </td>



                        <td>

                            <span class="transaction-name">

                                <?= htmlspecialchars(
                                    $data['nama_transaksi']
                                ); ?>

                            </span>

                        </td>



                        <td class="text-center">

                            <?= htmlspecialchars(
                                $data['jumlah']
                            ); ?>

                        </td>



                        <td class="text-end price">

                            Rp <?= number_format(
                                $data['harga'],
                                0,
                                ',',
                                '.'
                            ); ?>

                        </td>



                        <td class="text-end total-price">

                            Rp <?= number_format(
                                $data['total'],
                                0,
                                ',',
                                '.'
                            ); ?>

                        </td>


                    </tr>

                <?php

                    }

                } else {

                ?>

                    <tr>

                        <td
                            colspan="5"
                            class="empty-state"
                        >

                            <div class="empty-icon">

                                <i class="bi bi-inbox"></i>

                            </div>


                            <p>

                                Tidak ada transaksi pada tanggal ini.

                            </p>

                        </td>

                    </tr>

                <?php } ?>

                </tbody>



                <?php if ($jumlah_transaksi > 0): ?>

                <tfoot>

                    <tr>

                        <th
                            colspan="4"
                            class="text-end"
                        >

                            TOTAL PENGHASILAN

                        </th>


                        <th
                            class="text-end footer-total"
                        >

                            Rp <?= number_format(
                                $total_penghasilan,
                                0,
                                ',',
                                '.'
                            ); ?>

                        </th>


                        <th></th>

                    </tr>

                </tfoot>

                <?php endif; ?>


            </table>

        </div>

    </div>


</div>



<!-- =====================================================
     BOOTSTRAP JS
===================================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


</div>
<script src="../assets/js/mobile-menu.js"></script>
</body>

</html>

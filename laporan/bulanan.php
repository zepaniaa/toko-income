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
| TENTUKAN BULAN DAN TAHUN
|--------------------------------------------------------------------------
*/

$bulan = isset($_GET['bulan'])
    ? (int) $_GET['bulan']
    : (int) date('m');

$tahun = isset($_GET['tahun'])
    ? (int) $_GET['tahun']
    : (int) date('Y');


/*
|--------------------------------------------------------------------------
| NAMA BULAN
|--------------------------------------------------------------------------
*/

$nama_bulan = [
    1  => 'Januari',
    2  => 'Februari',
    3  => 'Maret',
    4  => 'April',
    5  => 'Mei',
    6  => 'Juni',
    7  => 'Juli',
    8  => 'Agustus',
    9  => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];


/*
|--------------------------------------------------------------------------
| AMBIL PENGHASILAN PER TANGGAL
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare($conn, "
    SELECT
        tanggal,
        SUM(total) AS total
    FROM penghasilan
    WHERE MONTH(tanggal) = ?
    AND YEAR(tanggal) = ?
    GROUP BY tanggal
    ORDER BY tanggal ASC
");

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $bulan,
    $tahun
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


/*
|--------------------------------------------------------------------------
| TOTAL PENGHASILAN BULANAN
|--------------------------------------------------------------------------
*/

$stmt_total = mysqli_prepare($conn, "
    SELECT
        COALESCE(SUM(total), 0) AS total_penghasilan,
        COUNT(*) AS jumlah_transaksi
    FROM penghasilan
    WHERE MONTH(tanggal) = ?
    AND YEAR(tanggal) = ?
");

mysqli_stmt_bind_param(
    $stmt_total,
    "ii",
    $bulan,
    $tahun
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
        Laporan Bulanan - <?= htmlspecialchars($nama_toko); ?>
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
    <a href="../laporan/harian.php" class=""><i class="bi bi-calendar-day"></i>Laporan Harian</a>
    <a href="../laporan/bulanan.php" class="active"><i class="bi bi-calendar-month"></i>Laporan Bulanan</a>
    <a href="../laporan/tahunan.php" class=""><i class="bi bi-calendar3"></i>Laporan Tahunan</a>
    <div class="menu-title">Sistem</div>
    <a href="../pengaturan/index.php"><i class="bi bi-gear"></i>Pengaturan</a>
</div>
<div class="main-content">



<!-- =====================================================
     CONTENT
===================================================== -->

<div class="container-fluid page-container">


    <!-- =================================================
         HEADER
    ================================================== -->

    <div class="page-header">

        <div class="page-title">

            <div class="title-icon">

                <i class="bi bi-calendar3"></i>

            </div>


            <div>

                <h2>
                    Laporan Bulanan
                </h2>

                <p>
                    Rekap penghasilan berdasarkan bulan
                </p>

            </div>

        </div>


        <a
            href="../dashboard.php"
            class="btn-dashboard"
        >

            <i class="bi bi-arrow-left"></i>

            Dashboard

        </a>

    </div>


    <!-- =================================================
         FILTER
    ================================================== -->

    <div class="custom-card filter-card mb-4">

        <div class="card-body-custom">

            <div class="filter-title">

                <i class="bi bi-funnel"></i>

                Filter Laporan

            </div>


            <form
                method="GET"
                class="row g-3 align-items-end"
            >


                <!-- BULAN -->

                <div class="col-md-4">

                    <label class="form-label">
                        Pilih Bulan
                    </label>

                    <select
                        name="bulan"
                        class="form-select"
                    >

                        <?php foreach ($nama_bulan as $nomor => $nama) { ?>

                            <option
                                value="<?= $nomor; ?>"
                                <?= $bulan == $nomor ? 'selected' : ''; ?>
                            >

                                <?= $nama; ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>


                <!-- TAHUN -->

                <div class="col-md-3">

                    <label class="form-label">
                        Pilih Tahun
                    </label>

                    <select
                        name="tahun"
                        class="form-select"
                    >

                        <?php

                        $tahun_sekarang = date('Y');

                        for (
                            $i = $tahun_sekarang - 5;
                            $i <= $tahun_sekarang + 1;
                            $i++
                        ) {

                        ?>

                            <option
                                value="<?= $i; ?>"
                                <?= $tahun == $i ? 'selected' : ''; ?>
                            >

                                <?= $i; ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>


                <!-- BUTTON -->

                <div class="col-md-auto">

                    <button
                        type="submit"
                        class="btn-show"
                    >

                        <i class="bi bi-bar-chart-line me-2"></i>

                        Tampilkan Laporan

                    </button>

                </div>

            </form>

        </div>

    </div>


    <!-- =================================================
         RINGKASAN
    ================================================== -->

    <div class="row g-4 mb-4">


        <!-- TOTAL PENGHASILAN -->

        <div class="col-md-6">

            <div class="summary-card">

                <div class="summary-top">

                    <div>

                        <div class="summary-label">

                            Total Penghasilan

                        </div>

                        <h3 class="summary-value">

                            Rp <?= number_format(
                                $total_penghasilan,
                                0,
                                ',',
                                '.'
                            ); ?>

                        </h3>

                    </div>


                    <div class="summary-icon">

                        <i class="bi bi-cash-stack"></i>

                    </div>

                </div>

            </div>

        </div>


        <!-- JUMLAH TRANSAKSI -->

        <div class="col-md-6">

            <div class="summary-card transaction">

                <div class="summary-top">

                    <div>

                        <div class="summary-label">

                            Jumlah Transaksi

                        </div>

                        <h3 class="summary-value">

                            <?= number_format(
                                $jumlah_transaksi,
                                0,
                                ',',
                                '.'
                            ); ?>

                        </h3>

                    </div>


                    <div class="summary-icon">

                        <i class="bi bi-receipt"></i>

                    </div>

                </div>

            </div>

        </div>


    </div>


    <!-- =================================================
         LAPORAN
    ================================================== -->

    <div class="custom-card">

        <div class="report-header">

            <h5 class="report-title">

                <i class="bi bi-graph-up me-2"></i>

                Laporan Penghasilan
                <?= $nama_bulan[$bulan]; ?>
                <?= $tahun; ?>

            </h5>


            <p class="report-subtitle">

                Rekap penghasilan berdasarkan tanggal

            </p>

        </div>


        <div class="table-wrapper">

            <div class="table-responsive">

                <table class="table custom-table align-middle">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>Tanggal</th>

                            <th class="text-end">
                                Penghasilan
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php

                    $no = 1;

                    if (mysqli_num_rows($result) > 0) {

                        while ($data = mysqli_fetch_assoc($result)) {

                    ?>

                        <tr>

                            <td class="number-cell">

                                <?= $no++; ?>

                            </td>


                            <td>

                                <i class="bi bi-calendar-event me-2 text-secondary"></i>

                                <?= date(
                                    'd-m-Y',
                                    strtotime($data['tanggal'])
                                ); ?>

                            </td>


                            <td class="text-end">

                                <span class="income-value">

                                    Rp <?= number_format(
                                        $data['total'],
                                        0,
                                        ',',
                                        '.'
                                    ); ?>

                                </span>

                            </td>

                        </tr>

                    <?php

                        }

                    } else {

                    ?>

                        <tr>

                            <td
                                colspan="3"
                                class="empty-state"
                            >

                                <i class="bi bi-inbox"></i>

                                Tidak ada transaksi pada bulan ini.

                            </td>

                        </tr>

                    <?php } ?>

                    </tbody>


                    <!-- TOTAL -->

                    <tfoot>

                        <tr>

                            <th
                                colspan="2"
                                class="text-end"
                            >

                                TOTAL PENGHASILAN

                            </th>


                            <th
                                class="text-end total-value"
                            >

                                Rp <?= number_format(
                                    $total_penghasilan,
                                    0,
                                    ',',
                                    '.'
                                ); ?>

                            </th>

                        </tr>

                    </tfoot>

                </table>

            </div>

        </div>

    </div>


</div>


<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


</div>
<script src="../assets/js/mobile-menu.js"></script>
</body>

</html>

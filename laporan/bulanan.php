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


    <style>

        /* =====================================================
           GLOBAL
        ===================================================== */

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;

            font-family: 'Segoe UI', Arial, sans-serif;

            background:
                radial-gradient(
                    circle at top left,
                    #ffffff 0,
                    #f3f4f6 40%,
                    #e5e7eb 100%
                );

            color: #202124;

            min-height: 100vh;
        }


        /* =====================================================
           NAVBAR
        ===================================================== */

        .navbar-custom {
            background: #18191c;

            min-height: 76px;

            box-shadow:
                0 4px 18px rgba(0, 0, 0, 0.18);

            border-bottom: 1px solid #303236;
        }


        .navbar-brand {
            color: #ffffff !important;

            font-size: 21px;

            font-weight: 800;

            letter-spacing: .3px;
        }


        .navbar-brand i {
            color: #d1d5db;
        }


        .user-button {
            background: #242529 !important;

            color: #ffffff !important;

            border: 1px solid #3a3b40 !important;

            border-radius: 10px;

            padding: 9px 15px;

            font-weight: 600;

            transition: .2s ease;
        }


        .user-button:hover {
            background: #303136 !important;

            border-color: #52545a !important;

            transform: translateY(-1px);
        }


        .dropdown-menu {
            border: 1px solid #e5e7eb;

            border-radius: 12px;

            padding: 7px;

            box-shadow:
                0 12px 30px rgba(0, 0, 0, .12);
        }


        .dropdown-item {
            border-radius: 8px;

            padding: 10px 12px;
        }


        /* =====================================================
           CONTENT
        ===================================================== */

        .page-container {
            padding: 35px 4%;
        }


        /* =====================================================
           PAGE HEADER
        ===================================================== */

        .page-header {
            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;

            margin-bottom: 28px;
        }


        .page-title {
            display: flex;

            align-items: center;

            gap: 14px;
        }


        .title-icon {
            width: 50px;
            height: 50px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 14px;

            background: #25262a;

            color: #ffffff;

            font-size: 23px;

            box-shadow:
                0 8px 20px rgba(0, 0, 0, .15);
        }


        .page-title h2 {
            margin: 0;

            font-size: 30px;

            font-weight: 800;

            color: #18181b;
        }


        .page-title p {
            margin: 5px 0 0;

            color: #71717a;

            font-size: 14px;
        }


        /* =====================================================
           DASHBOARD BUTTON
        ===================================================== */

        .btn-dashboard {
            display: inline-flex;

            align-items: center;

            gap: 8px;

            background: #ffffff;

            color: #27272a;

            border: 1px solid #d4d4d8;

            border-radius: 10px;

            padding: 11px 16px;

            font-weight: 600;

            text-decoration: none;

            transition: .2s ease;

            box-shadow:
                0 3px 10px rgba(0,0,0,.05);
        }


        .btn-dashboard:hover {
            background: #27272a;

            color: #ffffff;

            border-color: #27272a;

            transform: translateY(-1px);
        }


        /* =====================================================
           CARD
        ===================================================== */

        .custom-card {
            background: rgba(255, 255, 255, .92);

            border: 1px solid #dedee2;

            border-radius: 18px;

            box-shadow:
                0 8px 25px rgba(0,0,0,.06);

            overflow: hidden;

            transition: .2s ease;
        }


        .custom-card:hover {
            box-shadow:
                0 12px 30px rgba(0,0,0,.09);
        }


        .card-body-custom {
            padding: 25px;
        }


        /* =====================================================
           FILTER
        ===================================================== */

        .filter-title {
            display: flex;

            align-items: center;

            gap: 10px;

            font-size: 16px;

            font-weight: 700;

            color: #27272a;

            margin-bottom: 20px;
        }


        .filter-title i {
            color: #52525b;

            font-size: 18px;
        }


        .form-label {
            color: #3f3f46;

            font-size: 13px;

            font-weight: 700;
        }


        .form-select {
            min-height: 46px;

            border: 1px solid #d4d4d8;

            border-radius: 10px;

            color: #27272a;

            background-color: #ffffff;

            box-shadow: none;

            transition: .2s ease;
        }


        .form-select:focus {
            border-color: #52525b;

            box-shadow:
                0 0 0 3px rgba(82,82,91,.12);
        }


        /* =====================================================
           BUTTON TAMPILKAN
        ===================================================== */

        .btn-show {
            min-height: 46px;

            background: #27272a;

            color: #ffffff;

            border: 1px solid #27272a;

            border-radius: 10px;

            padding: 10px 20px;

            font-weight: 700;

            transition: .2s ease;
        }


        .btn-show:hover {
            background: #09090b;

            border-color: #09090b;

            color: #ffffff;

            transform: translateY(-1px);

            box-shadow:
                0 6px 15px rgba(0,0,0,.15);
        }


        /* =====================================================
           SUMMARY CARDS
        ===================================================== */

        .summary-card {
            position: relative;

            background: #ffffff;

            border: 1px solid #dedee2;

            border-radius: 18px;

            padding: 24px;

            height: 100%;

            overflow: hidden;

            box-shadow:
                0 7px 22px rgba(0,0,0,.06);

            transition: .25s ease;
        }


        .summary-card:hover {
            transform: translateY(-3px);

            box-shadow:
                0 12px 28px rgba(0,0,0,.1);
        }


        .summary-card::before {
            content: "";

            position: absolute;

            top: 0;
            left: 0;

            width: 100%;
            height: 4px;

            background: #27272a;
        }


        .summary-card.transaction::before {
            background: #71717a;
        }


        .summary-top {
            display: flex;

            justify-content: space-between;

            align-items: flex-start;

            gap: 15px;
        }


        .summary-label {
            color: #71717a;

            font-size: 14px;

            font-weight: 600;

            margin-bottom: 7px;
        }


        .summary-value {
            color: #18181b;

            font-size: 27px;

            font-weight: 800;

            margin: 0;
        }


        .summary-icon {
            width: 48px;
            height: 48px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 13px;

            background: #f1f1f3;

            color: #27272a;

            font-size: 22px;
        }


        /* =====================================================
           REPORT CARD
        ===================================================== */

        .report-header {
            padding: 25px 25px 10px;
        }


        .report-title {
            margin: 0;

            color: #18181b;

            font-size: 20px;

            font-weight: 800;
        }


        .report-subtitle {
            color: #71717a;

            font-size: 14px;

            margin-top: 6px;

            margin-bottom: 0;
        }


        /* =====================================================
           TABLE
        ===================================================== */

        .table-wrapper {
            padding: 15px 25px 25px;
        }


        .custom-table {
            margin: 0;

            border-collapse: separate;

            border-spacing: 0;

            overflow: hidden;

            border: 1px solid #e4e4e7;

            border-radius: 12px;
        }


        .custom-table thead th {
            background: #27272a !important;

            color: #ffffff !important;

            border: none !important;

            padding: 15px 16px;

            font-size: 13px;

            font-weight: 700;

            white-space: nowrap;
        }


        .custom-table thead th:first-child {
            border-top-left-radius: 11px;
        }


        .custom-table thead th:last-child {
            border-top-right-radius: 11px;
        }


        .custom-table tbody td {
            padding: 15px 16px;

            color: #3f3f46;

            border-color: #ededf0;

            font-size: 14px;

            background: #ffffff;

            vertical-align: middle;
        }


        .custom-table tbody tr {
            transition: .15s ease;
        }


        .custom-table tbody tr:hover td {
            background: #f4f4f5 !important;
        }


        .custom-table tbody tr:last-child td {
            border-bottom: none;
        }


        .number-cell {
            width: 70px;

            color: #71717a !important;

            font-weight: 700;
        }


        .income-value {
            color: #27272a;

            font-weight: 800;
        }


        /* =====================================================
           TOTAL TABLE
        ===================================================== */

        .custom-table tfoot th {
            background: #f4f4f5 !important;

            color: #27272a !important;

            border-top: 2px solid #d4d4d8 !important;

            padding: 16px;

            font-weight: 800;
        }


        .total-value {
            color: #18181b !important;

            font-size: 15px;
        }


        /* =====================================================
           EMPTY DATA
        ===================================================== */

        .empty-state {
            padding: 45px 20px !important;

            text-align: center;

            color: #71717a !important;
        }


        .empty-state i {
            display: block;

            font-size: 40px;

            color: #a1a1aa;

            margin-bottom: 10px;
        }


        /* =====================================================
           SCROLLBAR
        ===================================================== */

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }


        ::-webkit-scrollbar-track {
            background: #e4e4e7;
        }


        ::-webkit-scrollbar-thumb {
            background: #71717a;

            border-radius: 20px;
        }


        ::-webkit-scrollbar-thumb:hover {
            background: #52525b;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 768px) {

            .page-container {
                padding: 22px 15px;
            }


            .page-header {
                align-items: flex-start;

                flex-direction: column;
            }


            .page-title h2 {
                font-size: 25px;
            }


            .btn-dashboard {
                width: 100%;

                justify-content: center;
            }


            .card-body-custom {
                padding: 20px;
            }


            .report-header {
                padding: 20px 20px 10px;
            }


            .table-wrapper {
                padding: 10px 20px 20px;
            }


            .summary-value {
                font-size: 23px;
            }

        }


        /* =====================================================
           PRINT
        ===================================================== */

        @media print {

            body {
                background: #ffffff;
            }


            .navbar-custom,
            .filter-card,
            .btn-dashboard {
                display: none !important;
            }


            .page-container {
                padding: 0;
            }


            .custom-card,
            .summary-card {
                box-shadow: none;

                border: 1px solid #ccc;
            }


            .custom-table thead th {
                background: #333333 !important;

                color: #ffffff !important;
            }

        }

    </style>

</head>


<body>


<!-- =====================================================
     NAVBAR
===================================================== -->

<nav class="navbar navbar-custom">

    <div class="container-fluid px-4">

        <a
            href="../dashboard.php"
            class="navbar-brand"
        >

            <i class="bi bi-shop"></i>

            <?= htmlspecialchars($nama_toko); ?>

        </a>



        <div class="dropdown">

            <button
                class="btn dropdown-toggle user-button"
                type="button"
                data-bs-toggle="dropdown"
            >

                <i class="bi bi-person-circle me-1"></i>

                <?= htmlspecialchars($_SESSION['nama']); ?>

            </button>


            <ul class="dropdown-menu dropdown-menu-end">

                <li>

                    <a
                        class="dropdown-item text-danger"
                        href="../login/logout.php"
                    >

                        <i class="bi bi-box-arrow-right me-2"></i>

                        Logout

                    </a>

                </li>

            </ul>

        </div>

    </div>

</nav>


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


</body>

</html>

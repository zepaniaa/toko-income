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


    <style>

        /* =====================================================
           GLOBAL
        ===================================================== */

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;

            font-family: 'Inter', sans-serif;

            background:
                radial-gradient(
                    circle at top left,
                    #ffffff 0%,
                    #f1f1f1 45%,
                    #e9e9e9 100%
                );

            color: #222;

            min-height: 100vh;
        }


        /* =====================================================
           NAVBAR
        ===================================================== */

        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);

            border-bottom: 1px solid #dedede;

            box-shadow:
                0 4px 20px rgba(0, 0, 0, 0.06);

            min-height: 76px;
        }


        .navbar-brand {
            display: flex;
            align-items: center;

            gap: 10px;

            color: #202020 !important;

            font-size: 21px;

            font-weight: 800;

            letter-spacing: -0.5px;
        }


        .navbar-brand i {
            width: 40px;
            height: 40px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 11px;

            background: #222;

            color: white;

            font-size: 19px;
        }


        /* =====================================================
           ADMIN BUTTON
        ===================================================== */

        .admin-button {
            border: 1px solid #dedede !important;

            background: #f7f7f7 !important;

            color: #222 !important;

            border-radius: 10px !important;

            padding: 9px 14px !important;

            font-weight: 600;

            box-shadow: none !important;
        }


        .admin-button:hover {
            background: #eeeeee !important;

            border-color: #cfcfcf !important;
        }


        .dropdown-menu {
            border: 1px solid #dedede;

            border-radius: 12px;

            padding: 7px;

            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.12);
        }


        .dropdown-item {
            border-radius: 8px;

            padding: 9px 12px;

            font-size: 14px;
        }


        .dropdown-item:hover {
            background: #f1f1f1;
        }


        /* =====================================================
           CONTENT
        ===================================================== */

        .page-container {
            max-width: 1500px;

            margin: auto;

            padding: 38px 30px 60px;
        }


        /* =====================================================
           PAGE HEADER
        ===================================================== */

        .page-header {
            display: flex;

            justify-content: space-between;

            align-items: flex-end;

            gap: 20px;

            margin-bottom: 28px;
        }


        .page-title {
            display: flex;

            align-items: center;

            gap: 14px;

            margin: 0;

            color: #171717;

            font-size: 32px;

            font-weight: 800;

            letter-spacing: -1px;
        }


        .page-title-icon {
            width: 52px;
            height: 52px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 15px;

            background: #242424;

            color: white;

            font-size: 22px;

            box-shadow:
                0 8px 20px rgba(0, 0, 0, 0.16);
        }


        .page-description {
            margin: 8px 0 0 66px;

            color: #777;

            font-size: 14px;
        }


        /* =====================================================
           BACK BUTTON
        ===================================================== */

        .btn-dashboard {
            display: inline-flex;

            align-items: center;

            gap: 8px;

            background: #242424;

            color: white;

            border: none;

            border-radius: 10px;

            padding: 11px 17px;

            font-size: 13px;

            font-weight: 600;

            transition: all .2s ease;
        }


        .btn-dashboard:hover {
            background: #000;

            color: white;

            transform: translateY(-2px);

            box-shadow:
                0 7px 18px rgba(0, 0, 0, .15);
        }


        /* =====================================================
           CARD
        ===================================================== */

        .custom-card {
            background: rgba(255, 255, 255, .96);

            border: 1px solid #dedede;

            border-radius: 18px;

            box-shadow:
                0 8px 25px rgba(0, 0, 0, .06);

            overflow: hidden;
        }


        .custom-card:hover {
            box-shadow:
                0 12px 32px rgba(0, 0, 0, .08);
        }


        .custom-card-body {
            padding: 25px;
        }


        /* =====================================================
           FILTER
        ===================================================== */

        .filter-card {
            margin-bottom: 24px;
        }


        .filter-title {
            color: #222;

            font-size: 15px;

            font-weight: 700;

            margin-bottom: 9px;
        }


        .form-control,
        .form-select {
            height: 48px;

            border: 1px solid #d5d5d5;

            border-radius: 10px;

            background: #fafafa;

            color: #222;

            font-size: 14px;

            padding: 10px 13px;

            box-shadow: none !important;

            transition: all .2s ease;
        }


        .form-control:focus,
        .form-select:focus {
            border-color: #555;

            background: white;

            box-shadow:
                0 0 0 3px rgba(0, 0, 0, .07) !important;
        }


        /* =====================================================
           BUTTON UTAMA
        ===================================================== */

        .btn-main {
            height: 48px;

            padding: 0 20px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 8px;

            border: none;

            border-radius: 10px;

            background: #242424;

            color: white;

            font-size: 14px;

            font-weight: 600;

            transition: all .2s ease;
        }


        .btn-main:hover {
            background: #000;

            color: white;

            transform: translateY(-2px);

            box-shadow:
                0 7px 18px rgba(0, 0, 0, .16);
        }


        /* =====================================================
           BUTTON CETAK
        ===================================================== */

        .btn-print {
            height: 48px;

            padding: 0 20px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 8px;

            border: 1px solid #cfcfcf;

            border-radius: 10px;

            background: #f7f7f7;

            color: #333;

            font-size: 14px;

            font-weight: 600;

            transition: all .2s ease;
        }


        .btn-print:hover {
            background: #eaeaea;

            border-color: #bdbdbd;

            color: #111;
        }


        /* =====================================================
           SUMMARY
        ===================================================== */

        .summary-grid {
            margin-bottom: 24px;
        }


        .summary-card {
            position: relative;

            height: 100%;

            background: white;

            border: 1px solid #dedede;

            border-radius: 18px;

            padding: 24px;

            overflow: hidden;

            box-shadow:
                0 7px 22px rgba(0, 0, 0, .055);

            transition: all .25s ease;
        }


        .summary-card:hover {
            transform: translateY(-4px);

            box-shadow:
                0 14px 30px rgba(0, 0, 0, .10);
        }


        .summary-card::before {
            content: "";

            position: absolute;

            top: 0;
            left: 0;

            width: 100%;
            height: 4px;

            background: #222;
        }


        .summary-icon {
            width: 52px;
            height: 52px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 14px;

            background: #eeeeee;

            color: #222;

            font-size: 23px;

            margin-bottom: 17px;
        }


        .summary-label {
            color: #777;

            font-size: 13px;

            font-weight: 500;

            margin-bottom: 5px;
        }


        .summary-value {
            color: #1b1b1b;

            font-size: 27px;

            font-weight: 800;

            letter-spacing: -.5px;

            margin: 0;
        }


        .summary-subtitle {
            margin-top: 7px;

            color: #999;

            font-size: 12px;
        }


        /* =====================================================
           REPORT CARD
        ===================================================== */

        .report-card {
            background: white;

            border: 1px solid #dedede;

            border-radius: 18px;

            box-shadow:
                0 8px 25px rgba(0, 0, 0, .06);

            overflow: hidden;
        }


        .report-header {
            padding: 25px 25px 20px;

            border-bottom: 1px solid #e7e7e7;

            display: flex;

            justify-content: space-between;

            align-items: center;
        }


        .report-title {
            margin: 0;

            color: #1c1c1c;

            font-size: 19px;

            font-weight: 800;
        }


        .report-date {
            margin: 6px 0 0;

            color: #777;

            font-size: 13px;
        }


        .report-date strong {
            color: #333;
        }


        .report-status {
            display: inline-flex;

            align-items: center;

            gap: 7px;

            padding: 7px 11px;

            border-radius: 999px;

            background: #eeeeee;

            color: #555;

            font-size: 11px;

            font-weight: 700;
        }


        .report-status::before {
            content: "";

            width: 7px;
            height: 7px;

            border-radius: 50%;

            background: #444;
        }


        /* =====================================================
           TABLE
        ===================================================== */

        .table-wrapper {
            overflow-x: auto;
        }


        .custom-table {
            width: 100%;

            margin: 0;

            border-collapse: collapse;
        }


        .custom-table thead th {
            background: #242424;

            color: white;

            border: none;

            padding: 15px 17px;

            font-size: 12px;

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: .3px;

            white-space: nowrap;
        }


        .custom-table tbody td {
            padding: 16px 17px;

            border-bottom: 1px solid #eeeeee;

            color: #444;

            font-size: 13px;

            vertical-align: middle;
        }


        .custom-table tbody tr {
            transition: background .2s ease;
        }


        .custom-table tbody tr:hover {
            background: #f7f7f7;
        }


        .custom-table tbody tr:last-child td {
            border-bottom: none;
        }


        .transaction-name {
            color: #222;

            font-weight: 700;
        }


        .price {
            color: #333;

            font-weight: 600;
        }


        .total-price {
            color: #111;

            font-weight: 800;
        }


        /* =====================================================
           NUMBER BADGE
        ===================================================== */

        .number-badge {
            width: 30px;
            height: 30px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            border-radius: 9px;

            background: #eeeeee;

            color: #444;

            font-size: 12px;

            font-weight: 700;
        }


        /* =====================================================
           EMPTY STATE
        ===================================================== */

        .empty-state {
            padding: 55px 20px !important;

            text-align: center;
        }


        .empty-icon {
            width: 64px;
            height: 64px;

            margin: auto;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 18px;

            background: #eeeeee;

            color: #777;

            font-size: 28px;
        }


        .empty-state p {
            margin: 13px 0 0;

            color: #888;

            font-size: 13px;
        }


        /* =====================================================
           FOOTER TABLE
        ===================================================== */

        .custom-table tfoot {
            background: #f1f1f1;
        }


        .custom-table tfoot th {
            padding: 17px;

            border-top: 2px solid #d2d2d2;

            color: #222;

            font-size: 13px;
        }


        .footer-total {
            color: #111;

            font-size: 15px;

            font-weight: 800;
        }


        /* =====================================================
           SCROLLBAR
        ===================================================== */

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }


        ::-webkit-scrollbar-track {
            background: #eeeeee;
        }


        ::-webkit-scrollbar-thumb {
            background: #999;

            border-radius: 10px;
        }


        ::-webkit-scrollbar-thumb:hover {
            background: #666;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 768px) {

            .page-container {
                padding: 25px 16px 40px;
            }


            .page-header {
                flex-direction: column;

                align-items: flex-start;
            }


            .page-title {
                font-size: 26px;
            }


            .page-description {
                margin-left: 0;

                margin-top: 8px;
            }


            .btn-dashboard {
                width: 100%;

                justify-content: center;
            }


            .custom-card-body {
                padding: 18px;
            }


            .report-header {
                flex-direction: column;

                align-items: flex-start;

                gap: 12px;
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
                background: white;
            }


            .navbar-custom,
            .filter-card,
            .btn-dashboard,
            .btn-print,
            .report-status {
                display: none !important;
            }


            .page-container {
                padding: 0;
            }


            .custom-card,
            .summary-card,
            .report-card {
                box-shadow: none;

                border: 1px solid #ccc;
            }


            .report-header {
                border-bottom: 1px solid #aaa;
            }


            .custom-table thead th {
                background: #333 !important;

                color: white !important;

                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
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
                class="btn dropdown-toggle admin-button"
                type="button"
                data-bs-toggle="dropdown"
            >

                <i class="bi bi-person-circle me-1"></i>

                <?= htmlspecialchars($_SESSION['nama']); ?>

            </button>


            <ul class="dropdown-menu dropdown-menu-end">

                <li>

                    <a
                        class="dropdown-item"
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

                        <th>
                            Keterangan
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



                        <td>

                            <?= htmlspecialchars(
                                $data['keterangan'] ?? ''
                            ); ?>

                        </td>


                    </tr>

                <?php

                    }

                } else {

                ?>

                    <tr>

                        <td
                            colspan="6"
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


</body>

</html>

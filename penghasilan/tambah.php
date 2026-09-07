<?php

include "../config/auth.php";
include "../config/database.php";


if (isset($_POST['simpan'])) {


    $tanggal = $_POST['tanggal'];

    $nama_transaksi = $_POST['nama_transaksi'];

    $jumlah = (int) $_POST['jumlah'];

    $harga = (float) $_POST['harga'];

    $total = $jumlah * $harga;

    $keterangan = $_POST['keterangan'];


    $stmt = mysqli_prepare($conn, "
        INSERT INTO penghasilan
        (
            tanggal,
            nama_transaksi,
            jumlah,
            harga,
            total,
            keterangan
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ");


    mysqli_stmt_bind_param(
        $stmt,
        "ssidss",
        $tanggal,
        $nama_transaksi,
        $jumlah,
        $harga,
        $total,
        $keterangan
    );


    mysqli_stmt_execute($stmt);


    header("Location: index.php");

    exit;

}

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
        Tambah Penghasilan - Toko Income
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


    <div class="brand">

        <i class="bi bi-shop"></i>

        TOKO INCOME

    </div>


    <div class="menu-title">
        Menu Utama
    </div>


    <a href="../dashboard.php">

        <i class="bi bi-speedometer2 me-2"></i>

        Dashboard

    </a>


    <a
        href="index.php"
        class="active"
    >

        <i class="bi bi-cash-stack me-2"></i>

        Penghasilan

    </a>


    <div class="menu-title">
        Laporan
    </div>


    <a href="../laporan/harian.php">

        <i class="bi bi-calendar-day me-2"></i>

        Laporan Harian

    </a>


    <a href="../laporan/bulanan.php">

        <i class="bi bi-calendar-month me-2"></i>

        Laporan Bulanan

    </a>


    <a href="../laporan/tahunan.php">

        <i class="bi bi-calendar3 me-2"></i>

        Laporan Tahunan

    </a>


    <div class="menu-title">
        Sistem
    </div>


    <a href="#">

        <i class="bi bi-gear me-2"></i>

        Pengaturan

    </a>


</div>


<!-- =====================================================
     MAIN CONTENT
===================================================== -->

<div class="main-content">


    <!-- NAVBAR -->

    <div class="top-navbar d-flex justify-content-between align-items-center">

        <div>

            <h5 class="mb-0 fw-bold">

                Tambah Penghasilan

            </h5>

            <small class="text-muted">

                Masukkan transaksi baru

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
         FORM
    ================================================== -->

    <div class="row justify-content-center">


        <div class="col-lg-8">


            <div class="card border-0 shadow-sm">


                <div class="card-body p-4">


                    <h4 class="fw-bold mb-1">

                        Tambah Penghasilan

                    </h4>


                    <p class="text-muted mb-4">

                        Isi data transaksi di bawah ini.

                    </p>


                    <form method="POST">


                        <!-- TANGGAL -->

                        <div class="mb-3">

                            <label class="form-label fw-semibold">

                                Tanggal

                            </label>

                            <input
                                type="date"
                                name="tanggal"
                                class="form-control"
                                value="<?= date('Y-m-d'); ?>"
                                required
                            >

                        </div>


                        <!-- NAMA TRANSAKSI -->

                        <div class="mb-3">

                            <label class="form-label fw-semibold">

                                Nama Produk / Transaksi

                            </label>

                            <input
                                type="text"
                                name="nama_transaksi"
                                class="form-control"
                                placeholder="Contoh: Kopi Robusta"
                                required
                            >

                        </div>


                        <div class="row">


                            <!-- JUMLAH -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-semibold">

                                    Jumlah

                                </label>

                                <input
                                    type="number"
                                    name="jumlah"
                                    class="form-control"
                                    min="1"
                                    placeholder="Contoh: 10"
                                    required
                                >

                            </div>


                            <!-- HARGA -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-semibold">

                                    Harga Satuan

                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        Rp
                                    </span>

                                    <input
                                        type="number"
                                        name="harga"
                                        class="form-control"
                                        min="0"
                                        placeholder="25000"
                                        required
                                    >

                                </div>

                            </div>


                        </div>


                        <!-- KETERANGAN -->

                        <div class="mb-4">

                            <label class="form-label fw-semibold">

                                Keterangan

                            </label>

                            <textarea
                                name="keterangan"
                                class="form-control"
                                rows="4"
                                placeholder="Keterangan transaksi (opsional)"
                            ></textarea>

                        </div>


                        <!-- TOMBOL -->

                        <div class="d-flex gap-2">


                            <button
                                type="submit"
                                name="simpan"
                                class="btn btn-primary"
                            >

                                <i class="bi bi-save me-1"></i>

                                Simpan

                            </button>


                            <a
                                href="index.php"
                                class="btn btn-secondary"
                            >

                                Batal

                            </a>


                        </div>


                    </form>


                </div>

            </div>


        </div>


    </div>


</div>


</body>

</html>

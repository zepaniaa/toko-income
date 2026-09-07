<?php

include "../config/auth.php";
include "../config/database.php";


/*
|--------------------------------------------------------------------------
| AMBIL ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];


/*
|--------------------------------------------------------------------------
| AMBIL DATA
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare($conn, "
    SELECT *
    FROM penghasilan
    WHERE id = ?
");

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$data = mysqli_fetch_assoc($result);


if (!$data) {
    echo "Data tidak ditemukan.";
    exit;
}


/*
|--------------------------------------------------------------------------
| PROSES UPDATE
|--------------------------------------------------------------------------
*/

if (isset($_POST['update'])) {

    $tanggal = $_POST['tanggal'];
    $nama_transaksi = $_POST['nama_transaksi'];
    $jumlah = (int) $_POST['jumlah'];
    $harga = (float) $_POST['harga'];
    $keterangan = $_POST['keterangan'];

    // Total dihitung otomatis
    $total = $jumlah * $harga;


    $stmt_update = mysqli_prepare($conn, "
        UPDATE penghasilan
        SET
            tanggal = ?,
            nama_transaksi = ?,
            jumlah = ?,
            harga = ?,
            total = ?,
            keterangan = ?
        WHERE id = ?
    ");


    mysqli_stmt_bind_param(
        $stmt_update,
        "ssidssi",
        $tanggal,
        $nama_transaksi,
        $jumlah,
        $harga,
        $total,
        $keterangan,
        $id
    );


    mysqli_stmt_execute($stmt_update);


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
        Edit Penghasilan - Toko Income
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

                Edit Penghasilan

            </h5>

            <small class="text-muted">

                Mengubah data transaksi

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

                        Edit Penghasilan

                    </h4>


                    <p class="text-muted mb-4">

                        Ubah informasi transaksi kemudian simpan.

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
                                value="<?= htmlspecialchars($data['tanggal']); ?>"
                                required
                            >

                        </div>


                        <!-- PRODUK -->

                        <div class="mb-3">

                            <label class="form-label fw-semibold">

                                Nama Produk / Transaksi

                            </label>

                            <input
                                type="text"
                                name="nama_transaksi"
                                class="form-control"
                                value="<?= htmlspecialchars($data['nama_transaksi']); ?>"
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
                                    value="<?= $data['jumlah']; ?>"
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
                                        value="<?= $data['harga']; ?>"
                                        required
                                    >

                                </div>

                            </div>


                        </div>


                        <!-- TOTAL -->

                        <div class="alert alert-info">

                            <strong>
                                Total saat ini:
                            </strong>

                            Rp <?= number_format(
                                $data['total'],
                                0,
                                ',',
                                '.'
                            ); ?>

                            <br>

                            <small>
                                Total akan dihitung ulang berdasarkan jumlah × harga.
                            </small>

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
                            ><?= htmlspecialchars($data['keterangan'] ?? ''); ?></textarea>

                        </div>


                        <!-- BUTTON -->

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                name="update"
                                class="btn btn-primary"
                            >

                                <i class="bi bi-save me-1"></i>

                                Simpan Perubahan

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

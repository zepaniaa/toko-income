<?php

include "../config/auth.php";
include "../config/database.php";


/*
|--------------------------------------------------------------------------
| BUAT TABEL PENGATURAN JIKA BELUM ADA
|--------------------------------------------------------------------------
*/

mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS pengaturan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama_toko VARCHAR(150) NOT NULL DEFAULT 'Toko Income',
        nama_aplikasi VARCHAR(150) NOT NULL DEFAULT 'Toko Income Management System',
        alamat TEXT NULL,
        telepon VARCHAR(30) NULL,
        email VARCHAR(150) NULL
    )
");


/*
|--------------------------------------------------------------------------
| BUAT DATA DEFAULT JIKA BELUM ADA
|--------------------------------------------------------------------------
*/

$cek_pengaturan = mysqli_query(
    $conn,
    "SELECT id FROM pengaturan LIMIT 1"
);

if (mysqli_num_rows($cek_pengaturan) == 0) {

    mysqli_query($conn, "
        INSERT INTO pengaturan
        (
            nama_toko,
            nama_aplikasi,
            alamat,
            telepon,
            email
        )
        VALUES
        (
            'Toko Income',
            'Toko Income Management System',
            '',
            '',
            ''
        )
    ");
}


/*
|--------------------------------------------------------------------------
| SIMPAN PENGATURAN
|--------------------------------------------------------------------------
*/

$pesan = "";
$tipe_pesan = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama_toko = trim($_POST['nama_toko'] ?? '');
    $nama_aplikasi = trim($_POST['nama_aplikasi'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $email = trim($_POST['email'] ?? '');


    /*
    |--------------------------------------------------------------------------
    | VALIDASI
    |--------------------------------------------------------------------------
    */

    if ($nama_toko === '') {

        $pesan = "Nama toko wajib diisi.";
        $tipe_pesan = "danger";

    } elseif ($nama_aplikasi === '') {

        $pesan = "Nama aplikasi wajib diisi.";
        $tipe_pesan = "danger";

    } else {


        /*
        |--------------------------------------------------------------------------
        | UPDATE DATABASE
        |--------------------------------------------------------------------------
        */

        $stmt = mysqli_prepare($conn, "
            UPDATE pengaturan
            SET
                nama_toko = ?,
                nama_aplikasi = ?,
                alamat = ?,
                telepon = ?,
                email = ?
            WHERE id = 1
        ");


        mysqli_stmt_bind_param(
            $stmt,
            "sssss",
            $nama_toko,
            $nama_aplikasi,
            $alamat,
            $telepon,
            $email
        );


        if (mysqli_stmt_execute($stmt)) {

            $pesan = "Pengaturan berhasil disimpan.";
            $tipe_pesan = "success";

        } else {

            $pesan = "Gagal menyimpan pengaturan.";
            $tipe_pesan = "danger";

        }


        mysqli_stmt_close($stmt);

    }

}


/*
|--------------------------------------------------------------------------
| AMBIL DATA PENGATURAN
|--------------------------------------------------------------------------
*/

$query_pengaturan = mysqli_query(
    $conn,
    "SELECT * FROM pengaturan WHERE id = 1 LIMIT 1"
);

$pengaturan = mysqli_fetch_assoc(
    $query_pengaturan
);


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
        Pengaturan - <?= htmlspecialchars(
            $pengaturan['nama_toko']
        ); ?>
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


    <!-- Custom CSS -->

    <link
        rel="stylesheet"
        href="../assets/css/style.css?v=2"
    >


    <style>

        .setting-card {

            background: #ffffff;

            border-radius: 16px;

            padding: 28px;

            box-shadow:
                0 4px 20px
                rgba(0, 0, 0, 0.05);

            margin-bottom: 24px;

        }


        .setting-header {

            display: flex;

            align-items: center;

            gap: 16px;

            margin-bottom: 28px;

        }


        .setting-icon {

            width: 50px;

            height: 50px;

            border-radius: 12px;

            background:
                rgba(37, 99, 235, 0.10);

            color: #2563eb;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 22px;

        }


        .setting-header h4 {

            margin: 0;

            font-size: 20px;

            font-weight: 700;

            color: #1e293b;

        }


        .setting-header p {

            margin: 4px 0 0;

            color: #64748b;

            font-size: 14px;

        }


        .form-label {

            font-weight: 600;

            color: #334155;

            margin-bottom: 8px;

        }


        .form-control {

            border: 1px solid #e2e8f0;

            border-radius: 10px;

            padding: 12px 14px;

        }


        .form-control:focus {

            border-color: #2563eb;

            box-shadow:
                0 0 0 3px
                rgba(37, 99, 235, 0.10);

        }


        textarea.form-control {

            min-height: 110px;

            resize: vertical;

        }


        .save-button {

            background: #2563eb;

            border: none;

            color: #ffffff;

            padding: 12px 24px;

            border-radius: 10px;

            font-weight: 600;

        }


        .save-button:hover {

            background: #1d4ed8;

            color: #ffffff;

        }


        .info-box {

            background: #f8fafc;

            border-radius: 12px;

            padding: 18px;

            color: #475569;

        }


        .info-box-item {

            display: flex;

            align-items: flex-start;

            gap: 12px;

            padding: 12px 0;

            border-bottom: 1px solid #e2e8f0;

        }


        .info-box-item:last-child {

            border-bottom: none;

        }


        .info-box-item i {

            color: #2563eb;

            font-size: 18px;

        }


        .info-label {

            font-size: 12px;

            color: #64748b;

            margin-bottom: 3px;

        }


        .info-value {

            font-weight: 600;

            color: #1e293b;

        }


    </style>

</head>


<body>


<!-- =====================================================
     SIDEBAR
===================================================== -->

<aside class="sidebar">


    <!-- BRAND -->

    <div class="brand">

        <div class="brand-icon">

            <i class="bi bi-shop"></i>

        </div>


        <div>

            <div class="brand-title">

                <?= htmlspecialchars(
                    $pengaturan['nama_toko']
                ); ?>

            </div>


            <div class="brand-subtitle">

                <?= htmlspecialchars(
                    $pengaturan['nama_aplikasi']
                ); ?>

            </div>

        </div>

    </div>


    <!-- MENU UTAMA -->

    <div class="menu-title">

        MENU UTAMA

    </div>


    <a href="../dashboard.php">

        <i class="bi bi-grid-1x2-fill"></i>

        <span>
            Dashboard
        </span>

    </a>


    <a href="../penghasilan/index.php">

        <i class="bi bi-wallet2"></i>

        <span>
            Penghasilan
        </span>

    </a>


    <!-- LAPORAN -->

    <div class="menu-title">

        LAPORAN

    </div>


    <a href="../laporan/harian.php">

        <i class="bi bi-calendar-day"></i>

        <span>
            Laporan Harian
        </span>

    </a>


    <a href="../laporan/bulanan.php">

        <i class="bi bi-calendar-month"></i>

        <span>
            Laporan Bulanan
        </span>

    </a>


    <a href="../laporan/tahunan.php">

        <i class="bi bi-calendar3"></i>

        <span>
            Laporan Tahunan
        </span>

    </a>


    <!-- SISTEM -->

    <div class="menu-title">

        SISTEM

    </div>


    <a
        href="index.php"
        class="active"
    >

        <i class="bi bi-gear"></i>

        <span>
            Pengaturan
        </span>

    </a>


</aside>



<!-- =====================================================
     MAIN CONTENT
===================================================== -->

<main class="main-content">


    <!-- TOP NAVBAR -->

    <header class="top-navbar">


        <div class="page-heading">

            <div>

                <h1>
                    Pengaturan
                </h1>

                <p>
                    Kelola informasi toko dan sistem
                </p>

            </div>

        </div>


        <!-- USER -->

        <div class="dropdown">

            <button
                class="user-button dropdown-toggle"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
            >

                <span class="user-avatar">

                    <i class="bi bi-person-fill"></i>

                </span>


                <span class="user-name">

                    <?= htmlspecialchars(
                        $_SESSION['nama']
                    ); ?>

                </span>

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


    </header>



    <!-- =================================================
         ALERT
    ================================================== -->

    <?php if ($pesan !== ""): ?>

        <div
            class="alert alert-<?= $tipe_pesan; ?> alert-dismissible fade show"
            role="alert"
        >

            <?php if ($tipe_pesan === "success"): ?>

                <i class="bi bi-check-circle-fill me-2"></i>

            <?php else: ?>

                <i class="bi bi-exclamation-circle-fill me-2"></i>

            <?php endif; ?>


            <?= htmlspecialchars($pesan); ?>


            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    <?php endif; ?>



    <!-- =================================================
         ROW
    ================================================== -->

    <div class="row g-4">


        <!-- FORM PENGATURAN -->

        <div class="col-xl-8">

            <div class="setting-card">


                <div class="setting-header">

                    <div class="setting-icon">

                        <i class="bi bi-gear-fill"></i>

                    </div>


                    <div>

                        <h4>
                            Informasi Toko
                        </h4>

                        <p>
                            Ubah informasi toko Anda
                        </p>

                    </div>

                </div>



                <form
                    method="POST"
                    action=""
                >


                    <!-- NAMA TOKO -->

                    <div class="mb-4">

                        <label
                            class="form-label"
                            for="nama_toko"
                        >

                            Nama Toko

                        </label>


                        <input
                            type="text"
                            class="form-control"
                            id="nama_toko"
                            name="nama_toko"
                            value="<?= htmlspecialchars(
                                $pengaturan['nama_toko']
                            ); ?>"
                            placeholder="Contoh: Toko Sejahtera"
                            required
                        >


                    </div>



                    <!-- NAMA APLIKASI -->

                    <div class="mb-4">

                        <label
                            class="form-label"
                            for="nama_aplikasi"
                        >

                            Nama Aplikasi

                        </label>


                        <input
                            type="text"
                            class="form-control"
                            id="nama_aplikasi"
                            name="nama_aplikasi"
                            value="<?= htmlspecialchars(
                                $pengaturan['nama_aplikasi']
                            ); ?>"
                            placeholder="Contoh: Management System"
                            required
                        >


                    </div>



                    <!-- ALAMAT -->

                    <div class="mb-4">

                        <label
                            class="form-label"
                            for="alamat"
                        >

                            Alamat Toko

                        </label>


                        <textarea
                            class="form-control"
                            id="alamat"
                            name="alamat"
                            placeholder="Masukkan alamat toko"
                        ><?= htmlspecialchars(
                            $pengaturan['alamat']
                        ); ?></textarea>


                    </div>



                    <!-- TELEPON -->

                    <div class="mb-4">

                        <label
                            class="form-label"
                            for="telepon"
                        >

                            Nomor Telepon

                        </label>


                        <input
                            type="text"
                            class="form-control"
                            id="telepon"
                            name="telepon"
                            value="<?= htmlspecialchars(
                                $pengaturan['telepon']
                            ); ?>"
                            placeholder="Contoh: 081234567890"
                        >


                    </div>



                    <!-- EMAIL -->

                    <div class="mb-4">

                        <label
                            class="form-label"
                            for="email"
                        >

                            Email Toko

                        </label>


                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            value="<?= htmlspecialchars(
                                $pengaturan['email']
                            ); ?>"
                            placeholder="Contoh: toko@email.com"
                        >


                    </div>



                    <!-- BUTTON -->

                    <div class="d-flex justify-content-end">

                        <button
                            type="submit"
                            class="save-button"
                        >

                            <i class="bi bi-save me-2"></i>

                            Simpan Pengaturan

                        </button>

                    </div>


                </form>


            </div>

        </div>



        <!-- PREVIEW -->

        <div class="col-xl-4">

            <div class="setting-card">


                <div class="setting-header">

                    <div class="setting-icon">

                        <i class="bi bi-eye-fill"></i>

                    </div>


                    <div>

                        <h4>
                            Informasi Saat Ini
                        </h4>

                        <p>
                            Data toko yang tersimpan
                        </p>

                    </div>

                </div>



                <div class="info-box">


                    <!-- TOKO -->

                    <div class="info-box-item">

                        <i class="bi bi-shop"></i>

                        <div>

                            <div class="info-label">
                                Nama Toko
                            </div>

                            <div class="info-value">

                                <?= htmlspecialchars(
                                    $pengaturan['nama_toko']
                                ); ?>

                            </div>

                        </div>

                    </div>



                    <!-- APLIKASI -->

                    <div class="info-box-item">

                        <i class="bi bi-window"></i>

                        <div>

                            <div class="info-label">
                                Nama Aplikasi
                            </div>

                            <div class="info-value">

                                <?= htmlspecialchars(
                                    $pengaturan['nama_aplikasi']
                                ); ?>

                            </div>

                        </div>

                    </div>



                    <!-- ALAMAT -->

                    <div class="info-box-item">

                        <i class="bi bi-geo-alt"></i>

                        <div>

                            <div class="info-label">
                                Alamat
                            </div>

                            <div class="info-value">

                                <?= $pengaturan['alamat'] !== ''
                                    ? nl2br(
                                        htmlspecialchars(
                                            $pengaturan['alamat']
                                        )
                                    )
                                    : '-';
                                ?>

                            </div>

                        </div>

                    </div>



                    <!-- TELEPON -->

                    <div class="info-box-item">

                        <i class="bi bi-telephone"></i>

                        <div>

                            <div class="info-label">
                                Telepon
                            </div>

                            <div class="info-value">

                                <?= $pengaturan['telepon'] !== ''
                                    ? htmlspecialchars(
                                        $pengaturan['telepon']
                                    )
                                    : '-';
                                ?>

                            </div>

                        </div>

                    </div>



                    <!-- EMAIL -->

                    <div class="info-box-item">

                        <i class="bi bi-envelope"></i>

                        <div>

                            <div class="info-label">
                                Email
                            </div>

                            <div class="info-value">

                                <?= $pengaturan['email'] !== ''
                                    ? htmlspecialchars(
                                        $pengaturan['email']
                                    )
                                    : '-';
                                ?>

                            </div>

                        </div>

                    </div>


                </div>


            </div>

        </div>


    </div>



    <!-- FOOTER -->

    <footer class="dashboard-footer">

        <span>

            © <?= date('Y'); ?>

            <?= htmlspecialchars(
                $pengaturan['nama_toko']
            ); ?>

        </span>


        <span>

            <?= htmlspecialchars(
                $pengaturan['nama_aplikasi']
            ); ?>

        </span>

    </footer>


</main>



<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>

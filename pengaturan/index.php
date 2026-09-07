<?php

include "../config/auth.php";
include "../config/database.php";


/*
|--------------------------------------------------------------------------
| BUAT TABEL PENGATURAN
|--------------------------------------------------------------------------
*/

$query_create = mysqli_query($conn, "
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
| CEK DATA PENGATURAN
|--------------------------------------------------------------------------
*/

$query_cek = mysqli_query(
    $conn,
    "SELECT * FROM pengaturan ORDER BY id ASC LIMIT 1"
);


if (!$query_cek) {

    die(
        "Error database: " .
        mysqli_error($conn)
    );

}


$pengaturan = mysqli_fetch_assoc($query_cek);


/*
|--------------------------------------------------------------------------
| BUAT DATA DEFAULT
|--------------------------------------------------------------------------
*/

if (!$pengaturan) {

    $insert = mysqli_query($conn, "
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


    if (!$insert) {

        die(
            "Gagal membuat pengaturan: " .
            mysqli_error($conn)
        );

    }


    $query_cek = mysqli_query(
        $conn,
        "SELECT * FROM pengaturan ORDER BY id ASC LIMIT 1"
    );


    $pengaturan = mysqli_fetch_assoc(
        $query_cek
    );

}


/*
|--------------------------------------------------------------------------
| SIMPAN PENGATURAN
|--------------------------------------------------------------------------
*/

$pesan = "";
$tipe_pesan = "";
$pesan_password = "";
$tipe_pesan_password = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ubah_password'])) {

    $password_lama = $_POST['password_lama'] ?? '';
    $password_baru = $_POST['password_baru'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';
    $user_id = (int) ($_SESSION['user_id'] ?? 0);

    if ($user_id <= 0) {
        $pesan_password = "Sesi pengguna tidak valid. Silakan login kembali.";
        $tipe_pesan_password = "danger";
    } elseif ($password_lama === '' || $password_baru === '' || $konfirmasi_password === '') {
        $pesan_password = "Semua kolom password wajib diisi.";
        $tipe_pesan_password = "danger";
    } elseif (strlen($password_baru) < 6) {
        $pesan_password = "Password baru minimal 6 karakter.";
        $tipe_pesan_password = "danger";
    } elseif ($password_baru !== $konfirmasi_password) {
        $pesan_password = "Konfirmasi password baru tidak sama.";
        $tipe_pesan_password = "danger";
    } else {
        $stmt_user = mysqli_prepare($conn, "SELECT password FROM users WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt_user, "i", $user_id);
        mysqli_stmt_execute($stmt_user);
        $result_user = mysqli_stmt_get_result($stmt_user);
        $user_password = mysqli_fetch_assoc($result_user);
        mysqli_stmt_close($stmt_user);

        $password_valid = $user_password && (
            password_verify($password_lama, $user_password['password']) ||
            $password_lama === $user_password['password']
        );

        if (!$password_valid) {
            $pesan_password = "Password saat ini salah.";
            $tipe_pesan_password = "danger";
        } elseif ($password_lama === $password_baru) {
            $pesan_password = "Password baru harus berbeda dari password saat ini.";
            $tipe_pesan_password = "danger";
        } else {
            $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
            $stmt_update_password = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt_update_password, "si", $password_hash, $user_id);

            if (mysqli_stmt_execute($stmt_update_password)) {
                $pesan_password = "Password berhasil diubah. Gunakan password baru saat login berikutnya.";
                $tipe_pesan_password = "success";
            } else {
                $pesan_password = "Gagal mengubah password: " . mysqli_stmt_error($stmt_update_password);
                $tipe_pesan_password = "danger";
            }

            mysqli_stmt_close($stmt_update_password);
        }
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $nama_toko = trim(
        $_POST['nama_toko'] ?? ''
    );


    $nama_aplikasi = trim(
        $_POST['nama_aplikasi'] ?? ''
    );


    $alamat = trim(
        $_POST['alamat'] ?? ''
    );


    $telepon = trim(
        $_POST['telepon'] ?? ''
    );


    $email = trim(
        $_POST['email'] ?? ''
    );


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
        | UPDATE
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
            WHERE id = ?
        ");


        if (!$stmt) {

            die(
                "Gagal menyiapkan query: " .
                mysqli_error($conn)
            );

        }


        $id_pengaturan = $pengaturan['id'];


        mysqli_stmt_bind_param(
            $stmt,
            "sssssi",
            $nama_toko,
            $nama_aplikasi,
            $alamat,
            $telepon,
            $email,
            $id_pengaturan
        );


        if (
            mysqli_stmt_execute(
                $stmt
            )
        ) {

            $pesan =
                "Pengaturan berhasil disimpan.";

            $tipe_pesan =
                "success";


            /*
            |--------------------------------------------------------------------------
            | AMBIL DATA TERBARU
            |--------------------------------------------------------------------------
            */

            $query_terbaru = mysqli_query(
                $conn,
                "SELECT * FROM pengaturan WHERE id = $id_pengaturan LIMIT 1"
            );


            $pengaturan =
                mysqli_fetch_assoc(
                    $query_terbaru
                );

        } else {

            $pesan =
                "Gagal menyimpan pengaturan: " .
                mysqli_stmt_error($stmt);

            $tipe_pesan =
                "danger";

        }


        mysqli_stmt_close($stmt);

    }

}


/*
|--------------------------------------------------------------------------
| DATA UNTUK TAMPILAN
|--------------------------------------------------------------------------
*/

$nama_toko =
    $pengaturan['nama_toko']
    ?? 'Toko Income';


$nama_aplikasi =
    $pengaturan['nama_aplikasi']
    ?? 'Toko Income Management System';


$alamat =
    $pengaturan['alamat']
    ?? '';


$telepon =
    $pengaturan['telepon']
    ?? '';


$email =
    $pengaturan['email']
    ?? '';

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
        Pengaturan - <?= htmlspecialchars($nama_toko); ?>
    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


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

        .password-note { color: #64748b; font-size: 13px; margin-top: 6px; }
        .password-input-wrap { position: relative; }
        .password-input-wrap .form-control { padding-right: 48px; }
        .password-toggle { position: absolute; top: 50%; right: 10px; transform: translateY(-50%); border: 0; background: transparent; color: #64748b; width: 36px; height: 36px; border-radius: 8px; }
        .password-toggle:hover { background: #f1f5f9; color: #2563eb; }

    </style>

</head>


<body>


<!-- SIDEBAR -->

<div class="sidebar">
<div class="brand"><i class="bi bi-shop"></i> TOKO INCOME</div>
<div class="menu-title">Menu Utama</div>
<a href="../dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
<a href="../penghasilan/index.php"><i class="bi bi-cash-stack me-2"></i> Penghasilan</a>
<a href="../pengeluaran/index.php"><i class="bi bi-wallet2 me-2"></i> Pengeluaran</a>
<a href="../barang/index.php"><i class="bi bi-box-seam me-2"></i> Barang</a>
<a href="../barang/stok.php"><i class="bi bi-clipboard-check me-2"></i> Pengecekan Stok</a>

<div class="menu-title">Laporan</div>
<a href="../laporan/harian.php"><i class="bi bi-calendar-day me-2"></i> Laporan Harian</a>
<a href="../laporan/bulanan.php"><i class="bi bi-calendar-month me-2"></i> Laporan Bulanan</a>
<a href="../laporan/tahunan.php"><i class="bi bi-calendar3 me-2"></i> Laporan Tahunan</a>
<div class="menu-title">Sistem</div>
<a href="../pengaturan/index.php" class="active"><i class="bi bi-gear me-2"></i> Pengaturan</a>
</div>


<!-- MAIN -->

<main class="main-content">


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


        <div class="dropdown">

            <button
                class="user-button dropdown-toggle"
                type="button"
                data-bs-toggle="dropdown"
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


    <!-- ALERT -->

    <?php if ($pesan !== ''): ?>

        <div
            class="alert alert-<?= htmlspecialchars($tipe_pesan); ?> alert-dismissible fade show"
            role="alert"
        >

            <?php if ($tipe_pesan === 'success'): ?>

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


    <!-- CONTENT -->

    <div class="row g-4">


        <!-- FORM -->

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


                <form method="POST">


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
                            value="<?= htmlspecialchars($nama_toko); ?>"
                            required
                        >

                    </div>


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
                            value="<?= htmlspecialchars($nama_aplikasi); ?>"
                            required
                        >

                    </div>


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
                        ><?= htmlspecialchars($alamat); ?></textarea>

                    </div>


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
                            value="<?= htmlspecialchars($telepon); ?>"
                        >

                    </div>


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
                            value="<?= htmlspecialchars($email); ?>"
                        >

                    </div>


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


        <!-- KEAMANAN AKUN -->

        <div class="col-xl-8">
            <div class="setting-card">
                <div class="setting-header">
                    <div class="setting-icon"><i class="bi bi-shield-lock-fill"></i></div>
                    <div>
                        <h4>Keamanan Akun</h4>
                        <p>Ganti password akun yang sedang digunakan</p>
                    </div>
                </div>

                <?php if ($pesan_password !== ''): ?>
                    <div class="alert alert-<?= htmlspecialchars($tipe_pesan_password); ?> alert-dismissible fade show" role="alert">
                        <i class="bi <?= $tipe_pesan_password === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill'; ?> me-2"></i>
                        <?= htmlspecialchars($pesan_password); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" autocomplete="off">
                    <input type="hidden" name="ubah_password" value="1">
                    <div class="mb-4">
                        <label class="form-label" for="password_lama">Password Saat Ini</label>
                        <div class="password-input-wrap">
                            <input type="password" class="form-control" id="password_lama" name="password_lama" autocomplete="current-password" required>
                            <button type="button" class="password-toggle" data-target="password_lama" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="password_baru">Password Baru</label>
                        <div class="password-input-wrap">
                            <input type="password" class="form-control" id="password_baru" name="password_baru" minlength="6" autocomplete="new-password" required>
                            <button type="button" class="password-toggle" data-target="password_baru" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
                        </div>
                        <div class="password-note">Minimal 6 karakter. Gunakan kombinasi yang sulit ditebak.</div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="konfirmasi_password">Konfirmasi Password Baru</label>
                        <div class="password-input-wrap">
                            <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" minlength="6" autocomplete="new-password" required>
                            <button type="button" class="password-toggle" data-target="konfirmasi_password" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="save-button"><i class="bi bi-key-fill me-2"></i>Ubah Password</button>
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


                    <div class="info-box-item">

                        <i class="bi bi-shop"></i>

                        <div>

                            <div class="info-label">
                                Nama Toko
                            </div>

                            <div class="info-value">
                                <?= htmlspecialchars($nama_toko); ?>
                            </div>

                        </div>

                    </div>


                    <div class="info-box-item">

                        <i class="bi bi-window"></i>

                        <div>

                            <div class="info-label">
                                Nama Aplikasi
                            </div>

                            <div class="info-value">
                                <?= htmlspecialchars($nama_aplikasi); ?>
                            </div>

                        </div>

                    </div>


                    <div class="info-box-item">

                        <i class="bi bi-geo-alt"></i>

                        <div>

                            <div class="info-label">
                                Alamat
                            </div>

                            <div class="info-value">

                                <?= $alamat !== ''
                                    ? nl2br(
                                        htmlspecialchars($alamat)
                                    )
                                    : '-';
                                ?>

                            </div>

                        </div>

                    </div>


                    <div class="info-box-item">

                        <i class="bi bi-telephone"></i>

                        <div>

                            <div class="info-label">
                                Telepon
                            </div>

                            <div class="info-value">

                                <?= $telepon !== ''
                                    ? htmlspecialchars($telepon)
                                    : '-';
                                ?>

                            </div>

                        </div>

                    </div>


                    <div class="info-box-item">

                        <i class="bi bi-envelope"></i>

                        <div>

                            <div class="info-label">
                                Email
                            </div>

                            <div class="info-value">

                                <?= $email !== ''
                                    ? htmlspecialchars($email)
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

            <?= htmlspecialchars($nama_toko); ?>

        </span>


        <span>

            <?= htmlspecialchars($nama_aplikasi); ?>

        </span>

    </footer>


</main>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


<script>
    document.querySelectorAll('.password-toggle').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.getElementById(button.dataset.target);
            const icon = button.querySelector('i');
            if (input.type === 'password') { input.type = 'text'; icon.className = 'bi bi-eye-slash'; }
            else { input.type = 'password'; icon.className = 'bi bi-eye'; }
        });
    });
</script>

<script src="../assets/js/mobile-menu.js"></script>
</body>

</html>

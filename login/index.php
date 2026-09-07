<?php

session_start();

include "../config/database.php";

/*
|--------------------------------------------------------------------------
| BUAT TABEL PENGATURAN
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
| AMBIL PENGATURAN TOKO
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
| Jika sudah login
|--------------------------------------------------------------------------
*/

if (isset($_SESSION['login'])) {

    header("Location: ../dashboard.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| Proses Login
|--------------------------------------------------------------------------
*/

$error = "";


if (isset($_POST['login'])) {

    $username = trim($_POST['username']);

    $password = $_POST['password'];


    /*
    |--------------------------------------------------------------------------
    | Cari user
    |--------------------------------------------------------------------------
    */

    $stmt = mysqli_prepare($conn, "
        SELECT *
        FROM users
        WHERE username = ?
        LIMIT 1
    ");


    mysqli_stmt_bind_param(
        $stmt,
        "s",
        $username
    );


    mysqli_stmt_execute($stmt);


    $result = mysqli_stmt_get_result($stmt);


    $user = mysqli_fetch_assoc($result);


    /*
    |--------------------------------------------------------------------------
    | Cek username dan password
    |--------------------------------------------------------------------------
    */

    if ($user && (
        password_verify($password, $user['password']) ||
        $password === $user['password']
    )) {

        $_SESSION['login'] = true;

        $_SESSION['user_id'] = $user['id'];

        $_SESSION['username'] = $user['username'];

        $_SESSION['nama'] = $user['nama'];


        header("Location: ../dashboard.php");

        exit;

    } else {

        $error = "Username atau password salah.";

    }

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
        Login - <?= htmlspecialchars($nama_toko); ?>
    </title>



    <!-- =====================================================
         FONT
    ====================================================== -->

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >


    <!-- =====================================================
         BOOTSTRAP ICON
    ====================================================== -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <!-- =====================================================
         CSS LOGIN
    ====================================================== -->

    <style>

        /* =====================================================
           GLOBAL
        ===================================================== */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        body {

            min-height: 100vh;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 20px;

            font-family:
                'Inter',
                'Segoe UI',
                Arial,
                sans-serif;

            color: #27272a;

            background:
                radial-gradient(
                    circle at top left,
                    #ffffff 0%,
                    #f4f4f5 40%,
                    #d4d4d8 100%
                );

            position: relative;

            overflow: hidden;

        }


        /* =====================================================
           BACKGROUND DECORATION
        ===================================================== */

        body::before {

            content: "";

            position: fixed;

            width: 420px;
            height: 420px;

            border-radius: 50%;

            background:
                rgba(255, 255, 255, 0.45);

            top: -180px;
            right: -120px;

            filter: blur(2px);

            pointer-events: none;

        }


        body::after {

            content: "";

            position: fixed;

            width: 350px;
            height: 350px;

            border-radius: 50%;

            background:
                rgba(39, 39, 42, 0.06);

            bottom: -180px;
            left: -100px;

            pointer-events: none;

        }


        /* =====================================================
           CONTAINER
        ===================================================== */

        .login-container {

            width: 100%;

            max-width: 430px;

            position: relative;

            z-index: 2;

        }


        /* =====================================================
           LOGIN CARD
        ===================================================== */

        .login-card {

            width: 100%;

            background:
                rgba(255, 255, 255, 0.96);

            border:
                1px solid #d4d4d8;

            border-radius: 22px;

            box-shadow:
                0 25px 60px
                rgba(0, 0, 0, 0.13);

            overflow: hidden;

            position: relative;

        }


        /* garis atas */

        .login-card::before {

            content: "";

            position: absolute;

            top: 0;
            left: 0;

            width: 100%;
            height: 5px;

            background:
                linear-gradient(
                    90deg,
                    #18181b,
                    #52525b,
                    #a1a1aa
                );

        }


        /* =====================================================
           CARD CONTENT
        ===================================================== */

        .login-card-body {

            padding:
                42px 38px 36px;

        }


        /* =====================================================
           LOGO
        ===================================================== */

        .login-icon {

            width: 76px;

            height: 76px;

            margin:
                0 auto 20px;

            display: flex;

            align-items: center;

            justify-content: center;

            background:
                linear-gradient(
                    135deg,
                    #27272a,
                    #18181b
                );

            color: #ffffff;

            border-radius: 20px;

            font-size: 31px;

            box-shadow:
                0 12px 25px
                rgba(0, 0, 0, 0.18);

            position: relative;

        }


        .login-icon::after {

            content: "";

            position: absolute;

            inset: -5px;

            border-radius: 24px;

            border:
                1px solid
                rgba(39, 39, 42, 0.12);

        }


        /* =====================================================
           TITLE
        ===================================================== */

        .login-title {

            text-align: center;

            color: #18181b;

            font-size: 26px;

            font-weight: 800;

            letter-spacing: .3px;

            margin-bottom: 7px;

        }


        .login-subtitle {

            text-align: center;

            color: #71717a;

            font-size: 13px;

            margin-bottom: 30px;

        }


        /* =====================================================
           ERROR
        ===================================================== */

        .login-error {

            display: flex;

            align-items: center;

            gap: 10px;

            background: #f4f4f5;

            border:
                1px solid #d4d4d8;

            color: #3f3f46;

            border-radius: 11px;

            padding:
                12px 14px;

            margin-bottom: 20px;

            font-size: 13px;

            line-height: 1.4;

        }


        .login-error i {

            color: #52525b;

            font-size: 17px;

        }


        /* =====================================================
           FORM GROUP
        ===================================================== */

        .form-group {

            margin-bottom: 20px;

        }


        .form-label {

            display: block;

            margin-bottom: 8px;

            color: #3f3f46;

            font-size: 13px;

            font-weight: 700;

        }


        /* =====================================================
           INPUT WRAPPER
        ===================================================== */

        .input-wrapper {

            position: relative;

        }


        /* =====================================================
           INPUT ICON
        ===================================================== */

        .input-icon {

            position: absolute;

            left: 15px;

            top: 50%;

            transform:
                translateY(-50%);

            color: #71717a;

            font-size: 17px;

            pointer-events: none;

            z-index: 2;

        }


        /* =====================================================
           INPUT
        ===================================================== */

        .form-control {

            width: 100%;

            height: 50px;

            padding:
                10px 14px 10px 45px;

            border:
                1px solid #d4d4d8;

            border-radius: 11px;

            background: #fafafa;

            color: #27272a;

            font-family: inherit;

            font-size: 14px;

            outline: none;

            transition:
                all .2s ease;

        }


        .form-control::placeholder {

            color: #a1a1aa;

        }


        .form-control:hover {

            background: #ffffff;

            border-color: #a1a1aa;

        }


        .form-control:focus {

            background: #ffffff;

            border-color: #52525b;

            box-shadow:
                0 0 0 3px
                rgba(82, 82, 91, .12);

        }


        /* =====================================================
           LOGIN BUTTON
        ===================================================== */

        .btn-login {

            width: 100%;

            height: 50px;

            margin-top: 4px;

            border: none;

            border-radius: 11px;

            background:
                linear-gradient(
                    135deg,
                    #27272a,
                    #18181b
                );

            color: #ffffff;

            font-family: inherit;

            font-size: 14px;

            font-weight: 700;

            cursor: pointer;

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 8px;

            box-shadow:
                0 8px 20px
                rgba(0, 0, 0, .16);

            transition:
                all .2s ease;

        }


        .btn-login:hover {

            background:
                linear-gradient(
                    135deg,
                    #18181b,
                    #09090b
                );

            transform:
                translateY(-2px);

            box-shadow:
                0 12px 25px
                rgba(0, 0, 0, .22);

        }


        .btn-login:active {

            transform:
                translateY(0);

        }


        .btn-login i {

            font-size: 16px;

        }


        /* =====================================================
           FOOTER
        ===================================================== */

        .login-footer {

            margin-top: 27px;

            padding-top: 20px;

            border-top:
                1px solid #e4e4e7;

            text-align: center;

            color: #a1a1aa;

            font-size: 11px;

        }


        .login-footer strong {

            color: #52525b;

        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 480px) {

            body {

                padding: 15px;

            }


            .login-card {

                border-radius: 18px;

            }


            .login-card-body {

                padding:
                    35px 23px 28px;

            }


            .login-icon {

                width: 65px;

                height: 65px;

                font-size: 27px;

                border-radius: 17px;

            }


            .login-title {

                font-size: 22px;

            }


            .login-subtitle {

                margin-bottom: 25px;

            }

        }

    </style>

</head>


<body>


<!-- =====================================================
     LOGIN CONTAINER
===================================================== -->

<div class="login-container">


    <!-- =================================================
         LOGIN CARD
    ================================================== -->

    <div class="login-card">


        <div class="login-card-body">


            <!-- =================================================
                 LOGO & TITLE
            ================================================== -->

            <div>

                <div class="login-icon">

                    <i class="bi bi-shop"></i>

                </div>


                <h1 class="login-title">

                    <?= htmlspecialchars($nama_toko); ?>

                </h1>



                <p class="login-subtitle">

                    Silakan login untuk melanjutkan

                </p>

            </div>


            <!-- =================================================
                 ERROR
            ================================================== -->

            <?php if ($error != ""): ?>

                <div class="login-error">

                    <i
                        class="bi bi-exclamation-circle"
                    ></i>

                    <span>

                        <?= htmlspecialchars($error); ?>

                    </span>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 FORM LOGIN
            ================================================== -->

            <form method="POST">


                <!-- USERNAME -->

                <div class="form-group">

                    <label
                        class="form-label"
                        for="username"
                    >

                        Username

                    </label>


                    <div class="input-wrapper">

                        <i
                            class="bi bi-person input-icon"
                        ></i>


                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="form-control"
                            placeholder="Masukkan username"
                            autocomplete="username"
                            required
                            autofocus
                        >

                    </div>

                </div>


                <!-- PASSWORD -->

                <div class="form-group">

                    <label
                        class="form-label"
                        for="password"
                    >

                        Password

                    </label>


                    <div class="input-wrapper">

                        <i
                            class="bi bi-lock input-icon"
                        ></i>


                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                        >

                    </div>

                </div>


                <!-- LOGIN BUTTON -->

                <button
                    type="submit"
                    name="login"
                    class="btn-login"
                >

                    <i
                        class="bi bi-box-arrow-in-right"
                    ></i>

                    Login

                </button>


            </form>


            <!-- =================================================
                 FOOTER
            ================================================== -->

            <div class="login-footer">

                <strong><?= htmlspecialchars($nama_toko); ?></strong>

                &nbsp;•&nbsp;

                <?= htmlspecialchars($nama_aplikasi); ?>

            </div>



        </div>

    </div>

</div>


</body>

</html>

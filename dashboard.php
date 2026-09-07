<?php

include "config/auth.php";
include "config/database.php";

/*
|--------------------------------------------------------------------------
| PENGATURAN TOKO
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


$query_pengaturan = mysqli_query(
    $conn,
    "SELECT * FROM pengaturan WHERE id = 1 LIMIT 1"
);


$pengaturan = mysqli_fetch_assoc(
    $query_pengaturan
);


$nama_toko = $pengaturan['nama_toko'];
$nama_aplikasi = $pengaturan['nama_aplikasi'];

/*
|--------------------------------------------------------------------------
| TOTAL HARI INI
|--------------------------------------------------------------------------
*/

$query_hari = mysqli_query($conn, "
    SELECT COALESCE(SUM(total), 0) AS total
    FROM penghasilan
    WHERE tanggal = CURDATE()
");

$data_hari = mysqli_fetch_assoc($query_hari);
$total_hari = $data_hari['total'];


/*
|--------------------------------------------------------------------------
| TOTAL BULAN INI
|--------------------------------------------------------------------------
*/

$query_bulan = mysqli_query($conn, "
    SELECT COALESCE(SUM(total), 0) AS total
    FROM penghasilan
    WHERE MONTH(tanggal) = MONTH(CURDATE())
    AND YEAR(tanggal) = YEAR(CURDATE())
");

$data_bulan = mysqli_fetch_assoc($query_bulan);
$total_bulan = $data_bulan['total'];


/*
|--------------------------------------------------------------------------
| TOTAL TAHUN INI
|--------------------------------------------------------------------------
*/

$query_tahun = mysqli_query($conn, "
    SELECT COALESCE(SUM(total), 0) AS total
    FROM penghasilan
    WHERE YEAR(tanggal) = YEAR(CURDATE())
");

$data_tahun = mysqli_fetch_assoc($query_tahun);
$total_tahun = $data_tahun['total'];


/*
|--------------------------------------------------------------------------
| JUMLAH TRANSAKSI
|--------------------------------------------------------------------------
*/

$query_transaksi = mysqli_query($conn, "
    SELECT COUNT(*) AS jumlah
    FROM penghasilan
");

$data_transaksi = mysqli_fetch_assoc($query_transaksi);
$jumlah_transaksi = $data_transaksi['jumlah'];

// TOTAL PEMASUKAN & PENGELUARAN
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS pengeluaran (id INT AUTO_INCREMENT PRIMARY KEY, tanggal DATE NOT NULL, nama_pengeluaran VARCHAR(255) NOT NULL, jumlah DECIMAL(15,2) NOT NULL DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
$total_pemasukan = (float) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total),0) total FROM penghasilan"))['total'];
$total_pengeluaran = (float) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(jumlah),0) total FROM pengeluaran"))['total'];
$saldo = $total_pemasukan - $total_pengeluaran;


/*
|--------------------------------------------------------------------------
| DATA GRAFIK 7 HARI TERAKHIR
|--------------------------------------------------------------------------
*/

$grafik_tanggal = [];
$grafik_total = [];

for ($i = 6; $i >= 0; $i--) {

    $tanggal_grafik = date(
        'Y-m-d',
        strtotime("-$i days")
    );

    $stmt_grafik = mysqli_prepare($conn, "
        SELECT COALESCE(SUM(total), 0) AS total
        FROM penghasilan
        WHERE tanggal = ?
    ");

    mysqli_stmt_bind_param(
        $stmt_grafik,
        "s",
        $tanggal_grafik
    );

    mysqli_stmt_execute($stmt_grafik);

    $result_grafik = mysqli_stmt_get_result(
        $stmt_grafik
    );

    $data_grafik = mysqli_fetch_assoc(
        $result_grafik
    );

    $grafik_tanggal[] = date(
        'd/m',
        strtotime($tanggal_grafik)
    );

    $grafik_total[] = (float) $data_grafik['total'];

    mysqli_stmt_close($stmt_grafik);
}


/*
|--------------------------------------------------------------------------
| TRANSAKSI TERBARU
|--------------------------------------------------------------------------
*/

$query_terbaru = mysqli_query($conn, "
    SELECT *
    FROM penghasilan
    ORDER BY tanggal DESC, id DESC
    LIMIT 5
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
        Dashboard - <?= htmlspecialchars($nama_toko); ?>
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
        href="assets/css/style.css?v=3"
    >


    <!-- Chart.js -->

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>


<body>


<!-- =====================================================
     SIDEBAR
===================================================== -->

<div class="sidebar">
<div class="brand"><i class="bi bi-shop"></i> TOKO INCOME</div>
<div class="menu-title">Menu Utama</div>
<a href="dashboard.php" class="active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
<a href="penghasilan/index.php"><i class="bi bi-cash-stack me-2"></i> Penghasilan</a>
<a href="pengeluaran/index.php"><i class="bi bi-wallet2 me-2"></i> Pengeluaran</a>
<a href="barang/index.php"><i class="bi bi-box-seam me-2"></i> Barang</a>
<a href="barang/stok.php"><i class="bi bi-clipboard-check me-2"></i> Pengecekan Stok</a>

<div class="menu-title">Laporan</div>
<a href="laporan/harian.php"><i class="bi bi-calendar-day me-2"></i> Laporan Harian</a>
<a href="laporan/bulanan.php"><i class="bi bi-calendar-month me-2"></i> Laporan Bulanan</a>
<a href="laporan/tahunan.php"><i class="bi bi-calendar3 me-2"></i> Laporan Tahunan</a>
<div class="menu-title">Sistem</div>
<a href="pengaturan/index.php"><i class="bi bi-gear me-2"></i> Pengaturan</a>
</div>



<!-- =====================================================
     MAIN CONTENT
===================================================== -->

<main class="main-content">


    <!-- =================================================
         TOP NAVBAR
    ================================================== -->

    <header class="top-navbar">


        <div class="page-heading">

            <div>

                <h1>
                    Dashboard
                </h1>

                <p>
                    Ringkasan penghasilan toko Anda
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
                        href="login/logout.php"
                    >

                        <i class="bi bi-box-arrow-right me-2"></i>

                        Logout

                    </a>

                </li>

            </ul>

        </div>


    </header>



    <!-- =================================================
         WELCOME
    ================================================== -->

    <section class="welcome-box">

        <div>

            <span class="welcome-label">
                SELAMAT DATANG 👋
            </span>

            <h2>
                Kelola penghasilan toko dengan mudah
            </h2>

            <p>
                Pantau performa dan transaksi toko Anda
                melalui dashboard ini.
            </p>

        </div>


        <div class="welcome-icon">

            <i class="bi bi-bar-chart-line-fill"></i>

        </div>

    </section>



    <!-- =================================================
         STAT CARD
    ================================================== -->

    <div class="row g-4 mb-4">


        <!-- TOTAL PEMASUKAN -->
        <div class="col-xl-3 col-md-6"><div class="stat-card green"><div class="stat-content"><div><span class="stat-label">TOTAL PEMASUKAN</span><h3>Rp <?= number_format($total_pemasukan,0,',','.') ?></h3><span class="stat-info">Semua pemasukan</span></div></div><div class="stat-icon"><i class="bi bi-arrow-down-circle"></i></div></div></div>

        <!-- TOTAL PENGELUARAN -->
        <div class="col-xl-3 col-md-6"><div class="stat-card red"><div class="stat-content"><div><span class="stat-label">TOTAL PENGELUARAN</span><h3>Rp <?= number_format($total_pengeluaran,0,',','.') ?></h3><span class="stat-info">Semua pengeluaran</span></div></div><div class="stat-icon"><i class="bi bi-arrow-up-circle"></i></div></div></div>

        <!-- SALDO -->
        <div class="col-xl-3 col-md-6"><div class="stat-card blue"><div class="stat-content"><div><span class="stat-label">SALDO</span><h3>Rp <?= number_format($saldo,0,',','.') ?></h3><span class="stat-info">Pemasukan - pengeluaran</span></div></div><div class="stat-icon"><i class="bi bi-wallet2"></i></div></div></div>

        <!-- TRANSAKSI -->
        <div class="col-xl-3 col-md-6"><div class="stat-card orange"><div class="stat-content"><div><span class="stat-label">TRANSAKSI PEMASUKAN</span><h3><?= number_format($jumlah_transaksi,0,',','.') ?></h3><span class="stat-info">Total transaksi pemasukan</span></div></div><div class="stat-icon"><i class="bi bi-receipt"></i></div></div></div>

    </div>



    <!-- =================================================
         GRAPH
    ================================================== -->

    <div class="row g-4 mb-4">

        <div class="col-xl-12">

            <div class="dashboard-card chart-card">

                <div class="card-header-custom">

                    <div>
                        <h4>
                            Penghasilan 7 Hari Terakhir
                        </h4>

                        <p>
                            Performa penghasilan berdasarkan tanggal
                        </p>
                    </div>

                    <div class="chart-icon">
                        <i class="bi bi-bar-chart-line"></i>
                    </div>

                </div>

                <div class="chart-container">
                    <canvas id="grafikPenghasilan"></canvas>
                </div>

            </div>

        </div>

    </div>


    <!-- =================================================
         TRANSAKSI TERBARU
    ================================================== -->

    <div class="dashboard-card transaction-card">


        <div class="card-header-custom">

            <div>

                <h4>
                    Transaksi Terbaru
                </h4>

                <p>
                    Lima transaksi terakhir yang tercatat
                </p>

            </div>


            <a
                href="penghasilan/index.php"
                class="view-all-button"
            >

                Lihat Semua

                <i class="bi bi-arrow-right"></i>

            </a>

        </div>



        <div class="table-responsive">


            <table class="table transaction-table mb-0">


                <thead>

                    <tr>

                        <th>
                            TANGGAL
                        </th>

                        <th>
                            PRODUK / TRANSAKSI
                        </th>

                        <th class="text-center">
                            JUMLAH
                        </th>

                        <th class="text-end">
                            TOTAL
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php

                if (
                    mysqli_num_rows(
                        $query_terbaru
                    ) > 0
                ) {

                    while (
                        $transaksi =
                        mysqli_fetch_assoc(
                            $query_terbaru
                        )
                    ) {

                ?>


                    <tr>


                        <td>

                            <div class="date-cell">

                                <span class="date-icon">

                                    <i class="bi bi-calendar3"></i>

                                </span>

                                <span>

                                    <?= date(
                                        'd-m-Y',
                                        strtotime(
                                            $transaksi['tanggal']
                                        )
                                    ); ?>

                                </span>

                            </div>

                        </td>


                        <td>

                            <div class="transaction-name">

                                <div class="transaction-avatar">

                                    <i class="bi bi-bag"></i>

                                </div>

                                <strong>

                                    <?= htmlspecialchars(
                                        $transaksi[
                                            'nama_transaksi'
                                        ]
                                    ); ?>

                                </strong>

                            </div>

                        </td>


                        <td class="text-center">

                            <span class="quantity-badge">

                                <?= $transaksi['jumlah']; ?>

                            </span>

                        </td>


                        <td class="text-end">

                            <strong class="total-income">

                                Rp <?= number_format(
                                    $transaksi['total'],
                                    0,
                                    ',',
                                    '.'
                                ); ?>

                            </strong>

                        </td>


                    </tr>


                <?php

                    }

                } else {

                ?>


                    <tr>

                        <td
                            colspan="4"
                            class="empty-data"
                        >

                            <i class="bi bi-inbox"></i>

                            <div>
                                Belum ada transaksi.
                            </div>

                        </td>

                    </tr>


                <?php

                }

                ?>


                </tbody>


            </table>


        </div>


    </div>


    <!-- FOOTER -->

    <footer class="dashboard-footer">

        <span>
            © <?= date('Y'); ?> <?= htmlspecialchars($nama_toko); ?>
        </span>

        <span>
            <?= htmlspecialchars($nama_aplikasi); ?>
        </span>

    </footer>


</main>



<!-- =====================================================
     JAVASCRIPT
===================================================== -->

<script>

const labels =
    <?= json_encode($grafik_tanggal); ?>;

const dataPenghasilan =
    <?= json_encode($grafik_total); ?>;


const ctx =
    document.getElementById(
        'grafikPenghasilan'
    );


new Chart(ctx, {

    type: 'line',

    data: {

        labels: labels,

        datasets: [

            {

                label: 'Penghasilan',

                data: dataPenghasilan,

                borderColor: '#2563eb',

                backgroundColor:
                    'rgba(37, 99, 235, 0.10)',

                borderWidth: 3,

                fill: true,

                tension: 0.4,

                pointRadius: 5,

                pointHoverRadius: 7,

                pointBackgroundColor: '#ffffff',

                pointBorderColor: '#2563eb',

                pointBorderWidth: 3

            }

        ]

    },


    options: {

        responsive: true,

        maintainAspectRatio: false,

        interaction: {

            intersect: false,

            mode: 'index'

        },


        plugins: {

            legend: {

                display: false

            },


            tooltip: {

                backgroundColor: '#0f172a',

                titleColor: '#ffffff',

                bodyColor: '#ffffff',

                padding: 12,

                cornerRadius: 8,

                displayColors: false,

                callbacks: {

                    label: function(context) {

                        return 'Rp ' +
                            new Intl.NumberFormat(
                                'id-ID'
                            ).format(
                                context.raw
                            );

                    }

                }

            }

        },


        scales: {

            x: {

                grid: {

                    display: false

                },

                border: {

                    display: false

                },

                ticks: {

                    color: '#64748b',

                    font: {

                        size: 12

                    }

                }

            },


            y: {

                beginAtZero: true,

                border: {

                    display: false

                },

                grid: {

                    color:
                        'rgba(148, 163, 184, 0.15)'

                },

                ticks: {

                    color: '#64748b',

                    font: {

                        size: 11

                    },

                    callback: function(value) {

                        if (value >= 1000000) {

                            return 'Rp ' +
                                (
                                    value / 1000000
                                ).toFixed(1) +
                                ' Jt';

                        }

                        if (value >= 1000) {

                            return 'Rp ' +
                                (
                                    value / 1000
                                ).toFixed(0) +
                                ' Rb';

                        }

                        return 'Rp ' + value;

                    }

                }

            }

        }

    }

});

</script>



<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


<script src="assets/js/mobile-menu.js"></script>
</body>

</html>

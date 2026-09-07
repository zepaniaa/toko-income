<?php

include "../config/auth.php";
include "../config/database.php";


// Ambil tanggal dari URL
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');


// Ambil data transaksi
$stmt = mysqli_prepare($conn, "
    SELECT *
    FROM penghasilan
    WHERE tanggal = ?
    ORDER BY id ASC
");

mysqli_stmt_bind_param($stmt, "s", $tanggal);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


// Hitung total
$stmt_total = mysqli_prepare($conn, "
    SELECT COALESCE(SUM(total), 0) AS total
    FROM penghasilan
    WHERE tanggal = ?
");

mysqli_stmt_bind_param($stmt_total, "s", $tanggal);
mysqli_stmt_execute($stmt_total);

$result_total = mysqli_stmt_get_result($stmt_total);

$data_total = mysqli_fetch_assoc($result_total);

$total_penghasilan = $data_total['total'];

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>
        Cetak Laporan Harian
    </title>


    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            margin-bottom: 5px;
        }

        .header p {
            margin: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #eee;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .total {
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
        }

        .tombol {
            margin-bottom: 20px;
        }

        .tombol button {
            padding: 10px 20px;
            border: none;
            background-color: #0d6efd;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }

        .tombol a {
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-left: 5px;
        }


        /*
        ------------------------------------------------
        Saat mencetak:
        tombol tidak ikut tercetak
        ------------------------------------------------
        */

        @media print {

            .tombol {
                display: none;
            }

            body {
                margin: 20px;
            }

        }

    </style>

</head>


<body>


<!-- Tombol -->

<div class="tombol">

    <button onclick="window.print()">
        🖨 Cetak Laporan
    </button>

    <a href="harian.php">
        Kembali
    </a>

</div>


<!-- Header -->

<div class="header">

    <h1>
        TOKO INCOME
    </h1>

    <h2>
        LAPORAN PENGHASILAN HARIAN
    </h2>

    <p>
        Tanggal:
        <strong>
            <?= date(
                'd-m-Y',
                strtotime($tanggal)
            ); ?>
        </strong>
    </p>

</div>


<!-- Tabel -->

<table>

    <thead>

        <tr>

            <th class="text-center">
                No
            </th>

            <th>
                Produk / Transaksi
            </th>

            <th class="text-center">
                Jumlah
            </th>

            <th class="text-right">
                Harga
            </th>

            <th class="text-right">
                Total
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

            <td class="text-center">

                <?= $no++; ?>

            </td>


            <td>

                <?= htmlspecialchars(
                    $data['nama_transaksi']
                ); ?>

            </td>


            <td class="text-center">

                <?= $data['jumlah']; ?>

            </td>


            <td class="text-right">

                Rp <?= number_format(
                    $data['harga'],
                    0,
                    ',',
                    '.'
                ); ?>

            </td>


            <td class="text-right">

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
                class="text-center"
            >

                Tidak ada transaksi.

            </td>

        </tr>

    <?php } ?>

    </tbody>


    <tfoot>

        <tr class="total">

            <td
                colspan="4"
                class="text-right"
            >

                TOTAL PENGHASILAN

            </td>

            <td class="text-right">

                Rp <?= number_format(
                    $total_penghasilan,
                    0,
                    ',',
                    '.'
                ); ?>

            </td>

            <td></td>

        </tr>

    </tfoot>

</table>


<!-- Footer -->

<div class="footer">

    <p>
        Dicetak pada:
        <?= date('d-m-Y H:i'); ?>
    </p>

</div>


</body>

</html>

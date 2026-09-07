<?php

// =====================================================
// DETEKSI DATABASE
// =====================================================

// Jika variable Railway tersedia,
// gunakan database Railway.
if (getenv('MYSQLHOST')) {

    $host = getenv('MYSQLHOST');
    $port = getenv('MYSQLPORT') ?: 3306;
    $user = getenv('MYSQLUSER');
    $password = getenv('MYSQLPASSWORD');
    $database = getenv('MYSQLDATABASE');

} else {

    // =================================================
    // DATABASE LOCALHOST XAMPP
    // =================================================

    $host = 'localhost';
    $port = 3306;
    $user = 'root';
    $password = '';
    $database = 'toko_income';

}


// =====================================================
// KONEKSI DATABASE
// =====================================================

$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database,
    $port
);


if (!$conn) {

    die(
        "Koneksi database gagal: " .
        mysqli_connect_error()
    );

}


mysqli_set_charset($conn, "utf8mb4");
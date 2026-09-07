<?php

session_start();


// Hapus semua session

$_SESSION = [];


// Hancurkan session

session_destroy();


// Kembali ke halaman login

header("Location: index.php");

exit;

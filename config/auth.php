<?php

session_start();


// Cek apakah user sudah login

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {

    header("Location: login/index.php");

    exit;
}

<?php

include "../config/auth.php";
include "../config/database.php";


/*
|--------------------------------------------------------------------------
| CEK ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id'])) {

    header("Location: index.php");

    exit;

}


$id = (int) $_GET['id'];


/*
|--------------------------------------------------------------------------
| HAPUS DATA
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare($conn, "
    DELETE FROM penghasilan
    WHERE id = ?
");


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);


mysqli_stmt_execute($stmt);


/*
|--------------------------------------------------------------------------
| KEMBALI KE DATA PENGHASILAN
|--------------------------------------------------------------------------
*/

header("Location: index.php");

exit;

?>

<?php
include "../config/auth.php";
include "../config/database.php";
$id=(int)($_GET['id']??0); if($id>0){$stmt=mysqli_prepare($conn,"DELETE FROM barang WHERE id=?"); mysqli_stmt_bind_param($stmt,'i',$id); mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt);} header('Location: index.php'); exit;

<?php
include "../config/auth.php";
include "../config/database.php";
$id=(int)($_GET['id']??0);
if($id<=0){header('Location:index.php');exit;}
$cek=mysqli_query($conn,"SHOW COLUMNS FROM penghasilan LIKE 'barang_id'");
if($cek && mysqli_num_rows($cek)===0) mysqli_query($conn,"ALTER TABLE penghasilan ADD COLUMN barang_id INT NULL AFTER id");
mysqli_begin_transaction($conn);
try{
 $stmt=mysqli_prepare($conn,"SELECT barang_id,jumlah FROM penghasilan WHERE id=? FOR UPDATE"); mysqli_stmt_bind_param($stmt,'i',$id); mysqli_stmt_execute($stmt); $d=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); mysqli_stmt_close($stmt);
 if($d && (int)$d['barang_id']>0){$stmt=mysqli_prepare($conn,"UPDATE barang SET stok=stok+? WHERE id=?");mysqli_stmt_bind_param($stmt,'ii',$d['jumlah'],$d['barang_id']);mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);}
 $stmt=mysqli_prepare($conn,"DELETE FROM penghasilan WHERE id=?");mysqli_stmt_bind_param($stmt,'i',$id);mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);mysqli_commit($conn);
}catch(Throwable $e){mysqli_rollback($conn);die('Gagal menghapus: '.$e->getMessage());}
header('Location:index.php');exit;
?>

<?php

session_start();
include "../config/koneksi.php";

$nama_mobil = $_POST['nama_mobil'];
$harga      = $_POST['harga'];
$stok       = $_POST['stok'];

$id_user = $_SESSION['id_user'];

$queryPenjual = mysqli_query($koneksi,
"SELECT * FROM penjual
WHERE id_user='$id_user'");

$dataPenjual = mysqli_fetch_assoc($queryPenjual);

$id_penjual = $dataPenjual['id_penjual'];

mysqli_query($koneksi,
"INSERT INTO mobil(
id_penjual,
nama_mobil,
harga,
stok,
status)

VALUES(
'$id_penjual',
'$nama_mobil',
'$harga',
'$stok',
'tersedia'
)");

header("Location: data_mobil.php");

?>
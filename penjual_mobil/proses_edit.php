<?php

include "../config/koneksi.php";

$id_mobil = $_POST['id_mobil'];
$nama_mobil = $_POST['nama_mobil'];
$harga = $_POST['harga'];
$stok = $_POST['stok'];
$status = $_POST['status'];

$query = mysqli_query($koneksi,
"UPDATE mobil SET

nama_mobil='$nama_mobil',
harga='$harga',
stok='$stok',
status='$status'

WHERE id_mobil='$id_mobil'");

if($query){

    header("Location: data_mobil.php");

}else{

    echo "Gagal update data!";

}

?>
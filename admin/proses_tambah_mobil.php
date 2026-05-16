<?php

session_start();
include "../config/koneksi.php";

/*
|--------------------------------------------------------------------------
| CEK LOGIN
|--------------------------------------------------------------------------
*/

if($_SESSION['role'] != "admin"){

    header("Location: ../auth/login.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| AMBIL DATA FORM
|--------------------------------------------------------------------------
*/

$nama_mobil = $_POST['nama_mobil'];
$harga      = $_POST['harga'];
$stok       = $_POST['stok'];

/*
|--------------------------------------------------------------------------
| UPLOAD FOTO
|--------------------------------------------------------------------------
*/

$namaFoto = $_FILES['foto']['name'];
$tmpFoto  = $_FILES['foto']['tmp_name'];

move_uploaded_file($tmpFoto, "../uploads/" . $namaFoto);

/*
|--------------------------------------------------------------------------
| CEK PENJUAL
|--------------------------------------------------------------------------
*/

$queryPenjual = mysqli_query($koneksi,
"SELECT * FROM penjual LIMIT 1");

$dataPenjual = mysqli_fetch_assoc($queryPenjual);

/*
|--------------------------------------------------------------------------
| JIKA BELUM ADA PENJUAL
|--------------------------------------------------------------------------
*/

if(!$dataPenjual){

    echo "
    <script>
        alert('Data penjual belum ada!');
        window.location='tambah_mobil.php';
    </script>
    ";

    exit;
}

$id_penjual = $dataPenjual['id_penjual'];

/*
|--------------------------------------------------------------------------
| STATUS MOBIL
|--------------------------------------------------------------------------
*/

$status = ($stok > 0) ? 'tersedia' : 'terjual';

/*
|--------------------------------------------------------------------------
| INSERT DATA MOBIL
|--------------------------------------------------------------------------
*/

mysqli_query($koneksi,
"INSERT INTO mobil(

    id_penjual,
    nama_mobil,
    harga,
    stok,
    status,
    foto

) VALUES(

    '$id_penjual',
    '$nama_mobil',
    '$harga',
    '$stok',
    '$status',
    '$namaFoto'

)");

/*
|--------------------------------------------------------------------------
| REDIRECT
|--------------------------------------------------------------------------
*/

header("Location: data_mobil_admin.php?tambah=success");
exit;

?>
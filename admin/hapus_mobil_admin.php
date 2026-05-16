<?php

session_start();
include "../config/koneksi.php";

/*
|--------------------------------------------------------------------------
| CEK LOGIN ADMIN
|--------------------------------------------------------------------------
*/

if($_SESSION['role'] != "admin"){

    header("Location: ../auth/login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| CEK ID MOBIL
|--------------------------------------------------------------------------
*/

if(!isset($_GET['id'])){

    header("Location: data_mobil_admin.php");
    exit;
}

$id_mobil = $_GET['id'];


/*
|--------------------------------------------------------------------------
| CEK DATA MOBIL
|--------------------------------------------------------------------------
*/

$queryMobil = mysqli_query($koneksi,
"SELECT * FROM mobil
WHERE id_mobil='$id_mobil'");

$dataMobil = mysqli_fetch_assoc($queryMobil);

if(!$dataMobil){

    echo "
    <script>
        alert('Data mobil tidak ditemukan!');
        window.location='data_mobil_admin.php';
    </script>
    ";

    exit;
}


/*
|--------------------------------------------------------------------------
| CEK APAKAH MOBIL SUDAH ADA PEMESANAN
|--------------------------------------------------------------------------
*/

$queryPemesanan = mysqli_query($koneksi,
"SELECT * FROM pemesanan
WHERE id_mobil='$id_mobil'");

$cekPemesanan = mysqli_num_rows($queryPemesanan);


/*
|--------------------------------------------------------------------------
| JIKA SUDAH ADA PEMESANAN
|--------------------------------------------------------------------------
*/

if($cekPemesanan > 0){

    echo "
    <script>
        alert('Mobil tidak bisa dihapus karena sudah ada pemesanan!');
        window.location='data_mobil_admin.php';
    </script>
    ";

    exit;
}


/*
|--------------------------------------------------------------------------
| HAPUS DATA MOBIL
|--------------------------------------------------------------------------
*/

$hapus = mysqli_query($koneksi,
"DELETE FROM mobil
WHERE id_mobil='$id_mobil'");


/*
|--------------------------------------------------------------------------
| JIKA BERHASIL
|--------------------------------------------------------------------------
*/

if($hapus){

    echo "
    <script>
        alert('Data mobil berhasil dihapus!');
        window.location='data_mobil_admin.php';
    </script>
    ";

} else {

    echo "
    <script>
        alert('Gagal menghapus data mobil!');
        window.location='data_mobil_admin.php';
    </script>
    ";

}

?>
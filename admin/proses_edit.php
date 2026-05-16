<?php

session_start();
include "../config/koneksi.php";

/*
|--------------------------------------------------------------------------
| AMBIL DATA FORM
|--------------------------------------------------------------------------
*/

$id_mobil   = $_POST['id_mobil'];
$nama_mobil = $_POST['nama_mobil'];
$deskripsi  = $_POST['deskripsi'];
$tahun      = $_POST['tahun'];
$harga      = $_POST['harga'];
$stok       = $_POST['stok'];
$status     = $_POST['status'];

$foto_lama = $_POST['foto_lama'];


/*
|--------------------------------------------------------------------------
| CEK FOTO BARU
|--------------------------------------------------------------------------
*/

if($_FILES['foto']['name'] != ""){

    $nama_file = $_FILES['foto']['name'];
    $tmp_file  = $_FILES['foto']['tmp_name'];

    // nama unik
    $foto_baru = time().'_'.$nama_file;

    // folder upload
    $path = "../uploads/".$foto_baru;

    // upload foto
    if(move_uploaded_file($tmp_file, $path)){

        // hapus foto lama
        if(file_exists("../uploads/".$foto_lama)){

            unlink("../uploads/".$foto_lama);

        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE DENGAN FOTO BARU
        |--------------------------------------------------------------------------
        */

        $update = mysqli_query($koneksi,

        "UPDATE mobil SET

        nama_mobil = '$nama_mobil',
        deskripsi  = '$deskripsi',
        tahun      = '$tahun',
        harga      = '$harga',
        stok       = '$stok',
        status     = '$status',
        foto       = '$foto_baru'

        WHERE id_mobil = '$id_mobil'

        ");

    } else {

        echo "
        <script>

            alert('Upload foto gagal!');

            window.location='edit_mobil.php?id=$id_mobil';

        </script>
        ";

        exit;

    }

} else {

    /*
    |--------------------------------------------------------------------------
    | UPDATE TANPA GANTI FOTO
    |--------------------------------------------------------------------------
    */

    $update = mysqli_query($koneksi,

    "UPDATE mobil SET

    nama_mobil = '$nama_mobil',
    deskripsi  = '$deskripsi',
    tahun      = '$tahun',
    harga      = '$harga',
    stok       = '$stok',
    status     = '$status'

    WHERE id_mobil = '$id_mobil'

    ");

}


/*
|--------------------------------------------------------------------------
| CEK UPDATE
|--------------------------------------------------------------------------
*/

if($update){

    echo "
    <script>

        alert('Data mobil berhasil diupdate!');

        window.location='data_mobil_admin.php';

    </script>
    ";

} else {

    echo "
    <script>

        alert('Data gagal diupdate!');

        window.location='edit_mobil.php?id=$id_mobil';

    </script>
    ";

}

?>
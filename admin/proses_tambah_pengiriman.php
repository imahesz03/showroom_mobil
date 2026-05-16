<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../auth/login.php");
    exit;
}

if(!isset($_POST['simpan'])){
    header("Location: tambah_pengiriman.php");
    exit;
}

$id_pemesanan = mysqli_real_escape_string($koneksi, $_POST['id_pemesanan']);
$id_kurir     = mysqli_real_escape_string($koneksi, $_POST['id_kurir']);
$alamat_kirim = mysqli_real_escape_string($koneksi, $_POST['alamat_kirim']);
$status       = mysqli_real_escape_string($koneksi, $_POST['status']);

if(empty($id_pemesanan) || empty($id_kurir) || empty($alamat_kirim) || empty($status)){
    $_SESSION['error'] = "Semua field wajib diisi.";
    header("Location: tambah_pengiriman.php");
    exit;
}

/* CEK APAKAH PEMESANAN VALID DAN LUNAS */
$cek_pemesanan = mysqli_query($koneksi, "
    SELECT id_pemesanan, status
    FROM pemesanan
    WHERE id_pemesanan='$id_pemesanan'
    LIMIT 1
");

if(!$cek_pemesanan){
    die("Query pemesanan error: " . mysqli_error($koneksi));
}

$data_pemesanan = mysqli_fetch_assoc($cek_pemesanan);

if(!$data_pemesanan){
    $_SESSION['error'] = "Data pemesanan tidak ditemukan.";
    header("Location: tambah_pengiriman.php");
    exit;
}

if($data_pemesanan['status'] != 'lunas'){
    $_SESSION['error'] = "Pengiriman hanya bisa dibuat untuk pemesanan yang sudah lunas.";
    header("Location: tambah_pengiriman.php");
    exit;
}

/* CEK APAKAH SUDAH ADA PENGIRIMAN */
$cek_pengiriman = mysqli_query($koneksi, "
    SELECT id_pengiriman
    FROM pengiriman
    WHERE id_pemesanan='$id_pemesanan'
    LIMIT 1
");

if(mysqli_num_rows($cek_pengiriman) > 0){
    $_SESSION['error'] = "Pengiriman untuk pemesanan ini sudah ada.";
    header("Location: tambah_pengiriman.php");
    exit;
}

/* CEK KURIR */
$cek_kurir = mysqli_query($koneksi, "
    SELECT id_kurir
    FROM kurir
    WHERE id_kurir='$id_kurir'
    LIMIT 1
");

if(mysqli_num_rows($cek_kurir) == 0){
    $_SESSION['error'] = "Data kurir tidak ditemukan.";
    header("Location: tambah_pengiriman.php");
    exit;
}

/* SIMPAN DATA */
$simpan = mysqli_query($koneksi, "
    INSERT INTO pengiriman
    (id_pemesanan, id_kurir, alamat_kirim, status)
    VALUES
    ('$id_pemesanan', '$id_kurir', '$alamat_kirim', '$status')
");

if(!$simpan){
    die("Gagal menyimpan pengiriman: " . mysqli_error($koneksi));
}

$_SESSION['success'] = "Data pengiriman berhasil ditambahkan.";
header("Location: pengiriman_mobil.php");
exit;
?>
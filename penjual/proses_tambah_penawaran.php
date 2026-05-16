<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "penjual"){
    header("Location: ../auth/login.php");
    exit;
}

if(!isset($_POST['simpan'])){
    header("Location: tambah_penawaran.php");
    exit;
}

$id_user = $_SESSION['id_user'];

/*
|------------------------------------------------------
| AMBIL ID PENJUAL
|------------------------------------------------------
*/
$q_penjual = mysqli_query($koneksi, "
    SELECT id_penjual
    FROM penjual
    WHERE id_user='$id_user'
    LIMIT 1
");

if(!$q_penjual){
    die("Query penjual error: " . mysqli_error($koneksi));
}

$penjual = mysqli_fetch_assoc($q_penjual);

if(!$penjual){
    $_SESSION['error'] = "Data penjual tidak ditemukan.";
    header("Location: tambah_penawaran.php");
    exit;
}

$id_penjual = $penjual['id_penjual'];

/*
|------------------------------------------------------
| PASTIKAN KOLOM TAMBAHAN ADA
|------------------------------------------------------
*/
$columns = [
    "status" => "ALTER TABLE penawaran ADD status ENUM('menunggu','diterima','ditolak') DEFAULT 'menunggu'",
    "catatan" => "ALTER TABLE penawaran ADD catatan TEXT DEFAULT NULL",
    "metode_pembayaran" => "ALTER TABLE penawaran ADD metode_pembayaran ENUM('tunai','transfer') DEFAULT NULL",
    "bukti_pembayaran" => "ALTER TABLE penawaran ADD bukti_pembayaran VARCHAR(255) DEFAULT NULL",
    "tanggal_keputusan" => "ALTER TABLE penawaran ADD tanggal_keputusan DATETIME DEFAULT NULL",
    "catatan_admin" => "ALTER TABLE penawaran ADD catatan_admin TEXT DEFAULT NULL"
];

foreach($columns as $col => $sql){
    $cek = mysqli_query($koneksi, "SHOW COLUMNS FROM penawaran LIKE '$col'");
    if($cek && mysqli_num_rows($cek) == 0){
        mysqli_query($koneksi, $sql);
    }
}

/*
|------------------------------------------------------
| AMBIL DATA FORM
|------------------------------------------------------
*/
$id_mobil    = mysqli_real_escape_string($koneksi, $_POST['id_mobil']);
$harga_tawar = mysqli_real_escape_string($koneksi, $_POST['harga_tawar']);
$catatan     = mysqli_real_escape_string($koneksi, $_POST['catatan'] ?? '');

if(empty($id_mobil) || empty($harga_tawar)){
    $_SESSION['error'] = "Mobil dan harga tawar wajib diisi.";
    header("Location: tambah_penawaran.php");
    exit;
}

if($harga_tawar <= 0){
    $_SESSION['error'] = "Harga tawar tidak valid.";
    header("Location: tambah_penawaran.php");
    exit;
}

/*
|------------------------------------------------------
| CEK MOBIL
|------------------------------------------------------
*/
$cek_mobil = mysqli_query($koneksi, "
    SELECT id_mobil
    FROM mobil
    WHERE id_mobil='$id_mobil'
    LIMIT 1
");

if(!$cek_mobil){
    die("Query mobil error: " . mysqli_error($koneksi));
}

if(mysqli_num_rows($cek_mobil) == 0){
    $_SESSION['error'] = "Mobil tidak ditemukan.";
    header("Location: tambah_penawaran.php");
    exit;
}

/*
|------------------------------------------------------
| CEK APAKAH MOBIL SUDAH PERNAH DITAWARKAN
|------------------------------------------------------
*/
$cek_penawaran = mysqli_query($koneksi, "
    SELECT id_penawaran
    FROM penawaran
    WHERE id_mobil='$id_mobil'
    LIMIT 1
");

if(mysqli_num_rows($cek_penawaran) > 0){
    $_SESSION['error'] = "Mobil ini sudah pernah ditawarkan, jadi tidak bisa ditawarkan lagi.";
    header("Location: tambah_penawaran.php");
    exit;
}

/*
|------------------------------------------------------
| SIMPAN PENAWARAN
|------------------------------------------------------
*/
$simpan = mysqli_query($koneksi, "
    INSERT INTO penawaran
    (
        id_penjual,
        id_mobil,
        harga_tawar,
        tanggal,
        status,
        catatan
    )
    VALUES
    (
        '$id_penjual',
        '$id_mobil',
        '$harga_tawar',
        CURDATE(),
        'menunggu',
        '$catatan'
    )
");

if(!$simpan){
    die("Gagal menyimpan penawaran: " . mysqli_error($koneksi));
}

$_SESSION['success'] = "Penawaran berhasil dikirim ke admin.";
header("Location: penawaran_mobil.php");
exit;
?>
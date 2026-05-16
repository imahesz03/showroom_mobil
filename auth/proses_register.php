<?php
session_start();
include "../config/koneksi.php";

if(!isset($_POST['register'])){
    header("Location: register.php");
    exit;
}

$nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$no_hp    = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
$alamat   = mysqli_real_escape_string($koneksi, $_POST['alamat']);
$role     = mysqli_real_escape_string($koneksi, $_POST['role']);

$password             = $_POST['password'];
$konfirmasi_password  = $_POST['konfirmasi_password'];

$roleValid = ['pembeli', 'penjual', 'kurir'];

if(!in_array($role, $roleValid)){
    $_SESSION['error'] = "Role tidak valid.";
    header("Location: register.php");
    exit;
}

if($password != $konfirmasi_password){
    $_SESSION['error'] = "Konfirmasi password tidak sama.";
    header("Location: register.php");
    exit;
}

if(strlen($password) < 6){
    $_SESSION['error'] = "Password minimal 6 karakter.";
    header("Location: register.php");
    exit;
}

$cek_username = mysqli_query($koneksi, "
    SELECT id_user 
    FROM users 
    WHERE username='$username'
");

if(mysqli_num_rows($cek_username) > 0){
    $_SESSION['error'] = "Username sudah digunakan.";
    header("Location: register.php");
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$simpan_user = mysqli_query($koneksi, "
    INSERT INTO users 
    (username, password, role)
    VALUES
    ('$username', '$password_hash', '$role')
");

if(!$simpan_user){
    die("Gagal menyimpan user: " . mysqli_error($koneksi));
}

$id_user = mysqli_insert_id($koneksi);

/* SIMPAN DETAIL SESUAI ROLE */
if($role == "pembeli"){
    $simpan_detail = mysqli_query($koneksi, "
        INSERT INTO pembeli
        (id_user, nama, alamat, no_hp, foto)
        VALUES
        ('$id_user', '$nama', '$alamat', '$no_hp', NULL)
    ");
} elseif($role == "penjual"){
    $simpan_detail = mysqli_query($koneksi, "
        INSERT INTO penjual
        (id_user, nama, alamat, no_hp, foto)
        VALUES
        ('$id_user', '$nama', '$alamat', '$no_hp', NULL)
    ");
} elseif($role == "kurir"){
    $simpan_detail = mysqli_query($koneksi, "
        INSERT INTO kurir
        (id_user, nama, alamat, no_hp, foto)
        VALUES
        ('$id_user', '$nama', '$alamat', '$no_hp', NULL)
    ");
}

if(!$simpan_detail){
    mysqli_query($koneksi, "DELETE FROM users WHERE id_user='$id_user'");
    die("Gagal menyimpan detail user: " . mysqli_error($koneksi));
}

$_SESSION['success'] = "Registrasi berhasil. Silakan login.";
header("Location: login.php");
exit;
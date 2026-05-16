<?php
session_start();
include "../config/koneksi.php";

if(!isset($_POST['login'])){
    header("Location: login.php");
    exit;
}

$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = $_POST['password'];

$query = mysqli_query($koneksi, "
    SELECT * FROM users 
    WHERE username='$username' 
    LIMIT 1
");

if(!$query){
    die("Query error: " . mysqli_error($koneksi));
}

$user = mysqli_fetch_assoc($query);

if(!$user){
    $_SESSION['error'] = "Username tidak ditemukan.";
    header("Location: login.php");
    exit;
}

if(!password_verify($password, $user['password'])){
    $_SESSION['error'] = "Password salah.";
    header("Location: login.php");
    exit;
}

$_SESSION['id_user']  = $user['id_user'];
$_SESSION['username'] = $user['username'];
$_SESSION['role']     = $user['role'];

/* Ambil foto profil sesuai role */
$role = $user['role'];
$id_user = $user['id_user'];

$detailTable = [
    'admin'   => 'admin',
    'pembeli' => 'pembeli',
    'penjual' => 'penjual',
    'kurir'   => 'kurir'
];

if(isset($detailTable[$role])){
    $tabel = $detailTable[$role];

    $q_detail = mysqli_query($koneksi, "
        SELECT foto 
        FROM $tabel 
        WHERE id_user='$id_user' 
        LIMIT 1
    ");

    $detail = mysqli_fetch_assoc($q_detail);

    if(!empty($detail['foto'])){
        $_SESSION['foto_profil'] = $detail['foto'];
    }
}

if($role == "admin"){
    header("Location: ../dashboard/admin.php");
} elseif($role == "pembeli"){
    header("Location: ../dashboard/pembeli.php");
} elseif($role == "penjual"){
    header("Location: ../dashboard/penjual.php");
} elseif($role == "kurir"){
    header("Location: ../dashboard/kurir.php");
} else {
    $_SESSION['error'] = "Role tidak valid.";
    header("Location: login.php");
}

exit;
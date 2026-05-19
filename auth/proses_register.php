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

$password            = $_POST['password'];
$konfirmasi_password = $_POST['konfirmasi_password'];

$roleValid = ['pembeli', 'penjual', 'kurir'];

/* VALIDASI ROLE */
if(!in_array($role, $roleValid)){

    $_SESSION['error'] = "Role tidak valid.";
    header("Location: register.php");
    exit;
}

/* VALIDASI PASSWORD */
if($password != $konfirmasi_password){

    $_SESSION['error'] = "Konfirmasi password tidak sama.";
    header("Location: register.php");
    exit;
}

/* PASSWORD MINIMAL */
if(strlen($password) < 6){

    $_SESSION['error'] = "Password minimal 6 karakter.";
    header("Location: register.php");
    exit;
}

/* CEK USERNAME */
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

/* HASH PASSWORD */
$password_hash = password_hash($password, PASSWORD_DEFAULT);

/* SIMPAN USER */
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

/* SIMPAN DETAIL */
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

/* GAGAL SIMPAN DETAIL */
if(!$simpan_detail){

    mysqli_query($koneksi, "
        DELETE FROM users 
        WHERE id_user='$id_user'
    ");

    die("Gagal menyimpan detail user: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <title>Proses Registrasi</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>

        body{
            margin:0;
            padding:0;
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background:#f8f9fc;
            font-family:Arial, sans-serif;
        }

        .loading{
            text-align:center;
        }

        .spinner{
            width:60px;
            height:60px;
            border:6px solid #ddd;
            border-top:6px solid #4e73df;
            border-radius:50%;
            animation:spin 1s linear infinite;
            margin:auto;
            margin-bottom:20px;
        }

        @keyframes spin{
            100%{
                transform:rotate(360deg);
            }
        }

        .text{
            color:#555;
            font-size:18px;
        }

    </style>

</head>

<body>

<div class="loading">

    <div class="spinner"></div>

    <div class="text">
        Memproses registrasi...
    </div>

</div>

<script>

Swal.fire({
    icon: 'success',
    title: 'Registrasi Berhasil',
    text: 'Silakan login ke akun Anda',
    showConfirmButton: false,
    timer: 1800
}).then(() => {

    window.location.href='login.php';

});

</script>

</body>
</html>
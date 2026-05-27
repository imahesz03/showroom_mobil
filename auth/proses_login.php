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

/* USERNAME TIDAK DITEMUKAN */
if(!$user){

    $_SESSION['error'] = "Username tidak ditemukan";
    header("Location: login.php");
    exit;

}

/* PASSWORD SALAH */
elseif(!password_verify($password, $user['password'])){

    $_SESSION['error'] = "Password salah";
    header("Location: login.php");
    exit;

}

/* LOGIN BERHASIL */
else{

    $_SESSION['id_user']  = $user['id_user'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role']     = $user['role'];

    $role = $user['role'];
    $id_user = $user['id_user'];

    /* FOTO PROFIL */
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

    $icon = "success";
    $title = "Login Berhasil";
    $text = "Selamat datang ".$user['username'];

    if($role == "admin"){
        $redirect = "../admin/dashboard.php";

    } elseif($role == "pembeli"){
        $redirect = "../pembeli/dashboard.php";

    } elseif($role == "penjual"){
        $redirect = "../penjual/dashboard.php";

    } elseif($role == "kurir"){
        $redirect = "../kurir/dashboard.php";

    } else {

        $icon = "error";
        $title = "Role Tidak Valid";
        $text = "Role tidak ditemukan";
        $redirect = "login.php";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <title>Proses Login</title>

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
        Memproses login...
    </div>

</div>

<script>

Swal.fire({
    icon: '<?= $icon ?>',
    title: '<?= $title ?>',
    text: '<?= $text ?>',
    showConfirmButton: false,
    timer: 1800
}).then(() => {

    window.location.href='<?= $redirect ?>';

});

</script>

</body>
</html>
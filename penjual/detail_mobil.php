<?php
session_start();
include "../config/koneksi.php";

// Pastikan akses hanya untuk penjual
if(!isset($_SESSION['role']) || $_SESSION['role'] != "penjual"){
    header("Location: ../auth/login.php");
    exit;
}

// Ambil ID dari URL
if(!isset($_GET['id']) || empty($_GET['id'])){
    header("Location: penawaran_mobil.php");
    exit;
}

$id_mobil = mysqli_real_escape_string($koneksi, $_GET['id']);
$id_user = $_SESSION['id_user'];

// Query disesuaikan dengan struktur tabel:
// 1. Menggunakan p.nama (bukan p.nama_penjual)
// 2. Memastikan hanya pemilik mobil yang bisa melihat detailnya
$query = mysqli_query($koneksi, "
    SELECT m.*, p.nama AS nama_penjual 
    FROM mobil m 
    JOIN penjual p ON m.id_penjual = p.id_penjual
    WHERE m.id_mobil = '$id_mobil' 
    AND p.id_user = '$id_user'
");

$data = mysqli_fetch_assoc($query);

if(!$data){
    die("Mobil tidak ditemukan atau Anda tidak memiliki akses ke data ini.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Mobil - <?= htmlspecialchars($data['nama_mobil']) ?></title>
    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">

</head>
<body id="page-top">
<div id="wrapper">
    <?php include "../includes/sidebar_penjual.php"; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include "../includes/topbar.php"; ?>
            <div class="container-fluid">
                
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Detail Mobil</h1>
                    <a href="penawaran_mobil.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5 text-center">
                                <?php 
                                // Pastikan path folder benar sesuai struktur Anda
                                $foto = !empty($data['foto']) ? "../uploads/" . $data['foto'] : "../assets/img/no-image.png"; 
                                ?>
                                <img src="<?= $foto ?>" class="img-fluid rounded shadow-sm" alt="Foto Mobil" style="max-height: 400px; object-fit: cover;">
                            </div>
                            <div class="col-md-7">
                                <h2 class="text-primary font-weight-bold"><?= htmlspecialchars($data['nama_mobil']) ?></h2>
                                <hr>
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="200">Tahun Produksi</th>
                                        <td>: <?= $data['tahun'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Harga</th>
                                        <td class="font-weight-bold text-success">: Rp <?= number_format($data['harga'], 0, ',', '.') ?></td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>: <span class="badge badge-info"><?= ucfirst($data['status']) ?></span></td>
                                    </tr>
                                    <tr>
                                        <th>Deskripsi</th>
                                        <td style="text-align: justify;">: <?= nl2br(htmlspecialchars($data['deskripsi'])) ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
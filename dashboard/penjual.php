<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "penjual"){
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

$q_penjual = mysqli_query($koneksi, "
    SELECT * FROM penjual 
    WHERE id_user='$id_user'
    LIMIT 1
");

$penjual = mysqli_fetch_assoc($q_penjual);

if(!$penjual){
    die("Data penjual tidak ditemukan.");
}

$id_penjual = $penjual['id_penjual'];

$q_menunggu = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM penawaran 
    WHERE id_penjual='$id_penjual' 
    AND status='menunggu'
");

$q_diterima = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM penawaran 
    WHERE id_penjual='$id_penjual' 
    AND status='diterima'
");

$q_ditolak = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM penawaran 
    WHERE id_penjual='$id_penjual' 
    AND status='ditolak'
");

$menunggu = mysqli_fetch_assoc($q_menunggu)['total'] ?? 0;
$diterima = mysqli_fetch_assoc($q_diterima)['total'] ?? 0;
$ditolak  = mysqli_fetch_assoc($q_ditolak)['total'] ?? 0;

$data = mysqli_query($koneksi, "
    SELECT 
        pn.*,
        m.nama_mobil,
        m.tahun,
        m.harga,
        m.foto
    FROM penawaran pn
    LEFT JOIN mobil m ON pn.id_mobil = m.id_mobil
    WHERE pn.id_penjual='$id_penjual'
    ORDER BY pn.id_penawaran DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Penjual</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .table td{ vertical-align:middle; font-size:14px; }
        .table th{ font-size:14px; white-space:nowrap; }
        .badge{ font-size:12px; padding:6px 9px; }
        .mobil-img{
            width:70px;
            height:50px;
            object-fit:cover;
            border-radius:8px;
        }
    </style>
</head>

<body id="page-top">

<div id="wrapper">

    <?php include "../includes/sidebar_penjual.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php $pageTitle = "Dashboard Penjual"; include "../includes/topbar.php"; ?>

            <div class="container-fluid">

                <h1 class="h3 mb-4 text-gray-800">Dashboard Penjual</h1>

                <div class="alert alert-info">
                    Selamat datang, <strong><?= htmlspecialchars($penjual['nama']); ?></strong>.
                    Di sini kamu bisa melihat status penawaran mobil kamu.
                </div>

                <div class="row">

                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Menunggu
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $menunggu; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Diterima
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $diterima; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                            Ditolak
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $ditolak; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card shadow mb-4">

                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Penawaran Terbaru
                        </h6>

                        <a href="../penjual/penawaran_mobil.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-handshake"></i> Kelola Penawaran
                        </a>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover text-center">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Foto</th>
                                        <th>Mobil</th>
                                        <th>Harga Tawar</th>
                                        <th>Status</th>
                                        <th>Metode</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php if(mysqli_num_rows($data) > 0){ ?>
                                        <?php 
                                        $no = 1;
                                        while($row = mysqli_fetch_assoc($data)){ 

                                            $status = $row['status'] ?? 'menunggu';

                                            if($status == 'diterima'){
                                                $badge = 'success';
                                                $text = 'Diterima';
                                            } elseif($status == 'ditolak'){
                                                $badge = 'danger';
                                                $text = 'Ditolak';
                                            } else {
                                                $badge = 'warning';
                                                $text = 'Menunggu';
                                            }

                                            $foto = !empty($row['foto']) ? "../uploads/" . $row['foto'] : "../assets/img/no-image.png";
                                        ?>

                                        <tr>
                                            <td><?= $no++; ?></td>

                                            <td>
                                                <img src="<?= htmlspecialchars($foto); ?>" class="mobil-img">
                                            </td>

                                            <td>
                                                <strong><?= htmlspecialchars($row['nama_mobil'] ?? '-'); ?></strong><br>
                                                <small>Tahun <?= htmlspecialchars($row['tahun'] ?? '-'); ?></small>
                                            </td>

                                            <td>
                                                Rp <?= number_format($row['harga_tawar'] ?? 0, 0, ',', '.'); ?>
                                            </td>

                                            <td>
                                                <span class="badge badge-<?= $badge; ?>">
                                                    <?= $text; ?>
                                                </span>
                                            </td>

                                            <td>
                                                <?= !empty($row['metode_pembayaran']) ? ucfirst($row['metode_pembayaran']) : '-'; ?>
                                            </td>
                                        </tr>

                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                Belum ada penawaran mobil.
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../assets/js/sb-admin-2.min.js"></script>

</body>
</html>
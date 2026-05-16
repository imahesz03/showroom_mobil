<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "kurir"){
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

/*
|------------------------------------------------------
| AMBIL DATA KURIR LOGIN
|------------------------------------------------------
*/
$q_kurir = mysqli_query($koneksi, "
    SELECT * FROM kurir 
    WHERE id_user='$id_user'
    LIMIT 1
");

if(!$q_kurir){
    die("Query kurir error: " . mysqli_error($koneksi));
}

$kurir = mysqli_fetch_assoc($q_kurir);

if(!$kurir){
    die("Data kurir tidak ditemukan. Pastikan akun kurir sudah ada di tabel kurir.");
}

$id_kurir = $kurir['id_kurir'];

/*
|------------------------------------------------------
| HITUNG DATA DASHBOARD
|------------------------------------------------------
*/
$q_diproses = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM pengiriman 
    WHERE id_kurir='$id_kurir'
    AND status='diproses'
");

$q_dikirim = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM pengiriman 
    WHERE id_kurir='$id_kurir'
    AND status='dikirim'
");

$q_terkirim = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM pengiriman 
    WHERE id_kurir='$id_kurir'
    AND (status='terkirim' OR status='selesai')
");

$diproses  = mysqli_fetch_assoc($q_diproses)['total'] ?? 0;
$dikirim   = mysqli_fetch_assoc($q_dikirim)['total'] ?? 0;
$terkirim  = mysqli_fetch_assoc($q_terkirim)['total'] ?? 0;

/*
|------------------------------------------------------
| AMBIL DATA TERBARU
|------------------------------------------------------
*/
$data_pengiriman = mysqli_query($koneksi, "
    SELECT 
        pg.id_pengiriman,
        pg.alamat_kirim,
        pg.status,
        pg.tanggal_terkirim,

        p.id_pemesanan,
        p.total_harga,

        b.nama AS nama_pembeli,
        b.no_hp AS no_hp_pembeli,

        m.nama_mobil,
        m.tahun

    FROM pengiriman pg
    JOIN pemesanan p ON pg.id_pemesanan = p.id_pemesanan
    JOIN pembeli b ON p.id_pembeli = b.id_pembeli
    JOIN mobil m ON p.id_mobil = m.id_mobil

    WHERE pg.id_kurir='$id_kurir'

    ORDER BY pg.id_pengiriman DESC
    LIMIT 5
");

if(!$data_pengiriman){
    die("Query pengiriman error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Kurir</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .table td{
            vertical-align: middle;
            font-size: 14px;
        }

        .table th{
            font-size: 14px;
            white-space: nowrap;
        }

        .badge{
            font-size: 12px;
            padding: 6px 9px;
        }

        .small-text{
            font-size: 12px;
            color: #858796;
        }
    </style>
</head>

<body id="page-top">

<div id="wrapper">

    <?php include "../includes/sidebar_kurir.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php 
            $pageTitle = "Dashboard Kurir";
            include "../includes/topbar.php"; 
            ?>

            <div class="container-fluid">

                <h1 class="h3 mb-4 text-gray-800">
                    Dashboard Kurir
                </h1>

                <div class="alert alert-info">
                    Selamat datang, <strong><?= htmlspecialchars($kurir['nama']); ?></strong>.
                    Berikut ringkasan tugas pengiriman mobil kamu.
                </div>

                <div class="row">

                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">

                                <div class="row no-gutters align-items-center">

                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Mobil Akan Dikirim
                                        </div>

                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $diproses; ?>
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
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">

                                <div class="row no-gutters align-items-center">

                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Sedang Dikirim
                                        </div>

                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $dikirim; ?>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <i class="fas fa-truck fa-2x text-gray-300"></i>
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
                                            Selesai Dikirim
                                        </div>

                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $terkirim; ?>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <div class="card shadow mb-4">

                    <div class="card-header py-3 d-flex justify-content-between align-items-center">

                        <h6 class="m-0 font-weight-bold text-primary">
                            Pengiriman Terbaru
                        </h6>

                        <a href="../kurir/pengiriman_mobil.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-truck"></i>
                            Lihat Semua
                        </a>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover text-center" width="100%" cellspacing="0">

                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Pembeli</th>
                                        <th>Mobil</th>
                                        <th>Alamat</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php if(mysqli_num_rows($data_pengiriman) > 0){ ?>

                                        <?php 
                                        $no = 1;
                                        while($row = mysqli_fetch_assoc($data_pengiriman)){ 

                                            if($row['status'] == 'diproses'){
                                                $badge = 'warning';
                                                $textStatus = 'Akan Dikirim';
                                            } elseif($row['status'] == 'dikirim'){
                                                $badge = 'info';
                                                $textStatus = 'Sedang Dikirim';
                                            } elseif($row['status'] == 'terkirim' || $row['status'] == 'selesai'){
                                                $badge = 'success';
                                                $textStatus = 'Selesai';
                                            } else {
                                                $badge = 'secondary';
                                                $textStatus = ucfirst($row['status']);
                                            }
                                        ?>

                                        <tr>
                                            <td><?= $no++; ?></td>

                                            <td>
                                                <strong><?= htmlspecialchars($row['nama_pembeli']); ?></strong><br>
                                                <small class="small-text">
                                                    <?= htmlspecialchars($row['no_hp_pembeli']); ?>
                                                </small>
                                            </td>

                                            <td>
                                                <strong><?= htmlspecialchars($row['nama_mobil']); ?></strong><br>
                                                <small class="small-text">
                                                    Tahun <?= htmlspecialchars($row['tahun']); ?>
                                                </small>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['alamat_kirim']); ?>
                                            </td>

                                            <td>
                                                Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?>
                                            </td>

                                            <td>
                                                <span class="badge badge-<?= $badge; ?>">
                                                    <?= $textStatus; ?>
                                                </span>

                                                <?php if(!empty($row['tanggal_terkirim'])){ ?>
                                                    <br>
                                                    <small class="small-text">
                                                        <?= date('d-m-Y H:i', strtotime($row['tanggal_terkirim'])); ?>
                                                    </small>
                                                <?php } ?>
                                            </td>
                                        </tr>

                                        <?php } ?>

                                    <?php } else { ?>

                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                Belum ada data pengiriman untuk kurir ini.
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
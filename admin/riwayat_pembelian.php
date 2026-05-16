<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../auth/login.php");
    exit;
}

$id = $_GET['id'] ?? null;

if(!$id){
    header("Location: data_pembeli_admin.php");
    exit;
}

$id = mysqli_real_escape_string($koneksi, $id);

/*
|----------------------------------
| AMBIL DATA PEMBELI
|----------------------------------
*/
$pembeli = mysqli_query($koneksi,
"SELECT * FROM pembeli WHERE id_pembeli='$id'");

$dataPembeli = mysqli_fetch_assoc($pembeli);

if(!$dataPembeli){
    header("Location: data_pembeli_admin.php");
    exit;
}

/*
|----------------------------------
| AMBIL RIWAYAT PEMBELIAN
|----------------------------------
*/
$query = mysqli_query($koneksi,
"SELECT 
    p.id_pemesanan,
    p.tanggal_pesan,
    p.total_harga,
    p.status AS status_pemesanan,

    m.nama_mobil,

    py.metode_bayar,
    py.jumlah,
    py.status AS status_pembayaran,
    py.bukti_pembayaran

FROM pemesanan p
JOIN mobil m ON p.id_mobil = m.id_mobil
LEFT JOIN pembayaran py ON p.id_pemesanan = py.id_pemesanan
WHERE p.id_pembeli='$id'
ORDER BY p.id_pemesanan DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pembelian</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

<div id="wrapper">

    <?php include "../includes/sidebar_admin.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php 
            $pageTitle = "Riwayat Pembelian";
            include "../includes/topbar.php"; 
            ?>

            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Riwayat Pembelian</h1>

                    <a href="data_pembeli_admin.php" class="btn btn-sm btn-secondary shadow-sm">
                        <i class="fas fa-arrow-left fa-sm"></i> Kembali
                    </a>
                </div>

                <div class="card shadow mb-4">

                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Riwayat Pembelian: <?= htmlspecialchars($dataPembeli['nama']); ?>
                        </h6>
                    </div>

                    <div class="card-body">

                        <?php if(mysqli_num_rows($query) > 0){ ?>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-center" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Mobil</th>
                                        <th>Tanggal Pesan</th>
                                        <th>Total Harga</th>
                                        <th>Status Pesanan</th>
                                        <th>Metode Bayar</th>
                                        <th>Bukti Transfer</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    $no = 1;
                                    while($row = mysqli_fetch_assoc($query)){

                                        $statusPesanan = $row['status_pemesanan'];

                                        if($statusPesanan == "lunas"){
                                            $badgePesanan = "success";
                                        } elseif($statusPesanan == "booking"){
                                            $badgePesanan = "warning";
                                        } elseif($statusPesanan == "dp"){
                                            $badgePesanan = "info";
                                        } elseif($statusPesanan == "batal"){
                                            $badgePesanan = "danger";
                                        } else {
                                            $badgePesanan = "secondary";
                                        }

                                        
                                    ?>

                                    <tr>
                                        <td><?= $no++; ?></td>

                                        <td><?= htmlspecialchars($row['nama_mobil']); ?></td>

                                        <td>
                                            <?= date('d-m-Y H:i', strtotime($row['tanggal_pesan'])); ?>
                                        </td>

                                        <td>
                                            Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?>
                                        </td>

                                        <td>
                                            <span class="badge badge-<?= $badgePesanan; ?>">
                                                <?= ucfirst($statusPesanan); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?php if(!empty($row['metode_bayar'])){ ?>
                                                <?= ucfirst($row['metode_bayar']); ?>
                                            <?php } else { ?>
                                                cash
                                            <?php } ?>
                                        </td>

                                        <td>
                                            <?php if($row['metode_bayar'] == "transfer"){ ?>

                                                <?php if(!empty($row['bukti_pembayaran'])){ ?>

                                                    <a href="../uploads/<?= htmlspecialchars($row['bukti_pembayaran']); ?>"
                                                    target="_blank"
                                                    class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> Lihat
                                                    </a>

                                                <?php } else { ?>

                                                    <span class="text-danger">Belum Upload</span>

                                                <?php } ?>

                                            <?php } else { ?>

                                                -

                                            <?php } ?>
                                        </td>
                                    </tr>

                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <?php } else { ?>

                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-gray-400 mb-3"></i>

                            <h4 class="text-gray-800">
                                Belum Ada Riwayat Pembelian
                            </h4>

                            <p class="text-muted">
                                Pembeli ini belum melakukan transaksi.
                            </p>
                        </div>

                        <?php } ?>

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
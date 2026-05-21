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

$pembeli = mysqli_query($koneksi, "SELECT * FROM pembeli WHERE id_pembeli='$id'");
$dataPembeli = mysqli_fetch_assoc($pembeli);

if(!$dataPembeli){
    header("Location: data_pembeli_admin.php");
    exit;
}

$query = mysqli_query($koneksi,
"SELECT 
    p.id_pemesanan,
    p.tanggal_pesan,
    p.status AS status_pemesanan,
    p.deadline_dp,

    m.nama_mobil,
    m.harga,

    SUM(CASE WHEN py.jenis_pembayaran = 'booking' THEN py.jumlah ELSE 0 END) AS booking,
    SUM(CASE WHEN py.jenis_pembayaran = 'dp' THEN py.jumlah ELSE 0 END) AS dp,
    SUM(CASE WHEN py.jenis_pembayaran = 'pelunasan' THEN py.jumlah ELSE 0 END) AS pelunasan,

    MAX(py.bukti_pembayaran) AS bukti_pembayaran

FROM pemesanan p
JOIN mobil m ON p.id_mobil = m.id_mobil
LEFT JOIN pembayaran py ON p.id_pemesanan = py.id_pemesanan
WHERE p.id_pembeli='$id'
GROUP BY p.id_pemesanan
ORDER BY p.id_pemesanan DESC");

function rupiah($angka){
    return "Rp " . number_format((float)$angka, 0, ',', '.');
}

function badgeStatus($status){
    if($status == "lunas") return "success";
    if($status == "booking") return "warning";
    if($status == "dp") return "info";
    if($status == "batal") return "danger";
    return "secondary";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pembelian</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
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
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Riwayat Pembelian</h1>
                        <p class="mb-0 text-gray-600">
                            Ringkasan transaksi pembeli secara sederhana.
                        </p>
                    </div>

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
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead class="thead-light text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Mobil</th>
                                        <th>Tanggal</th>
                                        <th>Pembayaran</th>
                                        <th>Sisa</th>
                                        <th>Status</th>
                                        <th>Bukti</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    $no = 1;

                                    while($row = mysqli_fetch_assoc($query)){

                                        $hargaMobil = (float)$row['harga'];

                                        $booking = (float)$row['booking'];
                                        $dp = (float)$row['dp'];
                                        $pelunasan = (float)$row['pelunasan'];

                                        $totalDibayar = $booking + $dp + $pelunasan;
                                        $sisa = $hargaMobil - $totalDibayar;

                                        if($sisa < 0){
                                            $sisa = 0;
                                        }

                                        if($pelunasan > 0){
                                            $tahap = "Pelunasan";
                                            $badgeTahap = "success";
                                        } elseif($dp > 0){
                                            $tahap = "DP 30%";
                                            $badgeTahap = "info";
                                        } elseif($booking > 0){
                                            $tahap = "Booking";
                                            $badgeTahap = "warning";
                                        } else {
                                            $tahap = "Belum Bayar";
                                            $badgeTahap = "secondary";
                                        }
                                    ?>

                                    <tr>
                                        <td class="text-center"><?= $no++; ?></td>

                                        <td>
                                            <strong><?= htmlspecialchars($row['nama_mobil']); ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                Harga: <?= rupiah($hargaMobil); ?>
                                            </small>
                                        </td>

                                        <td class="text-center">
                                            <?= date('d-m-Y', strtotime($row['tanggal_pesan'])); ?>
                                            <br>
                                            <small class="text-muted">
                                                <?= date('H:i', strtotime($row['tanggal_pesan'])); ?>
                                            </small>
                                        </td>

                                        <td>
                                            <span class="badge badge-<?= $badgeTahap; ?> mb-2">
                                                <?= $tahap; ?>
                                            </span>
                                            <br>

                                            <small class="text-muted">Total Dibayar</small>
                                            <br>
                                            <strong><?= rupiah($totalDibayar); ?></strong>

                                            <br>

                                            <small class="text-muted">
                                                Booking: <?= rupiah($booking); ?> |
                                                DP: <?= rupiah($dp); ?> |
                                                Pelunasan: <?= rupiah($pelunasan); ?>
                                            </small>

                                            <?php if($row['status_pemesanan'] == "booking" && !empty($row['deadline_dp'])){ ?>
                                                <br>
                                                <small class="text-danger">
                                                    Batas DP: <?= date('d-m-Y', strtotime($row['deadline_dp'])); ?>
                                                </small>
                                            <?php } ?>
                                        </td>

                                        <td class="text-center">
                                            <?php if($sisa <= 0){ ?>
                                                <span class="badge badge-success">Lunas</span>
                                            <?php } else { ?>
                                                <strong class="text-danger"><?= rupiah($sisa); ?></strong>
                                            <?php } ?>
                                        </td>

                                        <td class="text-center">
                                            <span class="badge badge-<?= badgeStatus($row['status_pemesanan']); ?>">
                                                <?= ucfirst($row['status_pemesanan']); ?>
                                            </span>
                                        </td>

                                        <td class="text-center">
                                            <?php if(!empty($row['bukti_pembayaran']) && $row['bukti_pembayaran'] != "-"){ ?>
                                                <a href="../uploads/<?= htmlspecialchars($row['bukti_pembayaran']); ?>"
                                                   target="_blank"
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
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
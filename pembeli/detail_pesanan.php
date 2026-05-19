<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "pembeli") {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['id_user'] ?? 0;
$id_pemesanan = $_GET['id'] ?? '';

if (empty($id_pemesanan)) {
    echo "<script>
        alert('ID pesanan tidak ditemukan!');
        window.location='pesanan_saya.php';
    </script>";
    exit;
}

function rupiah($angka)
{
    return "Rp " . number_format((float)$angka, 0, ',', '.');
}

/*
|------------------------------------------------------
| DATA PEMBELI
|------------------------------------------------------
*/
$qPembeli = mysqli_query($koneksi, "
    SELECT *
    FROM pembeli
    WHERE id_user = '$id_user'
    LIMIT 1
");

if (!$qPembeli || mysqli_num_rows($qPembeli) == 0) {
    echo "<script>
        alert('Data pembeli tidak ditemukan!');
        window.location='../auth/login.php';
    </script>";
    exit;
}

$pembeli = mysqli_fetch_assoc($qPembeli);
$id_pembeli = $pembeli['id_pembeli'];

/*
|------------------------------------------------------
| DATA PESANAN
|------------------------------------------------------
*/
$qPesanan = mysqli_query($koneksi, "
    SELECT
        p.*,
        m.nama_mobil,
        m.harga,
        m.tahun,
        m.deskripsi,
        m.foto,
        m.status AS status_mobil
    FROM pemesanan p
    LEFT JOIN mobil m ON p.id_mobil = m.id_mobil
    WHERE p.id_pemesanan = '$id_pemesanan'
    AND p.id_pembeli = '$id_pembeli'
    LIMIT 1
");

if (!$qPesanan || mysqli_num_rows($qPesanan) == 0) {
    echo "<script>
        alert('Pesanan tidak ditemukan!');
        window.location='pesanan_saya.php';
    </script>";
    exit;
}

$pesanan = mysqli_fetch_assoc($qPesanan);

/*
|------------------------------------------------------
| DATA PEMBAYARAN
|------------------------------------------------------
*/
$qPembayaran = mysqli_query($koneksi, "
    SELECT *
    FROM pembayaran
    WHERE id_pemesanan = '$id_pemesanan'
    ORDER BY id_pembayaran ASC
");

$total_bayar = 0;

while ($pay = mysqli_fetch_assoc($qPembayaran)) {
    $total_bayar += (float)$pay['jumlah'];
}

mysqli_data_seek($qPembayaran, 0);

$total_harga = (float)$pesanan['total_harga'];
$sisa_bayar  = max($total_harga - $total_bayar, 0);

$statusPesanan = strtolower($pesanan['status']);

$badgeStatus = "secondary";

if ($statusPesanan == "booking") {
    $badgeStatus = "warning";
} elseif ($statusPesanan == "dp") {
    $badgeStatus = "info";
} elseif ($statusPesanan == "lunas") {
    $badgeStatus = "success";
} elseif ($statusPesanan == "batal") {
    $badgeStatus = "danger";
}

$fotoMobil = !empty($pesanan['foto'])
    ? "../uploads/" . $pesanan['foto']
    : "../assets/img/undraw_posting_photo.svg";
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Detail Pesanan - Galaxy Showroom</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

<div id="wrapper">

    <?php include "../includes/sidebar_pembeli.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php 
            $pageTitle = "Detail Pesanan";
            include "../includes/topbar.php"; 
            ?>

            <div class="container-fluid">

                <!-- HEADER -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">

                    <div>

                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold">
                            Detail Pesanan
                        </h1>

                        <p class="mb-0 text-gray-600">
                            Informasi lengkap pesanan mobil kamu.
                        </p>

                    </div>

                    <a href="pesanan_saya.php"
                       class="btn btn-secondary btn-sm shadow-sm">

                        <i class="fas fa-arrow-left mr-1"></i>
                        Kembali

                    </a>

                </div>

                <div class="row">

                    <!-- DETAIL MOBIL -->
                    <div class="col-lg-4">

                        <div class="card shadow mb-4">

                            <div class="card-header py-3">

                                <h6 class="m-0 font-weight-bold text-primary">
                                    Detail Mobil
                                </h6>

                            </div>

                            <div class="card-body text-center">

                                <div class="position-relative d-inline-block">

                                    <img src="<?= htmlspecialchars($fotoMobil); ?>"
                                         class="img-fluid img-thumbnail mb-3"
                                         style="max-height:230px; object-fit:cover; cursor:pointer;"
                                         data-toggle="modal"
                                         data-target="#modalFotoMobil"
                                         onerror="this.src='../assets/img/undraw_posting_photo.svg'">

                                    <div class="position-absolute w-100 h-100 d-flex align-items-center justify-content-center"
                                         style="
                                            top:0;
                                            left:0;
                                            background:rgba(0,0,0,.45);
                                            opacity:0;
                                            transition:.2s;
                                            border-radius:.35rem;
                                            cursor:pointer;
                                         "
                                         onmouseover="this.style.opacity='1'"
                                         onmouseout="this.style.opacity='0'"
                                         data-toggle="modal"
                                         data-target="#modalFotoMobil">

                                        <i class="fas fa-search-plus text-white fa-lg"></i>

                                    </div>

                                </div>

                                <h4 class="font-weight-bold text-gray-800">
                                    <?= htmlspecialchars($pesanan['nama_mobil']); ?>
                                </h4>

                                <p class="mb-1">
                                    Tahun <?= htmlspecialchars($pesanan['tahun']); ?>
                                </p>

                                <p class="mb-1">
                                    Status Mobil:
                                    <strong class="text-success">
                                        <?= ucfirst($pesanan['status_mobil']); ?>
                                    </strong>
                                </p>

                                <h5 class="font-weight-bold text-primary mt-3">
                                    <?= rupiah($pesanan['harga']); ?>
                                </h5>

                                <hr>

                                <div class="text-left">

                                    <h6 class="font-weight-bold text-gray-700">
                                        Deskripsi Mobil
                                    </h6>

                                    <p class="text-gray-600 mb-0">
                                        <?= nl2br(htmlspecialchars($pesanan['deskripsi'] ?? '-')); ?>
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- DETAIL PESANAN -->
                    <div class="col-lg-8">

                        <!-- STATUS -->
                        <div class="card shadow mb-4">

                            <div class="card-header py-3 d-flex justify-content-between align-items-center">

                                <h6 class="m-0 font-weight-bold text-primary">
                                    Status Pesanan
                                </h6>

                                <span class="badge badge-<?= $badgeStatus; ?> px-3 py-2">
                                    <?= strtoupper($statusPesanan); ?>
                                </span>

                            </div>

                            <div class="card-body">

                                <div class="row">

                                    <div class="col-md-6 mb-3">

                                        <div class="border rounded p-3 h-100">

                                            <div class="text-gray-500 small mb-1">
                                                Tanggal Pesan
                                            </div>

                                            <div class="font-weight-bold text-gray-800">
                                                <?= date('d F Y H:i', strtotime($pesanan['tanggal_pesan'])); ?>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <div class="border rounded p-3 h-100">

                                            <div class="text-gray-500 small mb-1">
                                                Deadline DP
                                            </div>

                                            <div class="font-weight-bold text-warning">

                                                <?php if(!empty($pesanan['deadline_dp'])){ ?>

                                                    <?= date('d F Y', strtotime($pesanan['deadline_dp'])); ?>

                                                <?php } else { ?>

                                                    -

                                                <?php } ?>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-md-4 mb-3">

                                        <div class="border rounded p-3 h-100 text-center">

                                            <div class="text-gray-500 small mb-1">
                                                Total Harga
                                            </div>

                                            <div class="font-weight-bold text-primary">
                                                <?= rupiah($total_harga); ?>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-md-4 mb-3">

                                        <div class="border rounded p-3 h-100 text-center">

                                            <div class="text-gray-500 small mb-1">
                                                Total Bayar
                                            </div>

                                            <div class="font-weight-bold text-success">
                                                <?= rupiah($total_bayar); ?>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-md-4 mb-3">

                                        <div class="border rounded p-3 h-100 text-center">

                                            <div class="text-gray-500 small mb-1">
                                                Sisa Bayar
                                            </div>

                                            <div class="font-weight-bold text-danger">
                                                <?= rupiah($sisa_bayar); ?>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- RIWAYAT PEMBAYARAN -->
                        <div class="card shadow mb-4">

                            <div class="card-header py-3 d-flex justify-content-between align-items-center">

                                <h6 class="m-0 font-weight-bold text-primary">
                                    Riwayat Pembayaran
                                </h6>

                                <?php if($statusPesanan == "lunas"){ ?>

                                    <a href="../admin/kwitansi.php?id=<?= $pesanan['id_pemesanan']; ?>"
                                       target="_blank"
                                       class="btn btn-success btn-sm">

                                        <i class="fas fa-print mr-1"></i>
                                        Cetak Kwitansi

                                    </a>

                                <?php } ?>

                            </div>

                            <div class="card-body">

                                <?php if(mysqli_num_rows($qPembayaran) > 0){ ?>

                                    <div class="table-responsive">

                                        <table class="table table-bordered table-hover">

                                            <thead class="thead-light text-center">

                                            <tr>

                                                <th>No</th>
                                                <th>Jenis</th>
                                                <th>Metode</th>
                                                <th>Jumlah</th>
                                                <th>Bukti</th>
                                                <th>Tanggal</th>

                                            </tr>

                                            </thead>

                                            <tbody>

                                            <?php 
                                            $no = 1;
                                            while($bayar = mysqli_fetch_assoc($qPembayaran)){ 
                                            ?>

                                                <tr>

                                                    <td class="text-center align-middle">
                                                        <?= $no++; ?>
                                                    </td>

                                                    <td class="align-middle text-center">

                                                        <?php
                                                        $jenis = $bayar['jenis_pembayaran'] ?? '-';

                                                        if($jenis == "booking"){
                                                            echo '<span class="badge badge-warning px-3 py-2">Booking</span>';
                                                        } elseif($jenis == "dp"){
                                                            echo '<span class="badge badge-info px-3 py-2">DP</span>';
                                                        } elseif($jenis == "pelunasan"){
                                                            echo '<span class="badge badge-success px-3 py-2">Pelunasan</span>';
                                                        } else {
                                                            echo '<span class="badge badge-secondary px-3 py-2">-</span>';
                                                        }
                                                        ?>

                                                    </td>

                                                    <td class="align-middle text-center">

                                                        <?php
                                                        $metode = strtolower($bayar['metode_bayar']);

                                                        if($metode == "transfer"){
                                                            echo '<span class="badge badge-primary px-3 py-2">Transfer</span>';
                                                        } else {
                                                            echo '<span class="badge badge-secondary px-3 py-2">Tunai</span>';
                                                        }
                                                        ?>

                                                    </td>

                                                    <td class="align-middle text-center font-weight-bold text-success">
                                                        <?= rupiah($bayar['jumlah']); ?>
                                                    </td>

                                                    <td class="align-middle text-center">

                                                        <?php if(
                                                            !empty($bayar['bukti_pembayaran']) &&
                                                            $bayar['bukti_pembayaran'] != '-'
                                                        ){ ?>

                                                            <a href="../uploads/<?= htmlspecialchars($bayar['bukti_pembayaran']); ?>"
                                                               target="_blank"
                                                               class="btn btn-warning btn-sm">

                                                                <i class="fas fa-image"></i>

                                                            </a>

                                                        <?php } else { ?>

                                                            <span class="text-muted">
                                                                -
                                                            </span>

                                                        <?php } ?>

                                                    </td>

                                                    <td class="align-middle text-center">

                                                        <?php
                                                        if(isset($bayar['tanggal_bayar']) && !empty($bayar['tanggal_bayar'])){
                                                            echo date('d M Y H:i', strtotime($bayar['tanggal_bayar']));
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>

                                                    </td>

                                                </tr>

                                            <?php } ?>

                                            </tbody>

                                        </table>

                                    </div>

                                <?php } else { ?>

                                    <div class="text-center py-4">

                                        <i class="fas fa-money-bill-wave fa-3x text-gray-300 mb-3"></i>

                                        <h5 class="text-gray-700">
                                            Belum Ada Pembayaran
                                        </h5>

                                    </div>

                                <?php } ?>

                            </div>

                        </div>

                        <!-- STATUS ADMINISTRASI -->
                        <div class="card shadow mb-4">

                            <div class="card-header py-3">

                                <h6 class="m-0 font-weight-bold text-primary">
                                    Status Administrasi
                                </h6>

                            </div>

                            <div class="card-body">

                                <div class="row">

                                    <div class="col-md-4 mb-3">

                                        <div class="border rounded p-3 h-100 text-center">

                                            <i class="fas fa-file-signature fa-2x text-primary mb-3"></i>

                                            <h6 class="font-weight-bold">
                                                Booking
                                            </h6>

                                            <?php if($total_bayar >= 500000){ ?>

                                                <span class="badge badge-success px-3 py-2">
                                                    Selesai
                                                </span>

                                            <?php } else { ?>

                                                <span class="badge badge-warning px-3 py-2">
                                                    Menunggu
                                                </span>

                                            <?php } ?>

                                        </div>

                                    </div>

                                    <div class="col-md-4 mb-3">

                                        <div class="border rounded p-3 h-100 text-center">

                                            <i class="fas fa-id-card fa-2x text-info mb-3"></i>

                                            <h6 class="font-weight-bold">
                                                STNK / BPKB
                                            </h6>

                                            <?php if($statusPesanan == "dp" || $statusPesanan == "lunas"){ ?>

                                                <span class="badge badge-info px-3 py-2">
                                                    Diproses
                                                </span>

                                            <?php } else { ?>

                                                <span class="badge badge-secondary px-3 py-2">
                                                    Belum
                                                </span>

                                            <?php } ?>

                                        </div>

                                    </div>

                                    <div class="col-md-4 mb-3">

                                        <div class="border rounded p-3 h-100 text-center">

                                            <i class="fas fa-truck fa-2x text-success mb-3"></i>

                                            <h6 class="font-weight-bold">
                                                Pengiriman
                                            </h6>

                                            <?php if($statusPesanan == "lunas"){ ?>

                                                <span class="badge badge-success px-3 py-2">
                                                    Siap Dikirim
                                                </span>

                                            <?php } else { ?>

                                                <span class="badge badge-secondary px-3 py-2">
                                                    Menunggu
                                                </span>

                                            <?php } ?>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- MODAL FOTO -->
<div class="modal fade" id="modalFotoMobil" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    <?= htmlspecialchars($pesanan['nama_mobil']); ?>
                </h5>

                <button type="button"
                        class="close"
                        data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <div class="modal-body text-center">

                <img src="<?= htmlspecialchars($fotoMobil); ?>"
                     class="img-fluid rounded shadow"
                     onerror="this.src='../assets/img/undraw_posting_photo.svg'">

            </div>

        </div>

    </div>

</div>

<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../assets/js/sb-admin-2.min.js"></script>

</body>
</html>
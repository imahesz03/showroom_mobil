<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "pembeli") {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['id_user'] ?? 0;

function rupiah($angka)
{
    return "Rp " . number_format((float)$angka, 0, ',', '.');
}

$qPembeli = mysqli_query($koneksi, "
    SELECT * FROM pembeli 
    WHERE id_user = '$id_user'
");

$pembeli = mysqli_fetch_assoc($qPembeli);

$id_pembeli = $pembeli['id_pembeli'] ?? 0;
$nama_pembeli = $pembeli['nama'] ?? ($_SESSION['username'] ?? 'Pembeli');

$totalMobil = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM mobil
"))['total'];

$mobilTersedia = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM mobil 
    WHERE status = 'tersedia' AND stok > 0
"))['total'];

$totalPesanan = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM pemesanan 
    WHERE id_pembeli = '$id_pembeli'
"))['total'];

$totalBooking = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM pemesanan 
    WHERE id_pembeli = '$id_pembeli'
    AND status = 'booking'
"))['total'];

$totalLunas = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM pemesanan 
    WHERE id_pembeli = '$id_pembeli'
    AND status = 'lunas'
"))['total'];

$totalPembelian = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COALESCE(SUM(total_harga), 0) AS total
    FROM pemesanan
    WHERE id_pembeli = '$id_pembeli'
    AND status = 'lunas'
"))['total'];

$mobilTerbaru = mysqli_query($koneksi, "
    SELECT *
    FROM mobil
    WHERE status = 'tersedia' AND stok > 0
    ORDER BY id_mobil DESC
    LIMIT 4
");

$pesananTerbaru = mysqli_query($koneksi, "
    SELECT 
        p.*,
        m.nama_mobil
    FROM pemesanan p
    LEFT JOIN mobil m ON p.id_mobil = m.id_mobil
    WHERE p.id_pembeli = '$id_pembeli'
    ORDER BY p.tanggal_pesan DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Pembeli - Galaxy Showroom</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

<div id="wrapper">

    <?php include "../includes/sidebar_pembeli.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php include "../includes/topbar.php"; ?>

            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h1 class="h3 mb-1 text-gray-800">Dashboard Pembeli</h1>
                        <p class="mb-0 text-gray-600">
                            Selamat datang, <?= htmlspecialchars($nama_pembeli); ?>. Temukan mobil impianmu di Galaxy Showroom.
                        </p>
                    </div>

                    <a href="../pembeli/lihat_mobil.php" class="btn btn-primary btn-sm shadow-sm mt-3 mt-sm-0">
                        <i class="fas fa-search fa-sm text-white-50"></i>
                        Lihat Mobil
                    </a>
                </div>

                <div class="row">

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Mobil
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $totalMobil; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-car fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Mobil Tersedia
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $mobilTersedia; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Pesanan Saya
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $totalPesanan; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Total Pembelian
                                        </div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                                            <?= rupiah($totalPembelian); ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-lg-4 mb-4">
                        <div class="card shadow h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center mr-3"
                                     style="width:45px; height:45px;">
                                    <i class="fas fa-clock text-white"></i>
                                </div>

                                <div>
                                    <div class="small text-gray-500">Status Booking</div>
                                    <div class="font-weight-bold text-gray-800">
                                        <?= $totalBooking; ?> Pesanan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-4">
                        <div class="card shadow h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="rounded-circle bg-success d-flex align-items-center justify-content-center mr-3"
                                     style="width:45px; height:45px;">
                                    <i class="fas fa-check text-white"></i>
                                </div>

                                <div>
                                    <div class="small text-gray-500">Pembelian Lunas</div>
                                    <div class="font-weight-bold text-gray-800">
                                        <?= $totalLunas; ?> Transaksi
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-4">
                        <div class="card shadow h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mr-3"
                                     style="width:45px; height:45px;">
                                    <i class="fas fa-headset text-white"></i>
                                </div>

                                <div>
                                    <div class="small text-gray-500">Layanan Pembeli</div>
                                    <div class="font-weight-bold text-gray-800">
                                        Siap Membantu
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-lg-6 mb-4">
                        <div class="card shadow h-100">
                            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    Mobil Tersedia Terbaru
                                </h6>

                                <a href="../pembeli_mobil/lihat_mobil.php" class="btn btn-sm btn-primary">
                                    Lihat Semua
                                </a>
                            </div>

                            <div class="card-body">

                                <?php if (mysqli_num_rows($mobilTerbaru) > 0) : ?>

                                    <?php while ($mobil = mysqli_fetch_assoc($mobilTerbaru)) : ?>

                                        <?php
                                        $fotoMobil = !empty($mobil['foto'])
                                            ? "../uploads/" . $mobil['foto']
                                            : "../assets/img/undraw_posting_photo.svg";
                                        ?>

                                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">

                                            <img src="<?= htmlspecialchars($fotoMobil); ?>"
                                                 onerror="this.src='../assets/img/undraw_posting_photo.svg'"
                                                 class="mr-3 shadow-sm"
                                                 style="width:90px; height:68px; object-fit:cover; border-radius:12px; border:1px solid #e3e6f0;">

                                            <div class="flex-grow-1">
                                                <div class="font-weight-bold text-gray-800 mb-1">
                                                    <?= htmlspecialchars($mobil['nama_mobil']); ?>
                                                </div>

                                                <div class="small text-gray-600 mb-1">
                                                    Tahun <?= htmlspecialchars($mobil['tahun']); ?>
                                                    •
                                                    Stok <?= htmlspecialchars($mobil['stok']); ?>
                                                </div>

                                                <div class="text-primary font-weight-bold">
                                                    <?= rupiah($mobil['harga']); ?>
                                                </div>
                                            </div>

                                        </div>

                                    <?php endwhile; ?>

                                <?php else : ?>

                                    <div class="text-center text-gray-500 py-4">
                                        <i class="fas fa-car fa-2x mb-2"></i>
                                        <p class="mb-0">Belum ada mobil tersedia.</p>
                                    </div>

                                <?php endif; ?>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 mb-4">
                        <div class="card shadow h-100">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    Pemesanan Terbaru
                                </h6>
                            </div>

                            <div class="card-body">

                                <?php if (mysqli_num_rows($pesananTerbaru) > 0) : ?>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover mb-0">
                                            <thead class="thead-light">
                                            <tr>
                                                <th>Mobil</th>
                                                <th>Status</th>
                                                <th>Total</th>
                                            </tr>
                                            </thead>

                                            <tbody>
                                            <?php while ($row = mysqli_fetch_assoc($pesananTerbaru)) : ?>

                                                <?php
                                                $badge = "secondary";

                                                if ($row['status'] == "booking") {
                                                    $badge = "warning";
                                                } elseif ($row['status'] == "dp") {
                                                    $badge = "info";
                                                } elseif ($row['status'] == "lunas") {
                                                    $badge = "success";
                                                } elseif ($row['status'] == "batal") {
                                                    $badge = "danger";
                                                }
                                                ?>

                                                <tr>
                                                    <td>
                                                        <div class="font-weight-bold text-gray-800">
                                                            <?= htmlspecialchars($row['nama_mobil'] ?? '-'); ?>
                                                        </div>
                                                        <div class="small text-gray-500">
                                                            <?= date('d M Y', strtotime($row['tanggal_pesan'])); ?>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <span class="badge badge-<?= $badge; ?>">
                                                            <?= ucfirst($row['status']); ?>
                                                        </span>
                                                    </td>

                                                    <td>
                                                        <?= rupiah($row['total_harga']); ?>
                                                    </td>
                                                </tr>

                                            <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                <?php else : ?>

                                    <div class="text-center text-gray-500 py-4">
                                        <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                        <p class="mb-0">Belum ada pemesanan.</p>
                                    </div>

                                <?php endif; ?>

                            </div>
                        </div>
                    </div>

                </div>

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
<?php
session_start();
include "../config/koneksi.php";

if ($_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit;
}

$totalMobil = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM mobil"));
$totalPembeli = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM pembeli"));
$totalPenjual = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM penjual"));
$totalPemesanan = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM pemesanan"));
$totalPembayaran = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM pembayaran"));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SB ADMIN 2 CSS -->
    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

<div id="wrapper">

    <!-- SIDEBAR -->
    <?php include '../includes/sidebar_admin.php'; ?>

    <!-- CONTENT WRAPPER -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- MAIN CONTENT -->
        <div id="content">

            <!-- TOPBAR -->
            <?php include '../includes/topbar.php'; ?>

            <!-- PAGE CONTENT -->
            <div class="container-fluid">

                <!-- TITLE -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Dashboard Admin</h1>
                        <p class="mb-0 text-gray-600">
                            Selamat datang, <?= htmlspecialchars($_SESSION['username']); ?> 👋
                        </p>
                    </div>
                </div>

                <!-- CARD ROW -->
                <div class="row">

                    <!-- TOTAL MOBIL -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Mobil
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= $totalMobil; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TOTAL PEMBELI -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Pembeli
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= $totalPembeli; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TOTAL PENJUAL -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total Penjual
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= $totalPenjual; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TOTAL PEMESANAN -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Total Pemesanan
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= $totalPemesanan; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TOTAL PEMBAYARAN -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Total Pembayaran
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= $totalPembayaran; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <!-- END PAGE CONTENT -->

        </div>
        <!-- END MAIN CONTENT -->

    </div>
    <!-- END CONTENT WRAPPER -->

</div>
<!-- END WRAPPER -->

<!-- SB ADMIN 2 JS -->
<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sb-admin-2.min.js"></script>

</body>
</html>
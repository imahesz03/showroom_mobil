<?php
session_start();

if($_SESSION['role'] != "pembeli"){
    header("Location: ../auth/login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport"
    content="width=device-width, initial-scale=1.0">

    <title>Dashboard Pembeli</title>

    <link rel="stylesheet"
    href="../assets/style.css">

</head>
<body>

<div class="sidebar-layout">

    <!-- SIDEBAR -->
    <?php include '../includes/sidebar_pembeli.php'; ?>


    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- TOPBAR -->
        <?php $pageTitle = 'Dashboard Pembeli'; include '../includes/topbar.php'; ?>

        <!-- CONTENT -->
        <div class="content">

            <!-- CONTENT -->
        <div class="content">

            <!-- WELCOME -->
            <div class="welcome-box">

                <h1>Selamat Datang <?= htmlspecialchars($_SESSION['username']) ?> 👋</h1>

                <p>
                    Selamat datang di Galaxy Showroom! Temukan mobil impianmu,
                    lakukan pemesanan, dan pantau status transaksimu dengan mudah.          
                </p>

            </div>


            <!-- MENU CARD -->
            <div class="menu-cards">

                <!-- LIHAT MOBIL -->
                <div class="menu-card">

                    <h3>Lihat Mobil</h3>

                    <p>

                        Jelajahi seluruh mobil yang tersedia
                        di showroom.

                    </p>

                    <a href="../pembeli_mobil/lihat_mobil.php">

                        Masuk

                    </a>

                </div>


                <!-- PESANAN -->
                <div class="menu-card">

                    <h3>Pesanan Saya</h3>

                    <p>

                        Lihat status pemesanan mobil anda.

                    </p>

                    <a href="../pembeli_mobil/pesanan_saya.php">

                        Masuk

                    </a>

                </div>


                <!-- PEMBAYARAN -->
                <div class="menu-card">

                    <h3>Pembayaran</h3>

                    <p>

                        Upload dan cek pembayaran mobil anda.

                    </p>

                    <a href="../pembayaran/pembayaran.php">

                        Masuk

                    </a>

                </div>


                <!-- PROFILE -->
                <div class="menu-card">

                    <h3>Profile</h3>

                    <p>

                        Kelola informasi akun pembeli anda.

                    </p>

                    <a href="#">

                        Masuk

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>
<?php
$page = basename($_SERVER['PHP_SELF']);
?>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- BRAND -->
    <a class="sidebar-brand d-flex align-items-center justify-content-start px-3 py-4"
       href="../dashboard/pembeli.php">

        <div class="sidebar-brand-icon">
            <div class="bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center"
                 style="width:52px; height:52px;">
                <i class="fas fa-car-side text-primary" style="font-size:21px;"></i>
            </div>
        </div>

        <div class="sidebar-brand-text text-left ml-3" style="line-height:1.15;">
            <div style="font-size:12px; letter-spacing:2px; font-weight:700; opacity:.75; text-transform:uppercase;">
                Pembeli
            </div>

            <div style="font-size:16px; font-weight:900; margin-top:3px; color:#ffffff;">
                Galaxy Showroom
            </div>
        </div>

    </a>

    <hr class="sidebar-divider my-0">

    <!-- DASHBOARD -->
    <li class="nav-item <?= ($page == 'pembeli.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="../dashboard/pembeli.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Mobil
    </div>

    <!-- LIHAT MOBIL -->
    <li class="nav-item <?= ($page == 'lihat_mobil.php' || $page == 'detail_mobil.php' || $page == 'pesan_mobil.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="../pembeli/lihat_mobil.php">
            <i class="fas fa-fw fa-car"></i>
            <span>Lihat Mobil</span>
        </a>
    </li>

    <!-- PESANAN SAYA -->
    <li class="nav-item <?= ($page == 'pesanan_saya.php' || $page == 'detail_pesanan.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="../pembeli/pesanan_saya.php">
            <i class="fas fa-fw fa-shopping-cart"></i>
            <span>Pesanan Saya</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Pembayaran
    </div>

    <!-- RIWAYAT PEMBAYARAN -->
    <li class="nav-item <?= ($page == 'riwayat_pembayaran.php' || $page == 'pembayaran.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="../pembeli/riwayat_pembayaran.php">
            <i class="fas fa-fw fa-credit-card"></i>
            <span>Riwayat Pembayaran</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Administrasi
    </div>

    <!-- STATUS STNK / BPKB -->
    <li class="nav-item <?= ($page == 'status_administrasi.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="../pembeli/status_administrasi.php">
            <i class="fas fa-fw fa-file-signature"></i>
            <span>Status STNK/BPKB</span>
        </a>
    </li>

    <!-- PENGIRIMAN MOBIL -->
    <li class="nav-item <?= ($page == 'pengiriman_mobil.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="../pembeli/pengiriman_mobil.php">
            <i class="fas fa-fw fa-truck"></i>
            <span>Pengiriman Mobil</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Riwayat
    </div>

    <!-- RIWAYAT PEMBELIAN -->
    <li class="nav-item <?= ($page == 'riwayat_pembelian.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="../pembeli/riwayat_pembelian.php">
            <i class="fas fa-fw fa-history"></i>
            <span>Riwayat Pembelian</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

</ul>
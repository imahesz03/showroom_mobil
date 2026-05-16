<?php
$page = basename($_SERVER['PHP_SELF']);
?>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- BRAND -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="../dashboard/admin.php">
        <div class="sidebar-brand-icon">
            <i class="fas fa-car"></i>
        </div>
        <div class="sidebar-brand-text mx-3">ADMIN</div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- DASHBOARD -->
    <li class="nav-item <?= ($page == 'admin.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="../dashboard/admin.php">
            <i class="fas fa-fw fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- DATA MOBIL -->
    <li class="nav-item <?= in_array($page, ['data_mobil_admin.php', 'tambah_mobil.php', 'edit_mobil.php', 'hapus_mobil_admin.php']) ? 'active' : ''; ?>">
        <a class="nav-link" href="../admin/data_mobil_admin.php">
            <i class="fas fa-fw fa-car"></i>
            <span>Data Mobil</span>
        </a>
    </li>

    <!-- DATA PEMBELI -->
    <li class="nav-item <?= (strpos($_SERVER['REQUEST_URI'], 'pembeli') !== false) ? 'active' : ''; ?>">
        <a class="nav-link" href="../admin/data_pembeli_admin.php">
            <i class="fas fa-fw fa-users"></i>
            <span>Data Pembeli</span>
        </a>
    </li>

    <!-- TRANSAKSI -->
    <li class="nav-item <?= (strpos($page, 'transaksi') !== false) ? 'active' : ''; ?>">
        <a class="nav-link" href="../admin/transaksi.php">
            <i class="fas fa-fw fa-receipt"></i>
            <span>Transaksi</span>
        </a>
    </li>

    <!-- Administrasi Kendaraan -->
    <li class="nav-item <?= ($page == 'administrasi_kendaraan.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="../admin/administrasi_kendaraan.php">
            <i class="fas fa-fw fa-folder-open"></i>
            <span>Administrasi Kendaraan</span>
        </a>
    </li>

    <!-- PENGIRIMAN MOBIL -->
    <li class="nav-item <?= ($page == 'pengiriman_mobil.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="../admin/pengiriman_mobil.php">
            <i class="fas fa-fw fa-car-side"></i>
            <span>Pengiriman Mobil</span>
        </a>
    </li>
        <!-- PENAWARAN MOBIL -->
    <li class="nav-item <?= ($page == 'penawaran.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="../admin/penawaran.php">
            <i class="fas fa-fw fa-handshake"></i>
            <span>Penawaran Mobil</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

</ul>
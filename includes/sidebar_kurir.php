<?php
$page = basename($_SERVER['PHP_SELF']);
?>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- BRAND -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center"
       href="../dashboard/kurir.php">

        <div class="sidebar-brand-icon">
            <i class="fas fa-truck"></i>
        </div>

        <div class="sidebar-brand-text mx-2">
            Kurir Panel
        </div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- DASHBOARD -->
    <li class="nav-item <?= ($page == 'kurir.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="../dashboard/kurir.php">

            <i class="fas fa-fw fa-home"></i>
            <span>Dashboard</span>

        </a>
    </li>

    <!-- PENGIRIMAN MOBIL -->
    <li class="nav-item <?= ($page == 'pengiriman_mobil.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="../kurir/pengiriman_mobil.php">

            <i class="fas fa-fw fa-truck"></i>
            <span>Pengiriman Mobil</span>

        </a>
    </li>

    <hr class="sidebar-divider">



</ul>
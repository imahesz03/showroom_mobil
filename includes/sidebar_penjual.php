<?php
$page = basename($_SERVER['PHP_SELF']);
?>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="../dashboard/penjual.php">
        <div class="sidebar-brand-icon">
            <i class="fas fa-store"></i>
        </div>
        <div class="sidebar-brand-text mx-2">Penjual Panel</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item <?= ($page == 'penjual.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="../dashboard/penjual.php">
            <i class="fas fa-fw fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-item <?= ($page == 'penawaran_mobil.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="../penjual/penawaran_mobil.php">
            <i class="fas fa-fw fa-handshake"></i>
            <span>Penawaran Mobil</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <li class="nav-item">
        <a class="nav-link" href="../auth/logout.php">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </li>

</ul>
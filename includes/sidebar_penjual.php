<?php
$page = basename($_SERVER['PHP_SELF']);
?>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- SIDEBAR BRAND -->
    <a class="sidebar-brand d-flex align-items-center justify-content-start px-3 py-4"
    href="../dashboard/admin.php">

        <!-- ICON -->
        <div class="sidebar-brand-icon">

            <div class="bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center"
                style="width:52px; height:52px;">

                <i class="fas fa-car-side text-primary"
                style="font-size:21px;"></i>

            </div>

        </div>

        <!-- TEXT -->
        <div class="sidebar-brand-text text-left ml-3"
            style="line-height:1.15;">

            <!-- SMALL LABEL -->
            <div style="
                font-size:12px;
                letter-spacing:2px;
                font-weight:700;
                opacity:.75;
                text-transform:uppercase;">

                Penjual

            </div>

            <!-- BRAND NAME -->
            <div style="
                font-size:16px;
                font-weight:900;
                margin-top:3px;
                color:#ffffff;">

                Galaxy Showroom

            </div>


        </div>

    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item <?= ($page == 'dashboard.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="../penjual/dashboard.php">
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

</ul>
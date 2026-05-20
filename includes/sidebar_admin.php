<?php
$page = basename($_SERVER['PHP_SELF']);
?>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- SIDEBAR BRAND -->
    <a class="sidebar-brand d-flex align-items-center justify-content-start px-3 py-4"
    href="../admin/dashboard.php">

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

                Admin

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

    <!-- DASHBOARD -->
    <li class="nav-item <?= ($page == 'dashboard.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="../admin/dashboard.php">
            <i class="fas fa-fw fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>

            <hr class="sidebar-divider d-none d-md-block">

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

<li class="nav-item">

    <a class="nav-link collapsed"
       href="#"
       data-toggle="collapse"
       data-target="#collapseLaporan"
       aria-expanded="true"
       aria-controls="collapseLaporan">

        <i class="fas fa-fw fa-chart-bar"></i>
        <span>Laporan</span>
    </a>

    <div id="collapseLaporan"
         class="collapse"
         aria-labelledby="headingLaporan"
         data-parent="#accordionSidebar">

        <div class="bg-white py-2 collapse-inner rounded shadow-sm">

            <h6 class="collapse-header text-primary">
                Menu Laporan
            </h6>

            <a class="collapse-item d-flex align-items-center"
               href="../admin/laporan_mobil.php">

                <i class="fas fa-car fa-sm mr-2 text-gray-500"></i>
                Data Mobil
            </a>

            <a class="collapse-item d-flex align-items-center"
               href="../admin/laporan_pembeli.php">

                <i class="fas fa-users fa-sm mr-2 text-gray-500"></i>
                Data Pembeli
            </a>

            <a class="collapse-item d-flex align-items-center"
               href="../admin/laporan_transaksi.php">

                <i class="fas fa-file-invoice-dollar fa-sm mr-2 text-gray-500"></i>
                Transaksi
            </a>

        </div>
    </div>

</li>

</ul>
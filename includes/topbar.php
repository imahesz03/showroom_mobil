<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$username = $_SESSION['username'] ?? 'Admin';
?>

<style>
    .topbar {
        height: 4.375rem;
        border-bottom: 1px solid #e3e6f0;
        
        /* Kunci posisi topbar di atas layar */
        position: fixed !important;
        top: 0;
        right: 0;
        z-index: 1020;
        
        /* Lebar mengikuti sisa ruang sidebar */
        left: 14rem; 
        width: calc(100% - 14rem);
        transition: left 0.2s cubic-bezier(0.4, 0, 0.2, 1), width 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Otomatis melebar jika sidebar dalam mode mengecil (toggled) */
    .sidebar.toggled ~ #content-wrapper .topbar,
    body.sidebar-toggled #wrapper .topbar {
        left: 6.5rem !important;
        width: calc(100% - 6.5rem) !important;
    }

    /* 🛠️ FIX ACCURATE SPACING: Hanya dorong elemen pembungkus utama sejauh tinggi topbar */
    #content-wrapper {
        padding-top: 4.375rem !important;
    }

    /* Reset margin/padding liar dari selektor lain agar tidak menumpuk ganda */
    #content, .container-fluid {
        margin-top: 0 !important;
    }
    .container-fluid {
        padding-top: 1.5rem !important;
    }
    
    .navbar-nav .nav-item .nav-link {
        height: 4.375rem;
        display: flex;
        align-items: center;
        padding: 0 0.75rem;
        transition: background-color 0.2s ease-in-out;
    }

    .navbar-nav .nav-item .nav-link:hover {
        background-color: #f8f9fc;
    }

    .img-profile {
        border: 2px solid #e3e6f0;
        padding: 2px;
        background-color: #fff;
        transition: border-color 0.2s ease-in-out, transform 0.2s ease-in-out;
    }

    .nav-item.show .img-profile,
    .navbar-nav .nav-item .nav-link:hover .img-profile {
        border-color: #4e73df;
        transform: scale(1.05);
    }

    .dropdown-menu {
        border: none;
        margin-top: 0.25rem !important;
    }

    .dropdown-item {
        padding: 0.6rem 1.25rem;
        font-size: 0.85rem;
        color: #4e73df;
        transition: background-color 0.15s ease, color 0.15s ease;
    }

    .dropdown-item:hover {
        background-color: #4e73df;
        color: #fff !important;
    }

    .dropdown-item:hover i {
        color: #fff !important;
    }
    
    .dropdown-divider {
        margin: 0.35rem 0;
        border-top: 1px solid #eaecf4;
    }
</style>

<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <ul class="navbar-nav ml-auto">

        <li class="nav-item dropdown no-arrow">

            <a class="nav-link dropdown-toggle"
               href="#"
               id="userDropdown"
               role="button"
               data-toggle="dropdown"
               aria-haspopup="true"
               aria-expanded="false">

                <span class="mr-2 d-none d-lg-inline text-gray-600 small font-weight-bold">
                    <?= htmlspecialchars($username) ?>
                </span>

                <?php
                $fotoTopbar = $_SESSION['foto_profil'] ?? '';

                if(!empty($fotoTopbar)){
                    $srcFotoTopbar = "../uploads/" . $fotoTopbar;
                } else {
                    $srcFotoTopbar = "https://cdn-icons-png.flaticon.com/512/3135/3135715.png";
                }
                ?>

                <img class="img-profile rounded-circle"
                    src="<?= htmlspecialchars($srcFotoTopbar); ?>"
                    width="40"
                    height="40"
                    style="object-fit: cover;">

            </a>

            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                 aria-labelledby="userDropdown">

                <a class="dropdown-item" href="../admin/profil.php">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profil
                </a>
                <div class="dropdown-divider"></div>
                
                <a class="dropdown-item" href="../auth/logout.php">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
                
            </div>

        </li>

    </ul>

</nav>
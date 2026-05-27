<?php
$page = basename($_SERVER['PHP_SELF']);
?>

<style>
    /* KUNCI POSISI SIDEBAR & INTERNAL SCROLL (SIDEBAR DIAM TIDAK BERGERAK) */
    .bg-gradient-primary.sidebar {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #0f2027 100%) !important;
        background-size: 400% 400% !important;
        animation: GradientShift 15s ease infinite;
        will-change: width, max-width;
        transform: translateZ(0);

        /* Kunci di posisi kiri layar */
        position: fixed !important;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 1030;
        
        height: 100vh !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
    }

    /* Kustomisasi Scrollbar Internal Sidebar */
    .bg-gradient-primary.sidebar::-webkit-scrollbar {
        width: 5px;
    }
    .bg-gradient-primary.sidebar::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
    }
    .bg-gradient-primary.sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 10px;
    }
    .bg-gradient-primary.sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    /* KOMPENSASI SPASI KONTEN UTAMA */
    #wrapper #content-wrapper {
        margin-left: 14rem;
        transition: margin-left 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        width: calc(100% - 14rem);
    }

    .sidebar.toggled ~ #content-wrapper,
    body.sidebar-toggled #wrapper #content-wrapper {
        margin-left: 6.5rem !important;
        width: calc(100% - 6.5rem) !important;
    }

    @keyframes GradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .sidebar .sidebar-heading {
        color: rgba(255, 255, 255, 0.45) !important;
        font-weight: 700;
        font-size: 0.7rem;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        margin-top: 1.5rem;
    }

    .sidebar-dark .nav-item .nav-link {
        color: rgba(255, 255, 255, 0.8) !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .sidebar-dark .nav-item .nav-link i {
        color: rgba(255, 255, 255, 0.6) !important;
        transition: color 0.2s ease-in-out;
    }

    /* HOVER MENU UTAMA */
    .sidebar:not(.toggled) .nav-item .nav-link:hover {
        background-color: rgba(0, 0, 0, 0.2) !important; 
        color: #ffffff !important;
        padding-left: 1.5rem !important;
    }
    .sidebar:not(.toggled) .nav-item .nav-link:hover i {
        color: #ffffff !important;
    }

    /* GAYA VISUAL MENU AKTIF */
    .sidebar-dark .nav-item.active .nav-link {
        color: #ffffff !important;
        font-weight: 700 !important;
        background-color: rgba(0, 0, 0, 0.25) !important;
    }
    .sidebar:not(.toggled) .nav-item.active .nav-link {
        border-left: 3.5px solid #1cc88a !important;
    }
    .sidebar-dark .nav-item.active .nav-link i {
        color: #ffffff !important;
    }

    /* MINIMIZE / TOGGLED MODE */
    .sidebar.toggled .nav-item .nav-link span,
    .sidebar.toggled .sidebar-heading,
    .sidebar.toggled .sidebar-brand-text {
        display: none !important;
    }

    .sidebar.toggled .nav-item .nav-link {
        text-align: center !important;
        padding: 0.75rem 1rem !important;
        width: 6.5rem !important;
    }
    .sidebar.toggled .nav-item .nav-link i {
        margin-right: 0 !important;
        font-size: 1.15rem !important;
    }
    .sidebar.toggled .nav-item .nav-link:hover {
        background-color: rgba(0, 0, 0, 0.2) !important;
        padding-left: 1rem !important;
    }
    .sidebar.toggled .sidebar-brand {
        padding: 1.5rem 1rem !important;
        justify-content: center !important;
    }

    /* DROPDOWN KOTAK LAPORAN */
    .sidebar .collapse-inner {
        background-color: rgba(0, 0, 0, 0.25) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        padding: 0.75rem 0.5rem !important;
        border-radius: 0.5rem !important;
    }
    .sidebar .collapse-inner .collapse-header {
        color: rgba(255, 255, 255, 0.4) !important;
        font-weight: 800;
        font-size: 0.65rem;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 0.25rem 0.75rem 0.5rem 0.75rem !important;
    }
    .sidebar .collapse-inner .collapse-item {
        color: rgba(255, 255, 255, 0.75) !important;
        padding: 0.6rem 0.75rem !important;
        margin-bottom: 0.2rem;
        border-radius: 0.35rem;
        transition: all 0.2s ease;
    }
    .sidebar .collapse-inner .collapse-item:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
        padding-left: 1rem !important;
    }
    .sidebar .collapse-inner .collapse-item.active {
        background-color: #1cc88a !important;
        color: #ffffff !important;
        font-weight: 700;
    }

    .sidebar-dark .sidebar-divider {
        border-top: 1px solid rgba(255, 255, 255, 0.06) !important;
    }
</style>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-start px-3 py-4 normal-link" href="../admin/dashboard.php" style="text-decoration: none; transition: all 0.3s ease;">
        <div class="sidebar-brand-icon">
            <div class="bg-gradient-primary shadow-sm rounded-circle d-flex align-items-center justify-content-center"
                style="width: 48px; height: 48px; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); transition: transform 0.2s ease;"
                onmouseover="this.style.transform='scale(1.05)'" 
                onmouseout="this.style.transform='scale(1)'">
                <i class="fas fa-car text-white" style="font-size: 20px; filter: drop-shadow(0px 2px 4px rgba(0,0,0,0.15));"></i>
            </div>
        </div>
        
        <div class="sidebar-brand-text text-left ml-3" style="line-height: 1.2;">
            <div style="font-size: 11px; letter-spacing: 1.5px; font-weight: 800; color: #a3b8cc; text-transform: uppercase;">
                Admin Panel
            </div>
            <div style="font-size: 15px; font-weight: 800; margin-top: 2px; color: #ffffff; letter-spacing: 0.5px;">
                Galaxy Showroom
            </div>
        </div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item <?= ($page == 'dashboard.php' || $page == '') ? 'active' : ''; ?>">
        <a class="nav-link normal-link" href="../admin/dashboard.php">
            <i class="fas fa-fw fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>
    
    <li class="nav-item <?= in_array($page, ['data_mobil_admin.php', 'tambah_mobil.php', 'edit_mobil.php', 'hapus_mobil_admin.php']) ? 'active' : ''; ?>">
        <a class="nav-link" href="../admin/data_mobil_admin.php">
            <i class="fas fa-fw fa-car"></i>
            <span>Data Mobil</span>
        </a>
    </li>

    <li class="nav-item <?= (strpos($_SERVER['REQUEST_URI'], 'pembeli') !== false) ? 'active' : ''; ?>">
        <a class="nav-link ajax-link" href="../admin/data_pembeli_admin.php">
            <i class="fas fa-fw fa-users"></i>
            <span>Data Pembeli</span>
        </a>
    </li>

    <li class="nav-item <?= (strpos($page, 'transaksi') !== false) ? 'active' : ''; ?>">
        <a class="nav-link ajax-link" href="../admin/transaksi.php">
            <i class="fas fa-fw fa-receipt"></i>
            <span>Transaksi</span>
        </a>
    </li>

    <li class="nav-item <?= ($page == 'administrasi_kendaraan.php') ? 'active' : ''; ?>">
        <a class="nav-link ajax-link" href="../admin/administrasi_kendaraan.php">
            <i class="fas fa-fw fa-folder-open"></i>
            <span>Administrasi Kendaraan</span>
        </a>
    </li>

    <li class="nav-item <?= ($page == 'pengiriman_mobil.php') ? 'active' : ''; ?>">
        <a class="nav-link ajax-link" href="../admin/pengiriman_mobil.php">
            <i class="fas fa-fw fa-car-side"></i>
            <span>Pengiriman Mobil</span>
        </a>
    </li>

    <li class="nav-item <?= ($page == 'penawaran.php') ? 'active' : ''; ?>">
        <a class="nav-link ajax-link" href="../admin/penawaran.php">
            <i class="fas fa-fw fa-handshake"></i>
            <span>Penawaran Mobil</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Analisis & Grafik
    </div>

    <?php 
    $is_laporan_active = in_array($page, ['laporan_mobil.php', 'laporan_pembeli.php', 'laporan_transaksi.php']);
    ?>
    <li class="nav-item <?= $is_laporan_active ? 'active' : ''; ?>">
        <a class="nav-link <?= $is_laporan_active ? '' : 'collapsed'; ?>"
           href="#"
           data-toggle="collapse"
           data-target="#collapseLaporan"
           aria-expanded="<?= $is_laporan_active ? 'true' : 'false'; ?>"
           aria-controls="collapseLaporan">
            <i class="fas fa-fw fa-chart-bar"></i>
            <span>Laporan</span>
        </a>

        <div id="collapseLaporan"
             class="collapse <?= $is_laporan_active ? 'show' : ''; ?>"
             aria-labelledby="headingLaporan"
             data-parent="#accordionSidebar">
            <div class="collapse-inner">
                <h6 class="collapse-header">Menu Laporan:</h6>
                
                <a class="collapse-item d-flex align-items-center ajax-link <?= ($page == 'laporan_mobil.php') ? 'active' : ''; ?>"
                   href="../admin/laporan_mobil.php">
                    <i class="fas fa-car fa-sm mr-2"></i>
                    <span>Data Mobil</span>
                </a>

                <a class="collapse-item d-flex align-items-center ajax-link <?= ($page == 'laporan_pembeli.php') ? 'active' : ''; ?>"
                   href="../admin/laporan_pembeli.php">
                    <i class="fas fa-users fa-sm mr-2"></i>
                    <span>Data Pembeli</span>
                </a>

                <a class="collapse-item d-flex align-items-center ajax-link <?= ($page == 'laporan_transaksi.php') ? 'active' : ''; ?>"
                   href="../admin/laporan_transaksi.php">
                    <i class="fas fa-file-invoice-dollar fa-sm mr-2"></i>
                    <span>Transaksi</span>
                </a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline mt-3">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const sidebar = document.getElementById("accordionSidebar");
    const toggleBtn = document.getElementById("sidebarToggle");

    // 1. Mempertahankan Status Toggled (Minimize) agar tidak reset saat diklik
    if (localStorage.getItem("sidebar-toggled") === "true") {
        sidebar.classList.add("toggled");
    }

    if (toggleBtn) {
        toggleBtn.addEventListener("click", function() {
            setTimeout(() => {
                localStorage.setItem("sidebar-toggled", sidebar.classList.contains("toggled"));
            }, 50);
        });
    }

    // 2. AJAX Hanya Berjalan pada Class ".ajax-link"
    const ajaxLinks = document.querySelectorAll(".ajax-link");
    
    ajaxLinks.forEach(link => {
        link.addEventListener("click", function(e) {
            e.preventDefault();
            
            const url = this.getAttribute("href");
            if (!url || url === "#") return;

            const contentContainer = document.getElementById("content") || document.getElementById("wrapper");
            if (contentContainer) {
                contentContainer.style.opacity = "0.5";
                contentContainer.style.transition = "opacity 0.1s ease";
            }

            fetch(url)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, "text/html");
                    const newContent = doc.getElementById("content") || doc.getElementById("wrapper");
                    
                    if (newContent && contentContainer) {
                        contentContainer.innerHTML = newContent.innerHTML;
                        contentContainer.style.opacity = "1";
                        history.pushState({ url: url }, "", url);
                        
                        updateActiveMenu(url);
                        window.scrollTo({ top: 0 });
                    } else {
                        window.location.href = url;
                    }
                })
                .catch(error => {
                    console.error("AJAX Error:", error);
                    window.location.href = url;
                });
        });
    });

    function updateActiveMenu(currentUrl) {
        document.querySelectorAll(".sidebar .nav-item").forEach(item => item.classList.remove("active"));
        document.querySelectorAll(".sidebar .collapse-item").forEach(item => item.classList.remove("active"));

        const currentFile = currentUrl.split('/').pop().split('?')[0];
        let targetLink = document.querySelector(`.sidebar a[href*="${currentFile}"]`);
        
        if (targetLink) {
            const parentLi = targetLink.closest(".nav-item");
            if (parentLi) parentLi.classList.add("active");
            if (targetLink.classList.contains("collapse-item")) targetLink.classList.add("active");
        }
    }

    window.addEventListener("popstate", function() {
        window.location.reload();
    });
});
</script>
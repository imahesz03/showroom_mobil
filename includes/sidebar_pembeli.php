<?php
$page = basename($_SERVER['PHP_SELF']);
?>

<style>
    /* Mengunci pembungkus paling luar agar tidak berantakan */
    #wrapper {
        display: flex;
        align-items: stretch;
    }

    /* 1. STICKY SIDEBAR (DIAM & MEMILIKI SCROLL MANDIRI TANPA BUG TERPOTONG) */
    .bg-gradient-primary.sidebar {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #0f2027 100%) !important;
        background-size: 400% 400% !important;
        animation: GradientShift 15s ease infinite;
        will-change: width, max-width;

        /* Menggunakan Sticky agar bersahabat dengan layout halaman */
        position: -webkit-sticky !important;
        position: sticky !important;
        top: 0;
        height: 100vh !important;
        z-index: 1050 !important;
        
        /* Area scroll mandiri internal */
        overflow-y: auto !important;
        overflow-x: hidden !important;
    }

    /* Kustomisasi Scrollbar Internal Sidebar */
    .bg-gradient-primary.sidebar::-webkit-scrollbar {
        width: 4px;
    }
    .bg-gradient-primary.sidebar::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.02);
    }
    .bg-gradient-primary.sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.12);
        border-radius: 10px;
    }
    .bg-gradient-primary.sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.25);
    }

    /* 2. PENATAAN KONTEN UTAMA */
    #wrapper #content-wrapper {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
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
    }

    /* HOVER MENU UTAMA */
    .sidebar:not(.toggled) .nav-item .nav-link:hover {
        background-color: rgba(0, 0, 0, 0.15) !important; 
        color: #ffffff !important;
        padding-left: 1.5rem !important;
    }

    /* GAYA VISUAL MENU AKTIF */
    .sidebar-dark .nav-item.active .nav-link {
        color: #ffffff !important;
        font-weight: 700 !important;
        background-color: rgba(0, 0, 0, 0.2) !important;
    }
    .sidebar:not(.toggled) .nav-item.active .nav-link {
        border-left: 3.5px solid #1cc88a !important;
    }

    /* MINIMIZE / TOGGLED MODE */
    .sidebar.toggled .nav-item .nav-link span,
    .sidebar.toggled .sidebar-heading,
    .sidebar.toggled .sidebar-brand-text {
        display: none !important;
    }

    .sidebar-dark .sidebar-divider {
        border-top: 1px solid rgba(255, 255, 255, 0.06) !important;
    }
</style>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-start px-3 py-4 normal-link" href="../pembeli/dashboard.php">
        <div class="sidebar-brand-icon">
            <div class="bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width:52px; height:52px;">
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

    <li class="nav-item <?= ($page == 'dashboard.php' || $page == '') ? 'active' : ''; ?>">
        <a class="nav-link normal-link" href="../pembeli/dashboard.php">
            <i class="fas fa-fw fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-item <?= in_array($page, ['lihat_mobil.php', 'detail_mobil.php', 'pesan_mobil.php']) ? 'active' : ''; ?>">
        <a class="nav-link ajax-link" href="../pembeli/lihat_mobil.php">
            <i class="fas fa-fw fa-car"></i>
            <span>Lihat Mobil</span>
        </a>
    </li>

    <li class="nav-item <?= in_array($page, ['pesanan_saya.php', 'detail_pesanan.php']) ? 'active' : ''; ?>">
        <a class="nav-link ajax-link" href="../pembeli/pesanan_saya.php">
            <i class="fas fa-fw fa-shopping-cart"></i>
            <span>Pesanan Saya</span>
        </a>
    </li>

    <li class="nav-item <?= in_array($page, ['riwayat_pembayaran.php', 'pembayaran.php']) ? 'active' : ''; ?>">
        <a class="nav-link ajax-link" href="../pembeli/riwayat_pembayaran.php">
            <i class="fas fa-fw fa-credit-card"></i>
            <span>Riwayat Pembayaran</span>
        </a>
    </li>


    <li class="nav-item <?= ($page == 'status_administrasi.php') ? 'active' : ''; ?>">
        <a class="nav-link ajax-link" href="../pembeli/status_administrasi.php">
            <i class="fas fa-fw fa-file-signature"></i>
            <span>Status STNK/BPKB</span>
        </a>
    </li>

    <li class="nav-item <?= ($page == 'pengiriman_mobil.php') ? 'active' : ''; ?>">
        <a class="nav-link ajax-link" href="../pembeli/pengiriman_mobil.php">
            <i class="fas fa-fw fa-truck"></i>
            <span>Pengiriman Mobil</span>
        </a>
    </li>

    <li class="nav-item <?= ($page == 'riwayat_pembelian.php') ? 'active' : ''; ?>">
        <a class="nav-link ajax-link" href="../pembeli/riwayat_pembelian.php">
            <i class="fas fa-fw fa-history"></i>
            <span>Riwayat Pembelian</span>
        </a>
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

    // 1. Mempertahankan Status Toggled (Minimize)
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

    // 2. AJAX Engine Utama untuk ".ajax-link"
    const ajaxLinks = document.querySelectorAll(".ajax-link");
    ajaxLinks.forEach(link => {
        link.addEventListener("click", function(e) {
            e.preventDefault();
            const url = this.getAttribute("href");
            if (!url || url === "#") return;

            const contentContainer = document.getElementById("content") || document.getElementById("wrapper");
            if (contentContainer) {
                contentContainer.style.opacity = "0.5";
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
                .catch(() => window.location.href = url);
        });
    });

    // 3. Logika Update Class Active Secara Otomatis
    function updateActiveMenu(currentUrl) {
        document.querySelectorAll(".sidebar .nav-item").forEach(item => item.classList.remove("active"));
        const currentFile = currentUrl.split('/').pop().split('?')[0];
        let targetLink = document.querySelector(`.sidebar a[href*="${currentFile}"]`);
        if (targetLink) {
            const parentLi = targetLink.closest(".nav-item");
            if (parentLi) parentLi.classList.add("active");
        }
    }

    window.addEventListener("popstate", () => window.location.reload());
});
</script>
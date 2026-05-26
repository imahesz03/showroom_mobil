<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$username = $_SESSION['username'] ?? 'Admin';
?>

<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>


    <!-- Right -->
    <ul class="navbar-nav ml-auto">

        <li class="nav-item dropdown no-arrow">

            <a class="nav-link dropdown-toggle"
               href="#"
               id="userDropdown"
               role="button"
               data-toggle="dropdown"
               aria-haspopup="true"
               aria-expanded="false">

                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
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

            <!-- Dropdown -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                 aria-labelledby="userDropdown">

                <a class="dropdown-item" href="../admin/profil.php">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profil
                </a>
                <a class="dropdown-item" href="javascript:void(0)" onclick="toggleDarkMode()" id="darkModeText">

                    <i class="fas fa-moon fa-sm fa-fw mr-2 text-gray-400"></i>

                    <span id="darkModeLabel">Tema Gelap</span>

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
<style>
/* =========================
   DARK MODE FINAL FIX
========================= */

html.dark-mode,
body.dark-mode{
    background:#111827 !important;
    color:#e5e7eb !important;
}

/* LAYOUT */
html.dark-mode #wrapper,
html.dark-mode #content-wrapper,
html.dark-mode #content,
html.dark-mode .container-fluid{
    background:#111827 !important;
    color:#e5e7eb !important;
}

/* TOPBAR */
html.dark-mode .topbar,
html.dark-mode nav.topbar{
    background:#1f2937 !important;
    color:#e5e7eb !important;
    border-bottom:1px solid #374151 !important;
    box-shadow:0 4px 18px rgba(0,0,0,.35) !important;
}

html.dark-mode .topbar span,
html.dark-mode .topbar .small,
html.dark-mode .topbar .text-gray-600,
html.dark-mode .topbar .text-gray-700{
    color:#d1d5db !important;
}

/* SIDEBAR TETAP DEFAULT SB ADMIN */
html.dark-mode .sidebar,
html.dark-mode .bg-gradient-primary{
    background-color:#4e73df !important;
    background-image:linear-gradient(180deg,#4e73df 10%,#224abe 100%) !important;
}

html.dark-mode .sidebar-divider{
    border-top:1px solid rgba(255,255,255,.15) !important;
}

/* CARD DARK */
html.dark-mode .card{
    background:#1f2937 !important;
    color:#e5e7eb !important;
    border-color:#374151 !important;
}

html.dark-mode .card-body,
html.dark-mode .card-footer{
    background:#1f2937 !important;
    color:#e5e7eb !important;
}

html.dark-mode .card-header{
    background:#273449 !important;
    color:#ffffff !important;
    border-bottom:1px solid #374151 !important;
}

/* JANGAN HILANGKAN BORDER WARNA CARD STATISTIK */
html.dark-mode .border-left-primary{
    border-left:.25rem solid #4e73df !important;
}

html.dark-mode .border-left-success{
    border-left:.25rem solid #1cc88a !important;
}

html.dark-mode .border-left-info{
    border-left:.25rem solid #36b9cc !important;
}

html.dark-mode .border-left-warning{
    border-left:.25rem solid #f6c23e !important;
}

html.dark-mode .border-left-danger{
    border-left:.25rem solid #e74a3b !important;
}

/* WARNA TEKS TETAP NYALA */
html.dark-mode .text-primary{
    color:#4e9cff !important;
}

html.dark-mode .text-success{
    color:#1ff0a0 !important;
}

html.dark-mode .text-info{
    color:#36dff5 !important;
}

html.dark-mode .text-warning{
    color:#ffd95a !important;
}

html.dark-mode .text-danger{
    color:#ff6b6b !important;
}

/* TEKS NETRAL */
html.dark-mode h1,
html.dark-mode h2,
html.dark-mode h3,
html.dark-mode h4,
html.dark-mode h5,
html.dark-mode h6,
html.dark-mode label,
html.dark-mode .text-gray-900,
html.dark-mode .text-gray-800,
html.dark-mode .text-gray-700{
    color:#f9fafb !important;
}

html.dark-mode p,
html.dark-mode small,
html.dark-mode .small,
html.dark-mode .text-gray-600,
html.dark-mode .text-gray-500,
html.dark-mode .text-gray-400{
    color:#d1d5db !important;
}

/* ICON CARD STATISTIK */
html.dark-mode .card .text-gray-300{
    color:#e5e7eb !important;
    opacity:.95 !important;
}

/* CARD STATISTIK BIAR LEBIH HIDUP */
html.dark-mode .card.border-left-primary{
    box-shadow:0 0 0 1px rgba(78,115,223,.25), 0 8px 20px rgba(0,0,0,.25) !important;
}

html.dark-mode .card.border-left-success{
    box-shadow:0 0 0 1px rgba(28,200,138,.25), 0 8px 20px rgba(0,0,0,.25) !important;
}

html.dark-mode .card.border-left-warning{
    box-shadow:0 0 0 1px rgba(246,194,62,.25), 0 8px 20px rgba(0,0,0,.25) !important;
}

html.dark-mode .card.border-left-info{
    box-shadow:0 0 0 1px rgba(54,185,204,.25), 0 8px 20px rgba(0,0,0,.25) !important;
}

html.dark-mode .card.border-left-danger{
    box-shadow:0 0 0 1px rgba(231,74,59,.25), 0 8px 20px rgba(0,0,0,.25) !important;
}

/* TABLE */
html.dark-mode .table,
html.dark-mode .table td,
html.dark-mode .table th{
    background:#1f2937 !important;
    color:#e5e7eb !important;
    border-color:#374151 !important;
}

html.dark-mode .thead-light th,
html.dark-mode .table thead th{
    background:#374151 !important;
    color:#ffffff !important;
    border-color:#4b5563 !important;
}

html.dark-mode .table-hover tbody tr:hover td{
    background:#273449 !important;
}

/* FORM */
html.dark-mode .form-control,
html.dark-mode input,
html.dark-mode textarea,
html.dark-mode select{
    background:#111827 !important;
    color:#f9fafb !important;
    border-color:#4b5563 !important;
}

html.dark-mode .form-control:focus{
    background:#111827 !important;
    color:#ffffff !important;
    border-color:#60a5fa !important;
    box-shadow:0 0 0 .2rem rgba(96,165,250,.25) !important;
}

html.dark-mode .form-control::placeholder{
    color:#9ca3af !important;
}

/* BG WHITE DI DARK MODE */
html.dark-mode .bg-white,
html.dark-mode .card-footer.bg-white,
html.dark-mode footer.bg-white{
    background:#1f2937 !important;
    color:#e5e7eb !important;
}

/* DROPDOWN */
html.dark-mode .dropdown-menu{
    background:#1f2937 !important;
    border:1px solid #374151 !important;
    box-shadow:0 10px 25px rgba(0,0,0,.45) !important;
}

html.dark-mode .dropdown-item{
    color:#e5e7eb !important;
}

html.dark-mode .dropdown-item i{
    color:#9ca3af !important;
}

html.dark-mode .dropdown-item:hover{
    background:#374151 !important;
    color:#ffffff !important;
}

html.dark-mode .dropdown-divider{
    border-top-color:#374151 !important;
}

/* FOOTER */
html.dark-mode footer,
html.dark-mode .sticky-footer{
    background:#1f2937 !important;
    color:#d1d5db !important;
    border-top:1px solid #374151 !important;
}

/* BUTTON */
html.dark-mode .btn-primary{
    background:#4e73df !important;
    border-color:#4e73df !important;
    color:#ffffff !important;
}

html.dark-mode .btn-secondary{
    background:#4b5563 !important;
    border-color:#4b5563 !important;
    color:#ffffff !important;
}

/* BADGE TETAP WARNA */
html.dark-mode .badge-primary{
    background:#4e73df !important;
    color:#ffffff !important;
}

html.dark-mode .badge-success{
    background:#1cc88a !important;
    color:#ffffff !important;
}

html.dark-mode .badge-warning{
    background:#f6c23e !important;
    color:#1f2937 !important;
}

html.dark-mode .badge-info{
    background:#36b9cc !important;
    color:#ffffff !important;
}

html.dark-mode .badge-danger{
    background:#e74a3b !important;
    color:#ffffff !important;
}

/* SCROLL TOP */
html.dark-mode .scroll-to-top{
    background:#374151 !important;
}

html.dark-mode .scroll-to-top:hover{
    background:#4b5563 !important;
}
</style>

<script>
(function(){
    if(localStorage.getItem("darkMode") === "true"){
        document.documentElement.classList.add("dark-mode");
        document.body.classList.add("dark-mode");
    }

    updateDarkModeText();
})();

function toggleDarkMode(){
    document.documentElement.classList.toggle("dark-mode");
    document.body.classList.toggle("dark-mode");

    const aktif = document.documentElement.classList.contains("dark-mode");

    localStorage.setItem("darkMode", aktif ? "true" : "false");

    updateDarkModeText();
}

function updateDarkModeText(){
    const label = document.getElementById("darkModeLabel");
    const icon = document.getElementById("darkModeIcon");

    if(!label) return;

    if(document.documentElement.classList.contains("dark-mode")){
        label.innerText = "Tema Terang";

        if(icon){
            icon.className = "fas fa-sun fa-sm fa-fw mr-2 text-gray-400";
        }
    }else{
        label.innerText = "Tema Gelap";

        if(icon){
            icon.className = "fas fa-moon fa-sm fa-fw mr-2 text-gray-400";
        }
    }
}
</script>
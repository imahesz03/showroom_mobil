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
                    $srcFotoTopbar = "/showroom_mobil/uploads/" . $fotoTopbar;
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

/* Layout utama */
html.dark-mode #wrapper,
html.dark-mode #content-wrapper,
html.dark-mode #content,
html.dark-mode .container-fluid{
    background:#111827 !important;
    color:#e5e7eb !important;
}

/* TOPBAR GELAP */
html.dark-mode .topbar,
html.dark-mode nav.topbar{
    background:#1f2937 !important;
    color:#e5e7eb !important;
    border-bottom:1px solid #374151 !important;
    box-shadow:0 4px 18px rgba(0,0,0,.35) !important;
}

html.dark-mode .topbar h1,
html.dark-mode .topbar h2,
html.dark-mode .topbar h3,
html.dark-mode .topbar h4,
html.dark-mode .topbar h5,
html.dark-mode .topbar h6,
html.dark-mode .topbar .text-primary{
    color:#f9fafb !important;
}

html.dark-mode .topbar span,
html.dark-mode .topbar .text-gray-600,
html.dark-mode .topbar .text-gray-700,
html.dark-mode .topbar .small{
    color:#d1d5db !important;
}

/* SIDEBAR */
html.dark-mode .sidebar{
    background:#4e73df !important;
}

html.dark-mode .sidebar hr.sidebar-divider{
    border-top:1px solid rgba(255,255,255,.15)!important;
}

/* COLLAPSE MENU SIDEBAR */
html.dark-mode .sidebar .collapse-inner{
    background:#ffffff !important;
}

html.dark-mode .sidebar .collapse-header{
    color:#4e73df !important;
    font-weight:800 !important;
}

html.dark-mode .sidebar .collapse-item{
    color:#3a3b45 !important;
}

html.dark-mode .sidebar .collapse-item i{
    color:#858796 !important;
}

html.dark-mode .sidebar .collapse-item:hover{
    background:#eaecf4 !important;
    color:#2e59d9 !important;
}

/* CARD */
html.dark-mode .card,
html.dark-mode .card-body{
    background:#1f2937 !important;
    color:#e5e7eb !important;
    border-color:#374151 !important;
}

html.dark-mode .card-header{
    background:#273449 !important;
    color:#ffffff !important;
    border-bottom:1px solid #374151 !important;
}

/* TEXT */
html.dark-mode .text-gray-900,
html.dark-mode .text-gray-800,
html.dark-mode .text-gray-700,
html.dark-mode h1,
html.dark-mode h2,
html.dark-mode h3,
html.dark-mode h4,
html.dark-mode h5,
html.dark-mode h6,
html.dark-mode label{
    color:#f9fafb !important;
}

html.dark-mode .text-gray-600,
html.dark-mode .text-gray-500,
html.dark-mode .text-gray-400,
html.dark-mode p,
html.dark-mode small,
html.dark-mode .small{
    color:#d1d5db !important;
}

/* TABLE */
html.dark-mode .table,
html.dark-mode .table td,
html.dark-mode .table th{
    background:#1f2937 !important;
    color:#e5e7eb !important;
    border-color:#374151 !important;
}

html.dark-mode .table thead th,
html.dark-mode .thead-light th{
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

html.dark-mode .form-control::placeholder{
    color:#9ca3af !important;
}

/* DROPDOWN GELAP */
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

/* ALERT */
html.dark-mode .alert-success{
    background:#dcfce7 !important;
    color:#166534 !important;
}

html.dark-mode .alert-danger{
    background:#fee2e2 !important;
    color:#991b1b !important;
}

html.dark-mode .alert-warning{
    background:#fef3c7 !important;
    color:#92400e !important;
}

html.dark-mode .alert-info{
    background:#dbeafe !important;
    color:#1e40af !important;
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

    if(!label) return;

    if(document.documentElement.classList.contains("dark-mode")){
        label.innerText = "Tema Terang";
    } else {
        label.innerText = "Tema Gelap";
    }
}
</script>
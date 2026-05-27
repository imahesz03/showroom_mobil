<?php
session_start();
include "../config/koneksi.php";

if(empty($_SESSION['id_user'])){
    header("Location: ../auth/login.php");
    exit;
}

$id_user = (int) $_SESSION['id_user'];
$role    = $_SESSION['role'] ?? '';

/* AMBIL DATA USER */
$q_user = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user='$id_user' LIMIT 1");
$user = mysqli_fetch_assoc($q_user);

if(!$user){
    header("Location: ../auth/login.php");
    exit;
}

/* AMBIL DATA DETAIL SESUAI ROLE */
$tabel_detail = [
    "admin"   => "admin",
    "pembeli" => "pembeli",
    "penjual" => "penjual",
    "kurir"   => "kurir"
];

$detail = null;

if(isset($tabel_detail[$role])){
    $tabel = $tabel_detail[$role];

    $q_detail = mysqli_query($koneksi, 
        "SELECT * FROM $tabel WHERE id_user='$id_user' LIMIT 1"
    );

    $detail = mysqli_fetch_assoc($q_detail);
}

/* FLASH MESSAGE */
$flash_ok  = $_SESSION['flash_ok'] ?? '';
$flash_err = $_SESSION['flash_err'] ?? '';

unset($_SESSION['flash_ok'], $_SESSION['flash_err']);

/* FOTO PROFIL */
$foto = $_SESSION['foto_profil'] ?? '';

if(!empty($foto)){
    $foto_path = "../uploads/" . $foto;
} else {
    $foto_path = "";
}

$nama_tampil = $detail['nama'] ?? $user['username'];
$initials = strtoupper(substr($user['username'], 0, 2));

/* SIDEBAR SESUAI ROLE */
$sidebar_map = [
    "admin"   => "../includes/sidebar_admin.php",
    "pembeli" => "../includes/sidebar_pembeli.php",
    "penjual" => "../includes/sidebar_penjual.php",
    "kurir"   => "../includes/sidebar_kurir.php"
];

$sidebar_file = $sidebar_map[$role] ?? "";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Saya</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
    
    <style>
        /* CSS internal bawaan dipertahankan untuk kebutuhan fungsional avatar & password strength */
        .profile-avatar{
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #4e73df;
        }

        .profile-placeholder{
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4e73df, #224abe);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: bold;
            border: 4px solid #4e73df;
        }

        .foto-wrap{
            position: relative;
            display: inline-block;
        }

        .foto-btn{
            position: absolute;
            right: 5px;
            bottom: 5px;
            width: 36px;
            height: 36px;
            background: #4e73df;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 3px solid white;
            transition: 0.2s;
        }
        
        .foto-btn:hover {
            background: #2e59d9;
            transform: scale(1.05);
        }

        #inputFoto{
            display: none;
        }

        .password-wrap{
            position: relative;
        }

        .password-wrap input{
            padding-right: 45px;
        }

        .toggle-password{
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #858796;
            cursor: pointer;
        }

        .strength-bar{
            height: 5px;
            background: #e3e6f0;
            border-radius: 20px;
            overflow: hidden;
            margin-top: 8px;
        }

        .strength-fill{
            height: 100%;
            width: 0%;
            transition: .3s;
        }
    </style>
</head>

<body id="page-top">

<div id="wrapper">

    <?php 
    if($sidebar_file && file_exists($sidebar_file)){
        include $sidebar_file;
    }
    ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php 
            $pageTitle = "Profil Saya";
            include "../includes/topbar.php"; 
            ?>

            <div class="container-fluid">

                <!-- TITLE -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Profil Saya</h1>
                    </div>

                    <a href="dashboard.php" class="btn btn-secondary shadow-sm mt-3 mt-sm-0">
                        <i class="fas fa-arrow-left fa-sm text-white-50"></i>
                        Kembali
                    </a>
                </div>
                <!-- Alert Flash Messages -->
                <?php if($flash_ok){ ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?= htmlspecialchars($flash_ok); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php } ?>

                <?php if($flash_err){ ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?= htmlspecialchars($flash_err); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php } ?>

                <!-- Form Utama -->
                <form action="proses_profil.php" method="POST" enctype="multipart/form-data">
                    
                    <div class="row">
                        
                        <!-- Kolom Kiri: Foto Utama & Informasi Akun Pokok -->
                        <div class="col-lg-4">
                            
                            <!-- Card Header Foto Profil -->
                            <div class="card shadow mb-4 text-center">
                                <div class="card-body pt-5 pb-4">
                                    <div class="foto-wrap mb-3">
                                        <?php if(!empty($foto_path)){ ?>
                                            <img src="<?= htmlspecialchars($foto_path); ?>" 
                                                 class="profile-avatar shadow" 
                                                 id="fotoPreview"
                                                 alt="Foto Profil">
                                        <?php } else { ?>
                                            <div class="profile-placeholder shadow" id="fotoPreview">
                                                <?= htmlspecialchars($initials); ?>
                                            </div>
                                        <?php } ?>

                                        <label for="inputFoto" class="foto-btn shadow-sm" title="Ubah Foto">
                                            <i class="fas fa-camera"></i>
                                        </label>

                                        <input type="file" 
                                               name="foto_profil" 
                                               id="inputFoto"
                                               accept="image/jpeg,image/png,image/webp">
                                    </div>

                                    <h4 class="font-weight-bold text-gray-800 mb-2">
                                        <?= htmlspecialchars($nama_tampil); ?>
                                    </h4>

                                    <span class="badge badge-pill badge-primary px-3 py-2 mb-3 shadow-sm">
                                        <i class="fas fa-user shadow-sm mr-1"></i> <?= ucfirst(htmlspecialchars($role)); ?>
                                    </span>

                                    <p class="text-xs text-muted px-2">
                                        Format file JPG, PNG, WEBP maksimal 2 MB.
                                    </p>
                                    <small class="text-success font-weight-bold d-block mt-2" id="namaFile"></small>
                                </div>
                            </div>

                            <!-- Card Informasi Akun -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 bg-light">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-user-shield mr-2"></i>Informasi Akun
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="text-gray-700 font-weight-bold text-sm"><i class="fas fa-at mr-1 text-muted"></i> Username</label>
                                        <input type="text"
                                               name="username"
                                               class="form-control"
                                               value="<?= htmlspecialchars($user['username']); ?>"
                                               required>
                                    </div>

                                    
                                </div>
                            </div>

                        </div>

                        <!-- Kolom Kanan: Data Diri & Ganti Password -->
                        <div class="col-lg-8">

                            <!-- Card Data Diri -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 bg-light">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-id-card mr-2"></i>Data Diri Lengkap
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-gray-700 font-weight-bold text-sm"><i class="fas fa-font mr-1 text-muted"></i> Nama Lengkap</label>
                                                <input type="text"
                                                       name="nama"
                                                       class="form-control"
                                                       value="<?= htmlspecialchars($detail['nama'] ?? ''); ?>"
                                                       placeholder="Nama lengkap Anda"
                                                       required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-gray-700 font-weight-bold text-sm"><i class="fas fa-phone mr-1 text-muted"></i> No. HP / WhatsApp</label>
                                                <input type="text"
                                                       name="no_hp"
                                                       class="form-control"
                                                       value="<?= htmlspecialchars($detail['no_hp'] ?? ''); ?>"
                                                       placeholder="08xxxxxxxxxx">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-0">
                                        <label class="text-gray-700 font-weight-bold text-sm"><i class="fas fa-map-marked-alt mr-1 text-muted"></i> Alamat Lengkap</label>
                                        <textarea name="alamat"
                                                  class="form-control"
                                                  rows="4"
                                                  placeholder="Tuliskan alamat lengkap rumah/kantor Anda..."><?= htmlspecialchars($detail['alamat'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Keamanan / Password -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 bg-light">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-lock mr-2"></i>Keamanan Akun (Ganti Password)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info py-2 px-3 text-sm mb-3">
                                        <i class="fas fa-info-circle mr-1"></i> Biarkan kolom di bawah ini <strong>kosong</strong> jika Anda tidak ingin mengganti password lama Anda.
                                    </div>

                                    <div class="form-group">
                                        <label class="text-gray-700 font-weight-bold text-sm">Password Saat Ini</label>
                                        <div class="password-wrap">
                                            <input type="password"
                                                   name="password_lama"
                                                   id="pwLama"
                                                   class="form-control"
                                                   placeholder="Masukkan password saat ini">
                                            <button type="button" 
                                                    class="toggle-password"
                                                    onclick="togglePassword('pwLama', this)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-md-0">
                                                <label class="text-gray-700 font-weight-bold text-sm">Password Baru</label>
                                                <div class="password-wrap">
                                                    <input type="password"
                                                           name="password_baru"
                                                           id="pwBaru"
                                                           class="form-control"
                                                           placeholder="Minimal 6 karakter"
                                                           oninput="cekKekuatanPassword(this.value)">
                                                    <button type="button" 
                                                            class="toggle-password"
                                                            onclick="togglePassword('pwBaru', this)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                                <div class="strength-bar">
                                                    <div class="strength-fill" id="strengthFill"></div>
                                                </div>
                                                <small id="strengthText" class="text-muted d-block mt-1 font-weight-bold"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-0">
                                                <label class="text-gray-700 font-weight-bold text-sm">Konfirmasi Password Baru</label>
                                                <div class="password-wrap">
                                                    <input type="password"
                                                           name="password_konfirm"
                                                           id="pwKonfirm"
                                                           class="form-control"
                                                           placeholder="Ulangi password baru"
                                                           oninput="cekKonfirmasiPassword()">
                                                    <button type="button" 
                                                            class="toggle-password"
                                                            onclick="togglePassword('pwKonfirm', this)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                                <small id="konfirmasiText" class="d-block mt-1 font-weight-bold"></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Mengganti mx-auto menjadi ml-auto agar mendorong card ke pojok kanan -->
                                <div class="col-md-6 ml-auto">
                                    
                                    <div class="card shadow mb-5">
                                        <div class="card-body d-flex justify-content-between align-items-center py-3">
                                            <a href="javascript:history.back()" class="btn btn-light text-gray-700 border">
                                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                                            </a>
                                            <button type="submit" class="btn btn-primary px-4">
                                                <i class="fas fa-save mr-1"></i> Simpan Perubahan
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../assets/js/sb-admin-2.min.js"></script>

<script>
const inputFoto = document.getElementById("inputFoto");
const namaFile = document.getElementById("namaFile");

inputFoto.addEventListener("change", function(){
    const file = this.files[0];

    if(!file){
        return;
    }

    if(file.size > 2 * 1024 * 1024){
        alert("Ukuran foto maksimal 2 MB.");
        this.value = "";
        return;
    }

    const reader = new FileReader();

    reader.onload = function(e){
        let preview = document.getElementById("fotoPreview");

        if(preview.tagName === "DIV"){
            const img = document.createElement("img");
            img.id = "fotoPreview";
            img.className = "profile-avatar shadow";
            img.alt = "Foto Profil";

            preview.replaceWith(img);
            preview = img;
        }

        preview.src = e.target.result;
    }

    reader.readAsDataURL(file);

    namaFile.innerHTML = '<i class="fas fa-check-circle mr-1"></i> ' + file.name + ' siap diupload';
});

function togglePassword(id, button){
    const input = document.getElementById(id);
    const icon = button.querySelector("i");

    if(input.type === "password"){
        input.type = "text";
        icon.className = "fas fa-eye-slash";
    } else {
        input.type = "password";
        icon.className = "fas fa-eye";
    }
}

function cekKekuatanPassword(value){
    const fill = document.getElementById("strengthFill");
    const text = document.getElementById("strengthText");

    if(value.length === 0){
        fill.style.width = "0%";
        fill.style.background = "transparent";
        text.innerHTML = "";
        return;
    }

    let score = 0;

    if(value.length >= 6) score++;
    if(value.length >= 10) score++;
    if(/[A-Z]/.test(value)) score++;
    if(/[0-9]/.test(value)) score++;
    if(/[^A-Za-z0-9]/.test(value)) score++;

    if(score <= 1){
        fill.style.width = "25%";
        fill.style.background = "#e74a3b";
        text.className = "text-danger text-xs";
        text.innerHTML = "<i class='fas fa-times-circle'></i> Password lemah";
    } else if(score <= 3){
        fill.style.width = "60%";
        fill.style.background = "#f6c23e";
        text.className = "text-warning text-xs";
        text.innerHTML = "<i class='fas fa-exclamation-triangle'></i> Password cukup";
    } else {
        fill.style.width = "100%";
        fill.style.background = "#1cc88a";
        text.className = "text-success text-xs";
        text.innerHTML = "<i class='fas fa-check-circle'></i> Password kuat";
    }
}

function cekKonfirmasiPassword(){
    const baru = document.getElementById("pwBaru").value;
    const konfirm = document.getElementById("pwKonfirm").value;
    const text = document.getElementById("konfirmasiText");

    if(konfirm.length === 0){
        text.innerHTML = "";
        return;
    }

    if(baru === konfirm){
        text.className = "text-success text-xs";
        text.innerHTML = "<i class='fas fa-check-circle'></i> Password cocok";
    } else {
        text.className = "text-danger text-xs";
        text.innerHTML = "<i class='fas fa-times-circle'></i> Password tidak cocok";
    }
}

document.querySelector("form").addEventListener("submit", function(e){
    const lama = document.getElementById("pwLama").value;
    const baru = document.getElementById("pwBaru").value;
    const konfirm = document.getElementById("pwKonfirm").value;

    if(lama || baru || konfirm){
        if(!lama){
            e.preventDefault();
            alert("Password lama wajib diisi.");
            return;
        }

        if(baru.length < 6){
            e.preventDefault();
            alert("Password baru minimal 6 karakter.");
            return;
        }

        if(baru !== konfirm){
            e.preventDefault();
            alert("Konfirmasi password tidak cocok.");
            return;
        }
    }
});
</script>

</body>
</html>
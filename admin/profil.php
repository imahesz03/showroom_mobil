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

    <style>
        .profile-avatar{
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #4e73df;
        }

        .profile-placeholder{
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4e73df, #224abe);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: bold;
            border: 4px solid #4e73df;
        }

        .foto-wrap{
            position: relative;
            display: inline-block;
        }

        .foto-btn{
            position: absolute;
            right: 3px;
            bottom: 3px;
            width: 34px;
            height: 34px;
            background: #4e73df;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid white;
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

                <h1 class="h3 mb-4 text-gray-800">Profil Saya</h1>

                <?php if($flash_ok){ ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?= htmlspecialchars($flash_ok); ?>
                    </div>
                <?php } ?>

                <?php if($flash_err){ ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= htmlspecialchars($flash_err); ?>
                    </div>
                <?php } ?>

                <form action="proses_profil.php" method="POST" enctype="multipart/form-data">

                    <div class="card shadow mb-4">
                        <div class="card-body d-flex align-items-center">

                            <div class="foto-wrap mr-4">

                                <?php if(!empty($foto_path)){ ?>
                                    <img src="<?= htmlspecialchars($foto_path); ?>" 
                                         class="profile-avatar" 
                                         id="fotoPreview"
                                         alt="Foto Profil">
                                <?php } else { ?>
                                    <div class="profile-placeholder" id="fotoPreview">
                                        <?= htmlspecialchars($initials); ?>
                                    </div>
                                <?php } ?>

                                <label for="inputFoto" class="foto-btn">
                                    <i class="fas fa-camera"></i>
                                </label>

                                <input type="file" 
                                       name="foto_profil" 
                                       id="inputFoto"
                                       accept="image/jpeg,image/png,image/webp">
                            </div>

                            <div>
                                <h4 class="font-weight-bold text-gray-800 mb-1">
                                    <?= htmlspecialchars($nama_tampil); ?>
                                </h4>

                                <span class="badge badge-primary mb-2">
                                    <?= ucfirst(htmlspecialchars($role)); ?>
                                </span>

                                <p class="text-muted mb-0">
                                    Klik ikon kamera untuk mengganti foto profil.
                                    Format JPG, PNG, WEBP maksimal 2 MB.
                                </p>

                                <small class="text-success" id="namaFile"></small>
                            </div>

                        </div>
                    </div>

                    <div class="row">

                        <div class="col-lg-6">

                            <div class="card shadow mb-4">

                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-user-shield"></i>
                                        Informasi Akun
                                    </h6>
                                </div>

                                <div class="card-body">

                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text"
                                               name="username"
                                               class="form-control"
                                               value="<?= htmlspecialchars($user['username']); ?>"
                                               required>
                                    </div>

                                    <div class="form-group">
                                        <label>Role</label>
                                        <input type="text"
                                               class="form-control"
                                               value="<?= ucfirst(htmlspecialchars($role)); ?>"
                                               readonly>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-lg-6">

                            <div class="card shadow mb-4">

                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-id-card"></i>
                                        Data Diri
                                    </h6>
                                </div>

                                <div class="card-body">

                                    <div class="form-group">
                                        <label>Nama Lengkap</label>
                                        <input type="text"
                                               name="nama"
                                               class="form-control"
                                               value="<?= htmlspecialchars($detail['nama'] ?? ''); ?>"
                                               required>
                                    </div>

                                    <div class="form-group">
                                        <label>No. HP</label>
                                        <input type="text"
                                               name="no_hp"
                                               class="form-control"
                                               value="<?= htmlspecialchars($detail['no_hp'] ?? ''); ?>"
                                               placeholder="08xxxxxxxxxx">
                                    </div>

                                    <div class="form-group">
                                        <label>Alamat</label>
                                        <textarea name="alamat"
                                                  class="form-control"
                                                  rows="4"
                                                  placeholder="Alamat lengkap"><?= htmlspecialchars($detail['alamat'] ?? ''); ?></textarea>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="card shadow mb-4">

                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-lock"></i>
                                Ganti Password
                            </h6>
                        </div>

                        <div class="card-body">

                            <p class="text-muted">
                                Kosongkan semua kolom password jika tidak ingin mengganti password.
                            </p>

                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Password Lama</label>

                                        <div class="password-wrap">
                                            <input type="password"
                                                   name="password_lama"
                                                   id="pwLama"
                                                   class="form-control"
                                                   placeholder="Password lama">

                                            <button type="button" 
                                                    class="toggle-password"
                                                    onclick="togglePassword('pwLama', this)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Password Baru</label>

                                        <div class="password-wrap">
                                            <input type="password"
                                                   name="password_baru"
                                                   id="pwBaru"
                                                   class="form-control"
                                                   placeholder="Password baru"
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

                                        <small id="strengthText" class="text-muted"></small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Konfirmasi Password</label>

                                        <div class="password-wrap">
                                            <input type="password"
                                                   name="password_konfirm"
                                                   id="pwKonfirm"
                                                   class="form-control"
                                                   placeholder="Ulangi password"
                                                   oninput="cekKonfirmasiPassword()">

                                            <button type="button" 
                                                    class="toggle-password"
                                                    onclick="togglePassword('pwKonfirm', this)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>

                                        <small id="konfirmasiText"></small>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="d-flex justify-content-end mb-5">
                        <a href="javascript:history.back()" class="btn btn-secondary mr-2">
                            <i class="fas fa-arrow-left"></i>
                            Kembali
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Simpan Perubahan
                        </button>
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
            img.className = "profile-avatar";
            img.alt = "Foto Profil";

            preview.replaceWith(img);
            preview = img;
        }

        preview.src = e.target.result;
    }

    reader.readAsDataURL(file);

    namaFile.innerHTML = '<i class="fas fa-check"></i> ' + file.name + ' siap diupload';
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
        text.className = "text-danger";
        text.innerHTML = "Password lemah";
    } else if(score <= 3){
        fill.style.width = "60%";
        fill.style.background = "#f6c23e";
        text.className = "text-warning";
        text.innerHTML = "Password cukup";
    } else {
        fill.style.width = "100%";
        fill.style.background = "#1cc88a";
        text.className = "text-success";
        text.innerHTML = "Password kuat";
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
        text.className = "text-success";
        text.innerHTML = "Password cocok";
    } else {
        text.className = "text-danger";
        text.innerHTML = "Password tidak cocok";
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
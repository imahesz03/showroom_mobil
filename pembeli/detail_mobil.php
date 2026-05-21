<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "pembeli") {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id']) || $_GET['id'] == "") {
    header("Location: lihat_mobil.php");
    exit;
}

$id_mobil = mysqli_real_escape_string($koneksi, $_GET['id']);

function rupiah($angka)
{
    return "Rp " . number_format((float)$angka, 0, ',', '.');
}

$queryMobil = mysqli_query($koneksi, "
    SELECT * FROM mobil 
    WHERE id_mobil = '$id_mobil'
");

if (mysqli_num_rows($queryMobil) == 0) {
    header("Location: lihat_mobil.php");
    exit;
}

$mobil = mysqli_fetch_assoc($queryMobil);

$foto = !empty($mobil['foto'])
    ? "../uploads/" . $mobil['foto']
    : "../assets/img/undraw_posting_photo.svg";

$tersedia = ($mobil['status'] == 'tersedia' && $mobil['stok'] > 0);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Mobil - Galaxy Showroom</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>

<body id="page-top">

<div id="wrapper">

    <?php include "../includes/sidebar_pembeli.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php include "../includes/topbar.php"; ?>

            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Detail Mobil</h1>
                        <p class="mb-0 text-gray-600">
                            Informasi lengkap mobil yang tersedia di Galaxy Showroom.
                        </p>
                    </div>

                    <a href="lihat_mobil.php" class="btn btn-secondary btn-sm shadow-sm mt-3 mt-sm-0">
                        <i class="fas fa-arrow-left fa-sm mr-1"></i>
                        Kembali
                    </a>
                </div>

                <div class="row">

                    <div class="col-lg-5 mb-4">
                        <div class="card shadow border-0">
                            <div class="card-body p-3">

                                <img src="<?= htmlspecialchars($foto); ?>"
                                     onerror="this.src='../assets/img/undraw_posting_photo.svg'"
                                     class="img-fluid rounded shadow-sm"
                                     style="width:100%; height:340px; object-fit:cover;">

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7 mb-4">
                        <div class="card shadow border-0 h-100">

                            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                                <h5 class="m-0 font-weight-bold text-primary">
                                    <?= htmlspecialchars($mobil['nama_mobil']); ?>
                                </h5>

                                <?php if ($tersedia) : ?>
                                    <span class="badge badge-success px-3 py-2">
                                        Tersedia
                                    </span>
                                <?php else : ?>
                                    <span class="badge badge-danger px-3 py-2">
                                        Tidak Tersedia
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="card-body">

                                <h4 class="font-weight-bold text-primary mb-4">
                                    <?= rupiah($mobil['harga']); ?>
                                </h4>

                                <div class="row mb-4">

                                    <div class="col-md-6 mb-3">
                                        <div class="card border-left-primary h-100 py-2">
                                            <div class="card-body py-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Tahun
                                                </div>
                                                <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                    <?= htmlspecialchars($mobil['tahun']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="card border-left-success h-100 py-2">
                                            <div class="card-body py-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    Stok
                                                </div>
                                                <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                    <?= htmlspecialchars($mobil['stok']); ?> Unit
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="mb-4">
                                    <h6 class="font-weight-bold text-gray-800">
                                        Deskripsi Mobil
                                    </h6>

                                    <p class="text-gray-600 mb-0" style="line-height:1.8;">
                                        <?= nl2br(htmlspecialchars($mobil['deskripsi'])); ?>
                                    </p>
                                </div>

                            </div>

                            <div class="card-footer bg-white border-0 pb-4">

                                <?php if ($tersedia) : ?>

                                    <div class="row">
                                        <div class="col-md-6 mb-2 mb-md-0">
                                            <a href="lihat_mobil.php" class="btn btn-outline-secondary btn-block">
                                                <i class="fas fa-arrow-left mr-1"></i>
                                                Kembali
                                            </a>
                                        </div>

                                        <div class="col-md-6">
                                            <a href="pesan_mobil.php?id=<?= $mobil['id_mobil']; ?>"
                                               class="btn btn-primary btn-block">
                                                <i class="fas fa-shopping-cart mr-1"></i>
                                                Pesan Mobil
                                            </a>
                                        </div>
                                    </div>

                                <?php else : ?>

                                    <div class="row">
                                        <div class="col-md-6 mb-2 mb-md-0">
                                            <a href="lihat_mobil.php" class="btn btn-outline-secondary btn-block">
                                                <i class="fas fa-arrow-left mr-1"></i>
                                                Kembali
                                            </a>
                                        </div>

                                        <div class="col-md-6">
                                            <button class="btn btn-secondary btn-block" disabled>
                                                <i class="fas fa-ban mr-1"></i>
                                                Mobil Tidak Tersedia
                                            </button>
                                        </div>
                                    </div>

                                <?php endif; ?>

                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>



    </div>

</div>

<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../assets/js/sb-admin-2.min.js"></script>

</body>
</html>
<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "pembeli") {
    header("Location: ../auth/login.php");
    exit;
}

function rupiah($angka)
{
    return "Rp " . number_format((float)$angka, 0, ',', '.');
}

$queryMobil = mysqli_query($koneksi, "
    SELECT * FROM mobil
    ORDER BY id_mobil DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Lihat Mobil - Galaxy Showroom</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
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
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Lihat Mobil</h1>
                        <p class="mb-0 text-gray-600">
                            Pilih mobil yang tersedia dan lakukan pemesanan sesuai kebutuhanmu.
                        </p>
                    </div>
                </div>

                <div class="card shadow mb-4">

                    <div class="card-header py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary mb-3 mb-md-0">
                            Daftar Mobil Showroom
                        </h6>

                        <input type="text"
                               id="searchInput"
                               class="form-control"
                               style="max-width: 330px;"
                               placeholder="Cari nama mobil, tahun, harga, status..."
                               onkeyup="filterMobil()">
                    </div>

                    <div class="card-body">

                        <?php if (mysqli_num_rows($queryMobil) > 0) : ?>

                            <div class="row" id="listMobil">

                                <?php while ($mobil = mysqli_fetch_assoc($queryMobil)) : ?>

                                    <?php
                                    $foto = !empty($mobil['foto'])
                                        ? "../uploads/" . $mobil['foto']
                                        : "../assets/img/undraw_posting_photo.svg";

                                    $tersedia = ($mobil['status'] == 'tersedia' && $mobil['stok'] > 0);
                                    ?>

                                    <div class="col-xl-4 col-md-6 mb-4 mobil-item">

                                        <div class="card h-100 shadow-sm border-0">

                                            <img src="<?= htmlspecialchars($foto); ?>"
                                                 onerror="this.src='../assets/img/undraw_posting_photo.svg'"
                                                 class="card-img-top"
                                                 style="height:210px; object-fit:cover; border-top-left-radius:.35rem; border-top-right-radius:.35rem;">

                                            <div class="card-body">

                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h5 class="font-weight-bold text-gray-800 mb-0">
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

                                                <div class="text-primary font-weight-bold mb-2">
                                                    <?= rupiah($mobil['harga']); ?>
                                                </div>

                                                <div class="small text-gray-600 mb-3">
                                                    <i class="fas fa-calendar-alt mr-1"></i>
                                                    Tahun <?= htmlspecialchars($mobil['tahun']); ?>

                                                    <span class="mx-2">|</span>

                                                    <i class="fas fa-box mr-1"></i>
                                                    Stok <?= htmlspecialchars($mobil['stok']); ?>
                                                </div>

                                                <p class="text-gray-600 mb-3" style="min-height:72px;">
                                                    <?= htmlspecialchars(substr($mobil['deskripsi'], 0, 120)); ?>
                                                    <?= strlen($mobil['deskripsi']) > 120 ? '...' : ''; ?>
                                                </p>

                                            </div>

                                            <div class="card-footer bg-white border-0 pb-3">

                                                <?php if ($tersedia) : ?>

                                                    <div class="row">
                                                        <div class="col-6 pr-1">
                                                            <a href="detail_mobil.php?id=<?= $mobil['id_mobil']; ?>"
                                                               class="btn btn-info btn-block">
                                                                <i class="fas fa-eye mr-1"></i>
                                                                Detail
                                                            </a>
                                                        </div>

                                                        <div class="col-6 pl-1">
                                                            <a href="pesan_mobil.php?id=<?= $mobil['id_mobil']; ?>"
                                                               class="btn btn-primary btn-block">
                                                                <i class="fas fa-shopping-cart mr-1"></i>
                                                                Pesan
                                                            </a>
                                                        </div>
                                                    </div>

                                                <?php else : ?>

                                                    <div class="row">
                                                        <div class="col-6 pr-1">
                                                            <a href="detail_mobil.php?id=<?= $mobil['id_mobil']; ?>"
                                                               class="btn btn-info btn-block">
                                                                <i class="fas fa-eye mr-1"></i>
                                                                Detail
                                                            </a>
                                                        </div>

                                                        <div class="col-6 pl-1">
                                                            <button class="btn btn-secondary btn-block" disabled>
                                                                <i class="fas fa-ban mr-1"></i>
                                                                Habis
                                                            </button>
                                                        </div>
                                                    </div>

                                                <?php endif; ?>

                                            </div>

                                        </div>

                                    </div>

                                <?php endwhile; ?>

                            </div>

                            <div id="noResult" class="text-center text-muted py-5" style="display:none;">
                                <i class="fas fa-search fa-3x text-gray-300 mb-3"></i>
                                <h5>Mobil tidak ditemukan</h5>
                                <p class="mb-0">Coba gunakan kata kunci lain.</p>
                            </div>

                        <?php else : ?>

                            <div class="text-center py-5">
                                <i class="fas fa-car fa-3x text-gray-300 mb-3"></i>
                                <h5 class="text-gray-800">Belum Ada Mobil</h5>
                                <p class="text-muted mb-0">
                                    Data mobil belum tersedia di showroom.
                                </p>
                            </div>

                        <?php endif; ?>

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

<script>
function filterMobil() {
    let input = document.getElementById("searchInput").value.toLowerCase();
    let items = document.querySelectorAll(".mobil-item");
    let noResult = document.getElementById("noResult");
    let visible = 0;

    items.forEach(function(item) {
        let text = item.innerText.toLowerCase();

        if (text.includes(input)) {
            item.style.display = "";
            visible++;
        } else {
            item.style.display = "none";
        }
    });

    noResult.style.display = visible === 0 ? "block" : "none";
}
</script>

</body>
</html>
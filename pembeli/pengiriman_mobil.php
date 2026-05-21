<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "pembeli") {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['id_user'] ?? 0;

/*
|------------------------------------------------------
| AMBIL DATA PEMBELI
|------------------------------------------------------
*/
$qPembeli = mysqli_query($koneksi, "
    SELECT id_pembeli
    FROM pembeli
    WHERE id_user = '$id_user'
    LIMIT 1
");

if (!$qPembeli || mysqli_num_rows($qPembeli) == 0) {
    echo "<script>
        alert('Data pembeli tidak ditemukan!');
        window.location='../auth/login.php';
    </script>";
    exit;
}

$pembeli = mysqli_fetch_assoc($qPembeli);
$id_pembeli = $pembeli['id_pembeli'];

/*
|------------------------------------------------------
| DATA PENGIRIMAN
|------------------------------------------------------
*/
$query = mysqli_query($koneksi, "
    SELECT 
        pengiriman.*,

        pemesanan.id_pemesanan,
        pemesanan.status AS status_pesanan,

        mobil.nama_mobil,
        mobil.tahun,
        mobil.foto,

        kurir.nama AS nama_kurir,
        kurir.no_hp AS telepon_kurir

    FROM pengiriman

    LEFT JOIN pemesanan
    ON pengiriman.id_pemesanan = pemesanan.id_pemesanan

    LEFT JOIN mobil
    ON pemesanan.id_mobil = mobil.id_mobil

    LEFT JOIN kurir
    ON pengiriman.id_kurir = kurir.id_kurir

    WHERE pemesanan.id_pembeli = '$id_pembeli'

    ORDER BY pengiriman.id_pengiriman DESC
");

if (!$query) {
    die("Query pengiriman error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pengiriman Mobil - Galaxy Showroom</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>

<body id="page-top">

<div id="wrapper">

    <?php include "../includes/sidebar_pembeli.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php 
            $pageTitle = "Pengiriman Mobil";
            include "../includes/topbar.php"; 
            ?>

            <div class="container-fluid">

                <!-- HEADER -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">

                    <div>
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold">
                            Pengiriman Mobil
                        </h1>

                        <p class="mb-0 text-gray-600">
                            Pantau status pengiriman mobil kamu.
                        </p>
                    </div>

                    <a href="pesanan_saya.php"
                       class="btn btn-primary btn-sm shadow-sm">

                        <i class="fas fa-shopping-cart mr-1"></i>
                        Pesanan Saya

                    </a>

                </div>

                <!-- CARD -->
                <div class="card shadow mb-4">

                    <div class="card-header py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">

                        <h6 class="m-0 font-weight-bold text-primary mb-3 mb-md-0">
                            <i class="fas fa-truck mr-1"></i>
                            Status Pengiriman
                        </h6>

                        <input type="text"
                               id="searchInput"
                               class="form-control form-control-sm"
                               style="max-width:320px;"
                               placeholder="Cari mobil atau kurir..."
                               onkeyup="filterTable()">

                    </div>

                    <div class="card-body">

                        <?php if (mysqli_num_rows($query) > 0) : ?>

                            <div class="table-responsive">

                                <table class="table table-bordered table-hover"
                                       id="pengirimanTable"
                                       width="100%"
                                       cellspacing="0">

                                    <thead class="thead-light text-center">

                                        <tr>

                                            <th width="80">Foto</th>
                                            <th>Mobil</th>
                                            <th>Kurir</th>
                                            <th>No. Telp</th>
                                            <th>Status</th>
                                            <th>Bukti</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                    <?php while ($row = mysqli_fetch_assoc($query)) : ?>

                                        <?php
                                        $foto = !empty($row['foto'])
                                            ? "../uploads/" . $row['foto']
                                            : "../assets/img/undraw_posting_photo.svg";

                                        $status = strtolower($row['status'] ?? 'diproses');

                                        $badge = "secondary";

                                        if ($status == "diproses") {
                                            $badge = "warning";
                                        } elseif ($status == "dikirim") {
                                            $badge = "info";
                                        } elseif ($status == "selesai") {
                                            $badge = "success";
                                        }
                                        ?>

                                        <tr>

                                            <!-- FOTO -->
                                            <td class="text-center align-middle">

                                                <img src="<?= htmlspecialchars($foto); ?>"
                                                     class="img-thumbnail"
                                                     width="70"
                                                     height="55"
                                                     style="object-fit:cover;"
                                                     onerror="this.src='../assets/img/undraw_posting_photo.svg'">

                                            </td>

                                            <!-- MOBIL -->
                                            <td class="align-middle">

                                                <div class="font-weight-bold text-gray-800">
                                                    <?= htmlspecialchars($row['nama_mobil'] ?? '-'); ?>
                                                </div>

                                                <div class="small text-gray-500">
                                                    Tahun <?= htmlspecialchars($row['tahun'] ?? '-'); ?>
                                                </div>

                                            </td>

                                            <!-- KURIR -->
                                            <td class="text-center align-middle">

                                                <?= !empty($row['nama_kurir'])
                                                    ? htmlspecialchars($row['nama_kurir'])
                                                    : '-'; ?>

                                            </td>

                                            <!-- TELEPON -->
                                            <td class="text-center align-middle">

                                                <?= !empty($row['telepon_kurir'])
                                                    ? htmlspecialchars($row['telepon_kurir'])
                                                    : '-'; ?>

                                            </td>

                                            <!-- STATUS -->
                                            <td class="text-center align-middle">

                                                <span class="badge badge-<?= $badge; ?> px-3 py-2">

                                                    <?= ucfirst($status); ?>

                                                </span>

                                            </td>

                                            <!-- BUKTI -->
                                            <td class="text-center align-middle">

                                                <?php if (
                                                    !empty($row['bukti_pengiriman']) &&
                                                    $row['bukti_pengiriman'] != "-"
                                                ) : ?>

                                                    <a href="../uploads/<?= htmlspecialchars($row['bukti_pengiriman']); ?>"
                                                       target="_blank"
                                                       class="btn btn-success btn-sm">

                                                        <i class="fas fa-image"></i>

                                                    </a>

                                                <?php else : ?>

                                                    <span class="text-muted">-</span>

                                                <?php endif; ?>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                    </tbody>

                                </table>

                            </div>

                        <?php else : ?>

                            <div class="text-center py-5">

                                <i class="fas fa-truck fa-3x text-gray-300 mb-3"></i>

                                <h5 class="text-gray-800">
                                    Belum Ada Pengiriman
                                </h5>

                                <p class="text-muted mb-4">
                                    Data pengiriman mobil belum tersedia.
                                </p>

                                <a href="pesanan_saya.php"
                                   class="btn btn-primary">

                                    <i class="fas fa-shopping-cart mr-1"></i>
                                    Pesanan Saya

                                </a>

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
function filterTable() {

    let input = document.getElementById("searchInput").value.toLowerCase();
    let table = document.getElementById("pengirimanTable");

    if (!table) return;

    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {

        let text = tr[i].innerText.toLowerCase();

        tr[i].style.display = text.includes(input)
            ? ""
            : "none";
    }
}
</script>

</body>
</html>
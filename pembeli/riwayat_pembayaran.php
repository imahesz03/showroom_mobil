<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "pembeli") {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['id_user'] ?? 0;

function rupiah($angka)
{
    return "Rp " . number_format((float)$angka, 0, ',', '.');
}

/*
|------------------------------------------------------
| AMBIL DATA PEMBELI LOGIN
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
| DATA RIWAYAT PEMBAYARAN
|------------------------------------------------------
*/
$query = mysqli_query($koneksi, "
    SELECT 
        pb.*,

        p.id_pemesanan,
        p.id_pembeli,
        p.status AS status_pesanan,
        p.tanggal_pesan,

        m.nama_mobil,
        m.tahun,
        m.foto

    FROM pembayaran pb

    JOIN pemesanan p 
    ON pb.id_pemesanan = p.id_pemesanan

    JOIN mobil m 
    ON p.id_mobil = m.id_mobil

    WHERE p.id_pembeli = '$id_pembeli'

    ORDER BY pb.id_pembayaran DESC
");

if (!$query) {
    die("Query riwayat pembayaran error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Riwayat Pembayaran - Galaxy Showroom</title>

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
            $pageTitle = "Riwayat Pembayaran";
            include "../includes/topbar.php"; 
            ?>

            <div class="container-fluid">

                <!-- HEADER -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">

                    <div>
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold">
                            Riwayat Pembayaran
                        </h1>

                        <p class="mb-0 text-gray-600">
                            Daftar pembayaran booking, DP, dan pelunasan yang sudah kamu lakukan.
                        </p>
                    </div>

                    <a href="pesanan_saya.php" class="btn btn-primary btn-sm shadow-sm mt-3 mt-sm-0">
                        <i class="fas fa-shopping-cart mr-1"></i>
                        Pesanan Saya
                    </a>

                </div>

                <!-- CARD -->
                <div class="card shadow mb-4">

                    <div class="card-header py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">

                        <h6 class="m-0 font-weight-bold text-primary mb-3 mb-md-0">
                            <i class="fas fa-credit-card mr-1"></i>
                            Data Riwayat Pembayaran
                        </h6>

                        <input type="text"
                               id="searchInput"
                               class="form-control form-control-sm"
                               style="max-width: 320px;"
                               placeholder="Cari mobil, jenis, metode..."
                               onkeyup="filterTable()">

                    </div>

                    <div class="card-body">

                        <?php if (mysqli_num_rows($query) > 0) : ?>

                            <div class="table-responsive">

                                <table class="table table-bordered table-hover" id="paymentTable" width="100%" cellspacing="0">

                                    <thead class="thead-light text-center">
                                        <tr>
                                            <th>Mobil</th>
                                            <th>Jenis Bayar</th>
                                            <th>Metode</th>
                                            <th>Jumlah</th>
                                            <th>Tanggal</th>
                                            <th>Bukti</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                    <?php while ($row = mysqli_fetch_assoc($query)) : ?>

                                        <?php


                                        $jenis = strtolower($row['jenis_pembayaran'] ?? '-');
                                        $metode = strtolower($row['metode_bayar'] ?? '-');
                                        $status = strtolower($row['status_pesanan'] ?? '-');

                                        $badgeJenis = "secondary";
                                        $textJenis  = ucfirst($jenis);

                                        if ($jenis == "booking") {
                                            $badgeJenis = "warning";
                                            $textJenis = "Booking";
                                        } elseif ($jenis == "dp") {
                                            $badgeJenis = "info";
                                            $textJenis = "DP";
                                        } elseif ($jenis == "pelunasan") {
                                            $badgeJenis = "success";
                                            $textJenis = "Pelunasan";
                                        }

                                        $badgeStatus = "secondary";

                                        if ($status == "booking") {
                                            $badgeStatus = "warning";
                                        } elseif ($status == "dp") {
                                            $badgeStatus = "info";
                                        } elseif ($status == "lunas") {
                                            $badgeStatus = "success";
                                        } elseif ($status == "batal") {
                                            $badgeStatus = "danger";
                                        }
                                        ?>

                                        <tr>



                                            <!-- MOBIL -->
                                            <td class="align-middle">

                                                <div class="font-weight-bold text-gray-800">
                                                    <?= htmlspecialchars($row['nama_mobil'] ?? '-'); ?>
                                                </div>

                                                <div class="small text-gray-500">
                                                    Tahun <?= htmlspecialchars($row['tahun'] ?? '-'); ?>
                                                </div>

                                            </td>

                                            <!-- JENIS BAYAR -->
                                            <td class="text-center align-middle">

                                                <span class="badge badge-<?= $badgeJenis; ?> px-3 py-2">
                                                    <?= $textJenis; ?>
                                                </span>

                                            </td>

                                            <!-- METODE -->
                                            <td class="text-center align-middle">

                                                <?php if ($metode == "transfer") : ?>

                                                    <span class="badge badge-info px-3 py-2">
                                                        Transfer
                                                    </span>

                                                <?php else : ?>

                                                    <span class="badge badge-primary px-3 py-2">
                                                        Tunai
                                                    </span>

                                                <?php endif; ?>

                                            </td>

                                            <!-- JUMLAH -->
                                            <td class="text-center align-middle font-weight-bold text-success">
                                                <?= rupiah($row['jumlah']); ?>
                                            </td>

                                            <!-- TANGGAL -->
                                            <td class="text-center align-middle">

                                                <?php if (isset($row['tanggal_bayar']) && !empty($row['tanggal_bayar'])) : ?>

                                                    <?= date('d M Y', strtotime($row['tanggal_bayar'])); ?>
                                                    <br>
                                                    <span class="small text-gray-500">
                                                        <?= date('H:i', strtotime($row['tanggal_bayar'])); ?>
                                                    </span>

                                                <?php else : ?>

                                                    <?= date('d M Y', strtotime($row['tanggal_pesan'])); ?>
                                                    <br>
                                                    <span class="small text-gray-500">
                                                        <?= date('H:i', strtotime($row['tanggal_pesan'])); ?>
                                                    </span>

                                                <?php endif; ?>

                                            </td>

                                            <!-- BUKTI -->
                                            <td class="text-center align-middle">

                                                <?php if (
                                                    $metode == "transfer" &&
                                                    !empty($row['bukti_pembayaran']) &&
                                                    $row['bukti_pembayaran'] != "-"
                                                ) : ?>

                                                    <a href="../uploads/<?= htmlspecialchars($row['bukti_pembayaran']); ?>"
                                                       target="_blank"
                                                       class="btn btn-warning btn-sm">
                                                        <i class="fas fa-image"></i>
                                                    </a>

                                                <?php else : ?>

                                                    <span class="text-muted">-</span>

                                                <?php endif; ?>

                                            </td>

                                            <!-- STATUS -->
                                            <td class="text-center align-middle">

                                                <span class="badge badge-<?= $badgeStatus; ?> px-3 py-2">
                                                    <?= ucfirst($status); ?>
                                                </span>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                    </tbody>

                                </table>

                            </div>

                        <?php else : ?>

                            <div class="text-center py-5">

                                <i class="fas fa-credit-card fa-3x text-gray-300 mb-3"></i>

                                <h5 class="text-gray-800">
                                    Belum Ada Riwayat Pembayaran
                                </h5>

                                <p class="text-muted mb-4">
                                    Kamu belum melakukan pembayaran.
                                </p>

                                <a href="pesanan_saya.php" class="btn btn-primary">
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
    let table = document.getElementById("paymentTable");

    if (!table) return;

    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let text = tr[i].innerText.toLowerCase();
        tr[i].style.display = text.includes(input) ? "" : "none";
    }
}
</script>

</body>
</html>
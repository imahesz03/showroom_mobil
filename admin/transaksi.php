<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit;
}

function rupiah($angka)
{
    return "Rp " . number_format((float)$angka, 0, ',', '.');
}

function cekKolom($koneksi, $tabel, $kolom)
{
    $q = mysqli_query($koneksi, "SHOW COLUMNS FROM `$tabel` LIKE '$kolom'");
    return ($q && mysqli_num_rows($q) > 0);
}

$kolomJenis = cekKolom($koneksi, "pembayaran", "jenis_pembayaran")
    ? "pembayaran.jenis_pembayaran"
    : "'booking'";

$data = mysqli_query($koneksi, "
    SELECT 
        pemesanan.id_pemesanan,
        pemesanan.tanggal_pesan,
        pemesanan.total_harga,
        pemesanan.status AS status_pesanan,

        pembeli.nama AS nama_pembeli,

        mobil.nama_mobil,

        COALESCE(SUM(pembayaran.jumlah), 0) AS total_bayar,

        MAX(CASE 
            WHEN $kolomJenis = 'booking' THEN 1 
            ELSE 0 
        END) AS sudah_booking,

        MAX(CASE 
            WHEN $kolomJenis = 'dp' THEN 1 
            ELSE 0 
        END) AS sudah_dp,

        MAX(CASE 
            WHEN $kolomJenis = 'pelunasan' THEN 1 
            ELSE 0 
        END) AS sudah_pelunasan,

        MAX(CASE 
            WHEN pembayaran.bukti_pembayaran IS NOT NULL
            AND pembayaran.bukti_pembayaran != '-'
            THEN pembayaran.bukti_pembayaran
            ELSE NULL
        END) AS bukti_pembayaran

    FROM pemesanan

    JOIN pembeli 
    ON pemesanan.id_pembeli = pembeli.id_pembeli

    JOIN mobil 
    ON pemesanan.id_mobil = mobil.id_mobil

    LEFT JOIN pembayaran 
    ON pemesanan.id_pemesanan = pembayaran.id_pemesanan

    GROUP BY pemesanan.id_pemesanan

    ORDER BY pemesanan.id_pemesanan DESC
");

if (!$data) {
    die("Query transaksi error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Transaksi Admin - Galaxy Showroom</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>

<body id="page-top" class="role-admin page-table">

<div id="wrapper">

    <?php include "../includes/sidebar_admin.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php 
            $pageTitle = "Transaksi";
            include "../includes/topbar.php"; 
            ?>

            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">

                    <div>
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold">
                            Transaksi
                        </h1>

                        <p class="mb-0 text-gray-600">
                            Ringkasan transaksi pemesanan dan pembayaran mobil.
                        </p>
                    </div>

                </div>

                <div class="card shadow mb-4">

                    <div class="card-header py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">

                        <h6 class="m-0 font-weight-bold text-primary mb-3 mb-md-0">
                            <i class="fas fa-receipt mr-1"></i>
                            Data Transaksi
                        </h6>

                        <input type="text"
                               id="searchInput"
                               class="form-control form-control-sm"
                               style="max-width: 340px;"
                               placeholder="Cari pembeli, mobil, status..."
                               onkeyup="filterTable()">

                    </div>

                    <div class="card-body">

                        <?php if (mysqli_num_rows($data) > 0) : ?>

                            <div class="table-responsive">

                                <table class="table table-bordered table-hover" id="transaksiTable" width="100%" cellspacing="0">

                                    <thead class="thead-light text-center">
                                        <tr>
                                            <th width="60">No</th>
                                            <th>Pembeli</th>
                                            <th>Mobil</th>
                                            <th>Pembayaran</th>
                                            <th>Status</th>
                                            <th width="160">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                    <?php 
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($data)) : 

                                        $status = strtolower($row['status_pesanan'] ?? '-');

                                        $badgeStatus = "secondary";
                                        $textStatus = ucfirst($status);

                                        if ($status == "booking") {
                                            $badgeStatus = "warning";
                                            $textStatus = "Booking";
                                        } elseif ($status == "dp") {
                                            $badgeStatus = "info";
                                            $textStatus = "DP";
                                        } elseif ($status == "lunas") {
                                            $badgeStatus = "success";
                                            $textStatus = "Lunas";
                                        } elseif ($status == "batal") {
                                            $badgeStatus = "danger";
                                            $textStatus = "Batal";
                                        }

                                        $totalHarga = (float)$row['total_harga'];
                                        $totalBayar = (float)$row['total_bayar'];
                                    ?>

                                        <tr>

                                            <td class="text-center align-middle">
                                                <?= $no++; ?>
                                            </td>

                                            <td class="align-middle">

                                                <div class="font-weight-bold text-gray-800">
                                                    <?= htmlspecialchars($row['nama_pembeli'] ?? '-'); ?>
                                                </div>

                                                <div class="small text-gray-500">
                                                    <?= date('d M Y H:i', strtotime($row['tanggal_pesan'])); ?>
                                                </div>

                                            </td>

                                            <td class="align-middle">

                                                <div class="font-weight-bold text-gray-800">
                                                    <?= htmlspecialchars($row['nama_mobil'] ?? '-'); ?>
                                                </div>

                                                <div class="small text-primary font-weight-bold">
                                                    <?= rupiah($totalHarga); ?>
                                                </div>

                                            </td>

                                            <td class="align-middle text-center">

                                                <div class="mb-2">

                                                    <?php if ($row['sudah_booking']) : ?>
                                                        <span class="badge badge-warning px-3 py-2 mr-1 mb-1">
                                                            Booking
                                                        </span>
                                                    <?php endif; ?>

                                                    <?php if ($row['sudah_dp']) : ?>
                                                        <span class="badge badge-info px-3 py-2 mr-1 mb-1">
                                                            DP
                                                        </span>
                                                    <?php endif; ?>

                                                    <?php if ($row['sudah_pelunasan']) : ?>
                                                        <span class="badge badge-success px-3 py-2 mr-1 mb-1">
                                                            Lunas
                                                        </span>
                                                    <?php endif; ?>

                                                    <?php if (!$row['sudah_booking'] && !$row['sudah_dp'] && !$row['sudah_pelunasan']) : ?>
                                                        <span class="badge badge-secondary px-3 py-2">
                                                            Belum Bayar
                                                        </span>
                                                    <?php endif; ?>

                                                </div>

                                                <div class="small text-gray-500">
                                                    Total Bayar:
                                                    <strong class="text-success">
                                                        <?= rupiah($totalBayar); ?>
                                                    </strong>
                                                </div>

                                            </td>

                                            <td class="align-middle text-center">

                                                <span class="badge badge-<?= $badgeStatus; ?> px-3 py-2">
                                                    <?= $textStatus; ?>
                                                </span>

                                            </td>

                                            <td class="align-middle text-center">

                                                <div class="dropdown">

                                                    <button class="btn btn-primary btn-sm dropdown-toggle"
                                                            type="button"
                                                            id="dropdownAksi<?= $row['id_pemesanan']; ?>"
                                                            data-toggle="dropdown"
                                                            aria-haspopup="true"
                                                            aria-expanded="false">
                                                        <i class="fas fa-cog mr-1"></i>
                                                        Aksi
                                                    </button>

                                                    <div class="dropdown-menu dropdown-menu-right shadow"
                                                         aria-labelledby="dropdownAksi<?= $row['id_pemesanan']; ?>">

                                                        <a href="detail_transaksi.php?id=<?= $row['id_pemesanan']; ?>"
                                                           class="dropdown-item">
                                                            <i class="fas fa-eye fa-sm fa-fw mr-2 text-info"></i>
                                                            Detail
                                                        </a>

                                                        <?php if (!empty($row['bukti_pembayaran'])) : ?>

                                                            <a href="../uploads/<?= htmlspecialchars($row['bukti_pembayaran']); ?>"
                                                               target="_blank"
                                                               class="dropdown-item">
                                                                <i class="fas fa-image fa-sm fa-fw mr-2 text-warning"></i>
                                                                Lihat Bukti
                                                            </a>

                                                        <?php endif; ?>

                                                        <?php if ($status == "lunas") : ?>

                                                            <div class="dropdown-divider"></div>

                                                            <a href="kwitansi.php?id=<?= $row['id_pemesanan']; ?>"
                                                               target="_blank"
                                                               class="dropdown-item">
                                                                <i class="fas fa-print fa-sm fa-fw mr-2 text-success"></i>
                                                                Cetak Kwitansi
                                                            </a>

                                                        <?php endif; ?>

                                                        <?php if ($status == "batal") : ?>

                                                            <div class="dropdown-divider"></div>

                                                            <span class="dropdown-item text-danger">
                                                                <i class="fas fa-times-circle fa-sm fa-fw mr-2"></i>
                                                                Pesanan Batal
                                                            </span>

                                                        <?php endif; ?>

                                                    </div>

                                                </div>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                    </tbody>

                                </table>

                            </div>

                        <?php else : ?>

                            <div class="text-center py-5">
                                <i class="fas fa-receipt fa-3x text-gray-300 mb-3"></i>

                                <h5 class="text-gray-800">
                                    Belum Ada Transaksi
                                </h5>

                                <p class="text-muted mb-0">
                                    Data transaksi pembayaran belum tersedia.
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
function filterTable() {
    let input = document.getElementById("searchInput").value.toLowerCase();
    let table = document.getElementById("transaksiTable");

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
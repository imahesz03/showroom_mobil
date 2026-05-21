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
| DATA RIWAYAT PEMBELIAN
| Hanya tampil pesanan yang sudah lunas
|------------------------------------------------------
*/
$query = mysqli_query($koneksi, "
    SELECT
        p.id_pemesanan,
        p.tanggal_pesan,
        p.total_harga,
        p.status,

        m.nama_mobil,
        m.tahun,
        m.foto,

        COALESCE(SUM(pb.jumlah), 0) AS total_bayar

    FROM pemesanan p

    LEFT JOIN mobil m
    ON p.id_mobil = m.id_mobil

    LEFT JOIN pembayaran pb
    ON p.id_pemesanan = pb.id_pemesanan

    WHERE p.id_pembeli = '$id_pembeli'
    AND p.status = 'lunas'

    GROUP BY p.id_pemesanan

    ORDER BY p.id_pemesanan DESC
");

if (!$query) {
    die("Query riwayat pembelian error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Riwayat Pembelian - Galaxy Showroom</title>

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
            $pageTitle = "Riwayat Pembelian";
            include "../includes/topbar.php"; 
            ?>

            <div class="container-fluid">

                <!-- HEADER -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">

                    <div>
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold">
                            Riwayat Pembelian
                        </h1>

                        <p class="mb-0 text-gray-600">
                            Daftar mobil yang sudah berhasil kamu beli.
                        </p>
                    </div>

                    <a href="lihat_mobil.php" class="btn btn-primary btn-sm shadow-sm mt-3 mt-sm-0">
                        <i class="fas fa-car mr-1"></i>
                        Lihat Mobil
                    </a>

                </div>

                <!-- CARD -->
                <div class="card shadow mb-4">

                    <div class="card-header py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">

                        <h6 class="m-0 font-weight-bold text-primary mb-3 mb-md-0">
                            <i class="fas fa-history mr-1"></i>
                            Data Riwayat Pembelian
                        </h6>

                        <input type="text"
                               id="searchInput"
                               class="form-control form-control-sm"
                               style="max-width: 320px;"
                               placeholder="Cari mobil, tahun, status..."
                               onkeyup="filterTable()">

                    </div>

                    <div class="card-body">

                        <?php if (mysqli_num_rows($query) > 0) : ?>

                            <div class="table-responsive">

                                <table class="table table-bordered table-hover" id="riwayatTable" width="100%" cellspacing="0">

                                    <thead class="thead-light text-center">
                                        <tr>
                                            <th width="75">Foto</th>
                                            <th>Mobil</th>
                                            <th>Tanggal Pesan</th>
                                            <th>Total Harga</th>
                                            <th>Total Bayar</th>
                                            <th>Status</th>
                                            <th width="130">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                    <?php while ($row = mysqli_fetch_assoc($query)) : ?>

                                        <?php
                                        $foto = !empty($row['foto'])
                                            ? "../uploads/" . $row['foto']
                                            : "../assets/img/undraw_posting_photo.svg";
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

                                            <!-- TANGGAL -->
                                            <td class="text-center align-middle">

                                                <?= date('d M Y', strtotime($row['tanggal_pesan'])); ?>

                                                <br>

                                                <span class="small text-gray-500">
                                                    <?= date('H:i', strtotime($row['tanggal_pesan'])); ?>
                                                </span>

                                            </td>

                                            <!-- TOTAL HARGA -->
                                            <td class="text-center align-middle font-weight-bold text-primary">
                                                <?= rupiah($row['total_harga']); ?>
                                            </td>

                                            <!-- TOTAL BAYAR -->
                                            <td class="text-center align-middle font-weight-bold text-success">
                                                <?= rupiah($row['total_bayar']); ?>
                                            </td>

                                            <!-- STATUS -->
                                            <td class="text-center align-middle">
                                                <span class="badge badge-success px-3 py-2">
                                                    Lunas
                                                </span>
                                            </td>

                                            <!-- AKSI -->
                                            <td class="text-center align-middle">

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

                                                        <a href="detail_pesanan.php?id=<?= $row['id_pemesanan']; ?>"
                                                           class="dropdown-item">
                                                            <i class="fas fa-eye fa-sm fa-fw mr-2 text-info"></i>
                                                            Detail Pembelian
                                                        </a>

                                                        <a href="../admin/kwitansi.php?id=<?= $row['id_pemesanan']; ?>"
                                                           target="_blank"
                                                           class="dropdown-item">
                                                            <i class="fas fa-print fa-sm fa-fw mr-2 text-success"></i>
                                                            Cetak Kwitansi
                                                        </a>

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

                                <i class="fas fa-history fa-3x text-gray-300 mb-3"></i>

                                <h5 class="text-gray-800">
                                    Belum Ada Riwayat Pembelian
                                </h5>

                                <p class="text-muted mb-4">
                                    Riwayat pembelian akan muncul setelah pesanan kamu sudah lunas.
                                </p>

                                <a href="lihat_mobil.php" class="btn btn-primary">
                                    <i class="fas fa-car mr-1"></i>
                                    Lihat Mobil
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
    let table = document.getElementById("riwayatTable");

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
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

$qPembeli = mysqli_query($koneksi, "
    SELECT * FROM pembeli 
    WHERE id_user = '$id_user'
");

if (mysqli_num_rows($qPembeli) == 0) {
    echo "<script>
        alert('Data pembeli tidak ditemukan!');
        window.location='../auth/login.php';
    </script>";
    exit;
}

$pembeli = mysqli_fetch_assoc($qPembeli);
$id_pembeli = $pembeli['id_pembeli'];

$queryPesanan = mysqli_query($koneksi, "
    SELECT 
        pemesanan.*,
        mobil.nama_mobil,
        mobil.foto,
        mobil.tahun,
        pembayaran.id_pembayaran,
        pembayaran.metode_bayar,
        pembayaran.jumlah,
        pembayaran.status AS status_pembayaran,
        pembayaran.bukti_pembayaran
    FROM pemesanan
    LEFT JOIN mobil ON pemesanan.id_mobil = mobil.id_mobil
    LEFT JOIN pembayaran ON pemesanan.id_pemesanan = pembayaran.id_pemesanan
    WHERE pemesanan.id_pembeli = '$id_pembeli'
    ORDER BY pemesanan.id_pemesanan DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pesanan Saya - Galaxy Showroom</title>

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
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Pesanan Saya</h1>
                        <p class="mb-0 text-gray-600">
                            Daftar pemesanan mobil yang sudah kamu lakukan.
                        </p>
                    </div>

                    <a href="lihat_mobil.php" class="btn btn-primary btn-sm shadow-sm mt-3 mt-sm-0">
                        <i class="fas fa-car mr-1"></i>
                        Lihat Mobil
                    </a>
                </div>

                <div class="card shadow mb-4">

                    <div class="card-header py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary mb-3 mb-md-0">
                            Data Pesanan Mobil
                        </h6>

                        <input type="text"
                               id="searchInput"
                               class="form-control"
                               style="max-width:320px;"
                               placeholder="Cari mobil, status, pembayaran..."
                               onkeyup="filterTable()">
                    </div>

                    <div class="card-body">

                        <?php if (mysqli_num_rows($queryPesanan) > 0) : ?>

                            <div class="table-responsive">

                                <table class="table table-bordered table-hover" id="pesananTable">
                                    <thead class="thead-light">
                                    <tr>
                                        <th width="80">Foto</th>
                                        <th>Mobil</th>
                                        <th>Tanggal</th>
                                        <th>Total Harga</th>
                                        <th>Metode</th>
                                        <th>Status Pesanan</th>
                                        <th>Status Bayar</th>
                                        <th width="180">Aksi</th>
                                    </tr>
                                    </thead>

                                    <tbody>

                                    <?php while ($row = mysqli_fetch_assoc($queryPesanan)) : ?>

                                        <?php
                                        $foto = !empty($row['foto'])
                                            ? "../uploads/" . $row['foto']
                                            : "../assets/img/undraw_posting_photo.svg";

                                        $badgePesanan = "secondary";

                                        if ($row['status'] == "booking") {
                                            $badgePesanan = "warning";
                                        } elseif ($row['status'] == "dp") {
                                            $badgePesanan = "info";
                                        } elseif ($row['status'] == "lunas") {
                                            $badgePesanan = "success";
                                        } elseif ($row['status'] == "batal") {
                                            $badgePesanan = "danger";
                                        }

                                        $statusBayar = !empty($row['status_pembayaran'])
                                            ? $row['status_pembayaran']
                                            : "pending";

                                        $badgeBayar = "secondary";

                                        if ($statusBayar == "pending") {
                                            $badgeBayar = "warning";
                                        } elseif ($statusBayar == "verifikasi") {
                                            $badgeBayar = "info";
                                        } elseif ($statusBayar == "diterima") {
                                            $badgeBayar = "success";
                                        }

                                        $metodeBayar = !empty($row['metode_bayar'])
                                            ? $row['metode_bayar']
                                            : "tunai";
                                        ?>

                                        <tr>
                                            <td class="text-center">
                                                <img src="<?= htmlspecialchars($foto); ?>"
                                                     onerror="this.src='../assets/img/undraw_posting_photo.svg'"
                                                     style="width:65px; height:48px; object-fit:cover; border-radius:8px;">
                                            </td>

                                            <td>
                                                <div class="font-weight-bold text-gray-800">
                                                    <?= htmlspecialchars($row['nama_mobil'] ?? '-'); ?>
                                                </div>
                                                <div class="small text-gray-500">
                                                    Tahun <?= htmlspecialchars($row['tahun'] ?? '-'); ?>
                                                </div>
                                            </td>

                                            <td>
                                                <?= date('d M Y', strtotime($row['tanggal_pesan'])); ?>
                                            </td>

                                            <td class="font-weight-bold text-primary">
                                                <?= rupiah($row['total_harga']); ?>
                                            </td>

                                            <td>
                                                <?php if ($metodeBayar == "tunai") : ?>
                                                    <span class="badge badge-primary px-3 py-2">Tunai</span>
                                                <?php elseif ($metodeBayar == "transfer") : ?>
                                                    <span class="badge badge-info px-3 py-2">Transfer</span>
                                                <?php else : ?>
                                                    <span class="badge badge-secondary px-3 py-2">-</span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <span class="badge badge-<?= $badgePesanan; ?> px-3 py-2">
                                                    <?= ucfirst($row['status']); ?>
                                                </span>
                                            </td>

                                            <td>
                                                <span class="badge badge-<?= $badgeBayar; ?> px-3 py-2">
                                                    <?= ucfirst($statusBayar); ?>
                                                </span>
                                            </td>

                                            <td>
                                                <div class="d-flex flex-wrap" style="gap:6px;">

                                                    <a href="detail_pesanan.php?id=<?= $row['id_pemesanan']; ?>"
                                                       class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                        Detail
                                                    </a>

                                                    <?php if (
                                                        $row['metode_bayar'] == "transfer" &&
                                                        !empty($row['bukti_pembayaran'])
                                                    ) : ?>

                                                        <a href="../uploads/<?= htmlspecialchars($row['bukti_pembayaran']); ?>"
                                                           target="_blank"
                                                           class="btn btn-warning btn-sm">
                                                            <i class="fas fa-image"></i>
                                                        </a>

                                                    <?php endif; ?>

                                                    <?php if ($row['status'] == "lunas") : ?>

                                                        <a href="../admin/kwitansi.php?id=<?= $row['id_pemesanan']; ?>"
                                                           target="_blank"
                                                           class="btn btn-success btn-sm">
                                                            <i class="fas fa-print"></i>
                                                        </a>

                                                    <?php endif; ?>

                                                </div>
                                            </td>
                                        </tr>

                                    <?php endwhile; ?>

                                    </tbody>
                                </table>

                            </div>

                        <?php else : ?>

                            <div class="text-center py-5">
                                <i class="fas fa-shopping-cart fa-3x text-gray-300 mb-3"></i>

                                <h5 class="text-gray-800">Belum Ada Pesanan</h5>

                                <p class="text-muted mb-4">
                                    Kamu belum melakukan pemesanan mobil.
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
    let table = document.getElementById("pesananTable");
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let text = tr[i].innerText.toLowerCase();

        if (text.includes(input)) {
            tr[i].style.display = "";
        } else {
            tr[i].style.display = "none";
        }
    }
}
</script>

</body>
</html>
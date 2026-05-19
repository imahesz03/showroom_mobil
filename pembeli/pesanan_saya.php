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

/* AUTO BATAL BOOKING LEWAT 7 HARI */
$qExpired = mysqli_query($koneksi, "
    SELECT id_pemesanan, id_mobil
    FROM pemesanan
    WHERE id_pembeli = '$id_pembeli'
    AND status = 'booking'
    AND deadline_dp IS NOT NULL
    AND deadline_dp < CURDATE()
");

if ($qExpired && mysqli_num_rows($qExpired) > 0) {
    while ($exp = mysqli_fetch_assoc($qExpired)) {
        mysqli_query($koneksi, "
            UPDATE pemesanan 
            SET status = 'batal'
            WHERE id_pemesanan = '{$exp['id_pemesanan']}'
        ");

        mysqli_query($koneksi, "
            UPDATE mobil 
            SET stok = stok + 1, status = 'tersedia'
            WHERE id_mobil = '{$exp['id_mobil']}'
        ");
    }
}

/*
|------------------------------------------------------
| DATA PESANAN
| 1 pesanan = 1 baris
| pembayaran diringkas pakai subquery
|------------------------------------------------------
*/
$queryPesanan = mysqli_query($koneksi, "
    SELECT 
        p.*,
        m.nama_mobil,
        m.foto,
        m.tahun,

        COALESCE(SUM(pb.jumlah), 0) AS total_bayar,

        MAX(CASE 
            WHEN pb.bukti_pembayaran IS NOT NULL 
            AND pb.bukti_pembayaran != '-' 
            THEN pb.bukti_pembayaran 
            ELSE NULL 
        END) AS bukti_pembayaran,

        MAX(pb.metode_bayar) AS metode_bayar,

        MAX(CASE 
            WHEN pb.jenis_pembayaran = 'booking' THEN 1 
            ELSE 0 
        END) AS sudah_booking,

        MAX(CASE 
            WHEN pb.jenis_pembayaran = 'dp' THEN 1 
            ELSE 0 
        END) AS sudah_dp,

        MAX(CASE 
            WHEN pb.jenis_pembayaran = 'pelunasan' THEN 1 
            ELSE 0 
        END) AS sudah_lunas_bayar

    FROM pemesanan p
    LEFT JOIN mobil m ON p.id_mobil = m.id_mobil
    LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan
    WHERE p.id_pembeli = '$id_pembeli'
    GROUP BY p.id_pemesanan
    ORDER BY p.id_pemesanan DESC
");

if (!$queryPesanan) {
    die("Query pesanan error: " . mysqli_error($koneksi));
}
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

            <?php 
            $pageTitle = "Pesanan Saya";
            include "../includes/topbar.php"; 
            ?>

            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Pesanan Saya</h1>
                        <p class="mb-0 text-gray-600">
                            Pantau status pemesanan dan pembayaran mobil kamu.
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
                            <i class="fas fa-clipboard-list mr-1"></i>
                            Data Pesanan Mobil
                        </h6>

                        <input type="text"
                               id="searchInput"
                               class="form-control form-control-sm"
                               style="max-width: 340px;"
                               placeholder="Cari mobil, status, pembayaran..."
                               onkeyup="filterTable()">

                    </div>

                    <div class="card-body">

                        <?php if (mysqli_num_rows($queryPesanan) > 0) : ?>

                            <div class="table-responsive">

                                <table class="table table-bordered table-hover" id="pesananTable" width="100%" cellspacing="0">

                                    <thead class="thead-light text-center">
                                        <tr>
                                            <th width="80">Foto</th>
                                            <th>Mobil</th>
                                            <th>Tanggal</th>
                                            <th>Total Harga</th>
                                            <th>Total Bayar</th>
                                            <th>Sisa Bayar</th>
                                            <th>Status</th>
                                            <th width="140">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                    <?php while ($row = mysqli_fetch_assoc($queryPesanan)) : ?>

                                        <?php
                                        $foto = !empty($row['foto'])
                                            ? "../uploads/" . $row['foto']
                                            : "../assets/img/undraw_posting_photo.svg";

                                        $status = $row['status'] ?? 'booking';

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

                                        $totalHarga = (float)$row['total_harga'];
                                        $totalBayar = (float)$row['total_bayar'];
                                        $sisaBayar = max($totalHarga - $totalBayar, 0);

                                        $dp30 = $totalHarga * 30 / 100;

                                        $deadline = !empty($row['deadline_dp'])
                                            ? date('d M Y', strtotime($row['deadline_dp']))
                                            : '-';
                                        ?>

                                        <tr>

                                            <td class="text-center align-middle">

                                                <div class="position-relative d-inline-block">

                                                    <img src="<?= htmlspecialchars($foto); ?>"
                                                         class="img-thumbnail"
                                                         width="75"
                                                         height="55"
                                                         style="object-fit:cover; cursor:pointer;"
                                                         data-toggle="modal"
                                                         data-target="#modalFoto<?= $row['id_pemesanan']; ?>"
                                                         onerror="this.src='../assets/img/undraw_posting_photo.svg'">

                                                    <div class="position-absolute w-100 h-100 d-flex align-items-center justify-content-center"
                                                         style="top:0; left:0; background:rgba(0,0,0,.45); opacity:0; transition:.2s; border-radius:.35rem; cursor:pointer;"
                                                         onmouseover="this.style.opacity='1'"
                                                         onmouseout="this.style.opacity='0'"
                                                         data-toggle="modal"
                                                         data-target="#modalFoto<?= $row['id_pemesanan']; ?>">
                                                        <i class="fas fa-search-plus text-white"></i>
                                                    </div>

                                                </div>

                                                <div class="modal fade" id="modalFoto<?= $row['id_pemesanan']; ?>" tabindex="-1">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">
                                                                    <?= htmlspecialchars($row['nama_mobil'] ?? '-'); ?>
                                                                </h5>

                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    <span>&times;</span>
                                                                </button>
                                                            </div>

                                                            <div class="modal-body text-center">
                                                                <img src="<?= htmlspecialchars($foto); ?>"
                                                                     class="img-fluid rounded shadow"
                                                                     onerror="this.src='../assets/img/undraw_posting_photo.svg'">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </td>

                                            <td class="align-middle">
                                                <div class="font-weight-bold text-gray-800">
                                                    <?= htmlspecialchars($row['nama_mobil'] ?? '-'); ?>
                                                </div>

                                                <div class="small text-gray-500">
                                                    Tahun <?= htmlspecialchars($row['tahun'] ?? '-'); ?>
                                                </div>

                                                <?php if($status == "booking") : ?>
                                                    <div class="small text-warning mt-1">
                                                        Deadline DP: <?= $deadline; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>

                                            <td class="align-middle text-center">
                                                <?= date('d M Y', strtotime($row['tanggal_pesan'])); ?>
                                                <br>
                                                <span class="small text-gray-500">
                                                    <?= date('H:i', strtotime($row['tanggal_pesan'])); ?>
                                                </span>
                                            </td>

                                            <td class="align-middle text-center font-weight-bold text-primary">
                                                <?= rupiah($totalHarga); ?>
                                                <br>
                                                <span class="small text-gray-500">
                                                    DP 30%: <?= rupiah($dp30); ?>
                                                </span>
                                            </td>

                                            <td class="align-middle text-center font-weight-bold text-success">
                                                <?= rupiah($totalBayar); ?>
                                            </td>

                                            <td class="align-middle text-center font-weight-bold <?= ($sisaBayar <= 0) ? 'text-success' : 'text-danger'; ?>">
                                                <?= rupiah($sisaBayar); ?>
                                            </td>

                                            <td class="align-middle text-center">
                                                <span class="badge badge-<?= $badgeStatus; ?> px-3 py-2">
                                                    <?= ucfirst($status); ?>
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

                                                        <a href="detail_pesanan.php?id=<?= $row['id_pemesanan']; ?>"
                                                           class="dropdown-item">
                                                            <i class="fas fa-eye fa-sm fa-fw mr-2 text-info"></i>
                                                            Detail Pesanan
                                                        </a>

                                                        <?php if (!empty($row['bukti_pembayaran']) && $row['bukti_pembayaran'] != "-") : ?>

                                                            <a href="../uploads/<?= htmlspecialchars($row['bukti_pembayaran']); ?>"
                                                               target="_blank"
                                                               class="dropdown-item">
                                                                <i class="fas fa-image fa-sm fa-fw mr-2 text-warning"></i>
                                                                Lihat Bukti Bayar
                                                            </a>

                                                        <?php endif; ?>

                                                        <?php if (!empty($row['foto_ktp'])) : ?>

                                                            <a href="../uploads/<?= htmlspecialchars($row['foto_ktp']); ?>"
                                                               target="_blank"
                                                               class="dropdown-item">
                                                                <i class="fas fa-id-card fa-sm fa-fw mr-2 text-primary"></i>
                                                                Lihat KTP
                                                            </a>

                                                        <?php endif; ?>

                                                        <?php if ($status == "booking") : ?>

                                                            <div class="dropdown-divider"></div>

                                                            <a href="pembayaran.php?id=<?= $row['id_pemesanan']; ?>&jenis=dp"
                                                               class="dropdown-item">
                                                                <i class="fas fa-money-bill-wave fa-sm fa-fw mr-2 text-success"></i>
                                                                Bayar DP 30%
                                                            </a>

                                                            <a href="pembayaran.php?id=<?= $row['id_pemesanan']; ?>&jenis=pelunasan"
                                                               class="dropdown-item">
                                                                <i class="fas fa-credit-card fa-sm fa-fw mr-2 text-success"></i>
                                                                Langsung Lunasi
                                                            </a>

                                                            <a href="proses_batal.php?id=<?= $row['id_pemesanan']; ?>"
                                                               class="dropdown-item"
                                                               onclick="return confirm('Yakin ingin membatalkan pesanan ini?');">
                                                                <i class="fas fa-times-circle fa-sm fa-fw mr-2 text-danger"></i>
                                                                Batalkan Pesanan
                                                            </a>

                                                        <?php endif; ?>

                                                        <?php if ($status == "dp") : ?>

                                                            <div class="dropdown-divider"></div>

                                                            <a href="pembayaran.php?id=<?= $row['id_pemesanan']; ?>&jenis=pelunasan"
                                                               class="dropdown-item">
                                                                <i class="fas fa-credit-card fa-sm fa-fw mr-2 text-success"></i>
                                                                Lunasi Pembayaran
                                                            </a>

                                                        <?php endif; ?>

                                                        <?php if ($status == "lunas") : ?>

                                                            <div class="dropdown-divider"></div>

                                                            <a href="../admin/kwitansi.php?id=<?= $row['id_pemesanan']; ?>"
                                                               target="_blank"
                                                               class="dropdown-item">
                                                                <i class="fas fa-print fa-sm fa-fw mr-2 text-success"></i>
                                                                Cetak Kwitansi
                                                            </a>

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

    if(!table){
        return;
    }

    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let text = tr[i].innerText.toLowerCase();
        tr[i].style.display = text.includes(input) ? "" : "none";
    }
}
</script>

</body>
</html>
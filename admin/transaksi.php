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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold tracking-tight">Transaksi</h1>
                        <p class="mb-0 text-muted small">
                            Ringkasan transaksi pemesanan dan riwayat pembayaran masuk dari pembeli.
                        </p>
                    </div>
                </div>

                <div class="card shadow border-0 mb-4 rounded-lg">

                    <div class="card-header bg-white border-bottom py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                        <div class="d-flex align-items-center mb-3 mb-md-0">
                            <div class="bg-light p-2 rounded mr-3">
                                <i class="fas fa-receipt text-primary"></i>
                            </div>
                            <div>
                                <h6 class="m-0 font-weight-bold text-gray-800">Data Transaksi</h6>
                                <p class="m-0 text-muted small">Kelola data pemesanan, bukti transfer, dan kwitansi</p>
                            </div>
                        </div>

                        <div class="input-group shadow-sm" style="max-width: 340px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0 text-muted">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                            <input type="text"
                                   id="searchInput"
                                   class="form-control bg-light border-left-0 text-sm"
                                   placeholder="Cari pembeli, mobil, status..."
                                   onkeyup="filterTable()">
                        </div>
                    </div>

                    <div class="card-body p-4">

                        <?php if (mysqli_num_rows($data) > 0) : ?>

                            <div class="table-responsive">

                                <table class="table table-bordered table-hover mb-0" id="transaksiTable" width="100%" cellspacing="0">

                                    <thead class="bg-light text-dark text-uppercase small font-weight-bold">
                                        <tr>
                                            <th class="text-center py-3" style="width: 5%;">No</th>
                                            <th class="py-3" style="width: 25%;">Pembeli</th>
                                            <th class="py-3" style="width: 25%;">Mobil</th>
                                            <th class="text-center py-3" style="width: 20%;">Tahap Pembayaran</th>
                                            <th class="text-center py-3" style="width: 13%;">Status</th>
                                            <th class="text-center py-3" style="width: 12%;">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody class="text-gray-700">

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

                                        <tr class="align-middle">
                                            <td class="text-center align-middle font-weight-bold text-muted">
                                                <?= $no++; ?>
                                            </td>

                                            <td class="align-middle">
                                                <div class="font-weight-bold text-gray-800 mb-0">
                                                    <?= htmlspecialchars($row['nama_pembeli'] ?? '-'); ?>
                                                </div>
                                                <small class="text-muted d-block">
                                                    <i class="far fa-clock mr-1"></i><?= date('d M Y H:i', strtotime($row['tanggal_pesan'])); ?>
                                                </small>
                                            </td>

                                            <td class="align-middle">
                                                <div class="font-weight-bold text-gray-800 mb-0">
                                                    <?= htmlspecialchars($row['nama_mobil'] ?? '-'); ?>
                                                </div>
                                                <div class="small text-primary font-weight-bold">
                                                    <?= rupiah($totalHarga); ?>
                                                </div>
                                            </td>

                                            <td class="align-middle text-center">
                                                <div class="mb-1">
                                                    <?php if ($row['sudah_booking']) : ?>
                                                        <span class="badge badge-pill badge-warning px-2 py-1 font-weight-bold small text-uppercase" style="font-size: 70%;">Booking</span>
                                                    <?php endif; ?>

                                                    <?php if ($row['sudah_dp']) : ?>
                                                        <span class="badge badge-pill badge-info px-2 py-1 font-weight-bold small text-uppercase" style="font-size: 70%;">DP</span>
                                                    <?php endif; ?>

                                                    <?php if ($row['sudah_pelunasan']) : ?>
                                                        <span class="badge badge-pill badge-success px-2 py-1 font-weight-bold small text-uppercase" style="font-size: 70%;">Lunas</span>
                                                    <?php endif; ?>

                                                    <?php if (!$row['sudah_booking'] && !$row['sudah_dp'] && !$row['sudah_pelunasan']) : ?>
                                                        <span class="badge badge-pill badge-secondary px-2 py-1 font-weight-bold small text-uppercase" style="font-size: 70%;">Belum Bayar</span>
                                                    <?php endif; ?>
                                                </div>

                                                <small class="text-muted d-block">
                                                    Total Bayar: <span class="text-success font-weight-bold"><?= rupiah($totalBayar); ?></span>
                                                </small>
                                            </td>

                                            <td class="text-center align-middle">
                                                <span class="badge badge-pill badge-<?= $badgeStatus; ?> px-3 py-2 font-weight-bold text-uppercase" style="font-size: 75%;">
                                                    <?= $textStatus; ?>
                                                </span>
                                            </td>

                                            <td class="text-center align-middle">
                                                <div class="dropdown">
                                                    <button class="btn btn-white border text-gray-800 btn-sm dropdown-toggle shadow-sm px-3 font-weight-bold"
                                                            type="button"
                                                            id="dropdownAksi<?= $row['id_pemesanan']; ?>"
                                                            data-toggle="dropdown"
                                                            aria-haspopup="true"
                                                            aria-expanded="false">
                                                        Aksi
                                                    </button>

                                                    <div class="dropdown-menu dropdown-menu-right shadow border-0 rounded-lg"
                                                         aria-labelledby="dropdownAksi<?= $row['id_pemesanan']; ?>">

                                                        <a href="detail_transaksi.php?id=<?= $row['id_pemesanan']; ?>"
                                                           class="dropdown-item py-2 text-sm">
                                                            <i class="fas fa-eye fa-sm fa-fw mr-2 text-info"></i> Detail
                                                        </a>

                                                        <?php if (!empty($row['bukti_pembayaran'])) : ?>
                                                            <a href="../uploads/<?= htmlspecialchars($row['bukti_pembayaran']); ?>"
                                                               target="_blank"
                                                               class="dropdown-item py-2 text-sm">
                                                                <i class="fas fa-image fa-sm fa-fw mr-2 text-warning"></i> Lihat Bukti
                                                            </a>
                                                        <?php endif; ?>

                                                        <?php if ($status == "lunas") : ?>
                                                            <div class="dropdown-divider my-1"></div>
                                                            <a href="kwitansi.php?id=<?= $row['id_pemesanan']; ?>"
                                                               target="_blank"
                                                               class="dropdown-item py-2 text-sm text-success font-weight-bold">
                                                                <i class="fas fa-print fa-sm fa-fw mr-2"></i> Cetak Kwitansi
                                                            </a>
                                                        <?php endif; ?>

                                                        <?php if ($status == "batal") : ?>
                                                            <div class="dropdown-divider my-1"></div>
                                                            <span class="dropdown-item py-2 text-sm text-danger disabled font-weight-bold">
                                                                <i class="fas fa-times-circle fa-sm fa-fw mr-2"></i> Pesanan Batal
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

                            <div id="no-result" class="text-center text-muted py-5 mx-3" style="display:none;">
                                <div class="p-3 bg-light d-inline-block rounded-circle mb-3">
                                    <i class="fas fa-search-minus fa-2x text-gray-400"></i>
                                </div>
                                <h6 class="font-weight-bold text-gray-800 mb-1">Data tidak cocok</h6>
                                <p class="small text-muted mb-0">Periksa kembali ejaan atau kata kunci pencarian Anda.</p>
                            </div>

                        <?php else : ?>

                            <div class="text-center py-5 my-3">
                                <div class="p-4 bg-light d-inline-block rounded-circle mb-4">
                                    <i class="fas fa-receipt fa-3x text-gray-300"></i>
                                </div>
                                <h5 class="text-gray-800 font-weight-bold mb-1">Belum Ada Transaksi</h5>
                                <p class="text-muted small mb-0">
                                    Data transaksi pembayaran atau pemesanan dari pembeli belum tersedia.
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
// Fungsi live search tabel
function filterTable() {
    const input = document.getElementById("searchInput");
    const filter = input.value.toLowerCase();
    const table = document.getElementById("transaksiTable");
    const noResult = document.getElementById("no-result");

    if (!table) return;

    const tr = table.getElementsByTagName("tr");
    let visibleCount = 0;

    for (let i = 1; i < tr.length; i++) {
        const text = tr[i].innerText.toLowerCase();
        
        if (text.includes(filter)) {
            tr[i].style.display = "";
            visibleCount++;
        } else {
            tr[i].style.display = "none";
        }
    }

    if (noResult) {
        noResult.style.display = visibleCount === 0 ? "block" : "none";
    }
}
</script>

</body>
</html>
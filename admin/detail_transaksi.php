<?php
session_start();
include "../config/koneksi.php";

// -------------------------------------------------------------------------
// 1. PROTEKSI & OTENTIKASI AKSES
// -------------------------------------------------------------------------
if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit;
}

// -------------------------------------------------------------------------
// 2. HELPER FUNCTIONS
// -------------------------------------------------------------------------
function rupiah($angka)
{
    return "Rp " . number_format((float)$angka, 0, ',', '.');
}

// -------------------------------------------------------------------------
// 3. VALIDASI & SANITASI PARAMETER URL
// -------------------------------------------------------------------------
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: transaksi.php");
    exit;
}

$id_pemesanan = mysqli_real_escape_string($koneksi, $_GET['id']);

// -------------------------------------------------------------------------
// 4. DATA FETCHING (QUERY UTAMA)
// -------------------------------------------------------------------------
$query_utama = mysqli_query($koneksi, "
    SELECT 
        pemesanan.id_pemesanan,
        pemesanan.tanggal_pesan,
        pemesanan.total_harga,
        pemesanan.status,
        pemesanan.foto_ktp,
        pembeli.nama AS nama_pembeli,
        pembeli.no_hp AS telp_pembeli,
        pembeli.alamat AS alamat_pembeli,
        mobil.nama_mobil,
        mobil.tahun,
        mobil.foto AS foto_mobil,
        mobil.harga AS harga_awal_mobil,
        COALESCE(SUM(pembayaran.jumlah), 0) AS total_bayar
    FROM pemesanan
    JOIN pembeli ON pemesanan.id_pembeli = pembeli.id_pembeli
    JOIN mobil ON pemesanan.id_mobil = mobil.id_mobil
    LEFT JOIN pembayaran ON pemesanan.id_pemesanan = pembayaran.id_pemesanan
    WHERE pemesanan.id_pemesanan = '$id_pemesanan'
    GROUP BY pemesanan.id_pemesanan
");

$trx = mysqli_fetch_assoc($query_utama);

if (!$trx) {
    header("Location: transaksi.php");
    exit;
}

// -------------------------------------------------------------------------
// 5. MAPPING STATUS & KONFIGURASI BADGE UI
// -------------------------------------------------------------------------
$status = strtolower($trx['status'] ?? 'booking');
$configStatus = [
    'booking' => ['badge' => 'warning', 'text' => 'Booking'],
    'dp'      => ['badge' => 'info',    'text' => 'DP (Down Payment)'],
    'lunas'   => ['badge' => 'success', 'text' => 'Lunas'],
    'batal'   => ['badge' => 'danger',  'text' => 'Batal']
];

$badgeStatus = $configStatus[$status]['badge'] ?? 'secondary';
$textStatus  = $configStatus[$status]['text'] ?? ucfirst($status);

// Kalkulasi nilai sisa kekurangan finansial
$totalHarga = (float)$trx['total_harga'];
$totalBayar = (float)$trx['total_bayar'];
$sisaKekurangan = ($totalHarga - $totalBayar > 0) ? ($totalHarga - $totalBayar) : 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Detail Transaksi #<?= $trx['id_pemesanan']; ?> - Galaxy Showroom</title>
    
    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>

<body id="page-top">

<div id="wrapper">

    <?php include "../includes/sidebar_admin.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <?php 
            $pageTitle = "Detail Transaksi";
            include "../includes/topbar.php"; 
            ?>

            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                    <div>
                        <a href="transaksi.php" class="btn btn-sm btn-light border shadow-sm mb-2 text-gray-700">
                            <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali
                        </a>
                        <h1 class="h4 mb-0 text-gray-800 font-weight-bold">
                            Invoice #INV-<?= $trx['id_pemesanan']; ?>
                        </h1>
                    </div>
                    <?php if ($status == "lunas") : ?>
                        <a href="kwitansi.php?id=<?= $trx['id_pemesanan']; ?>" target="_blank" class="btn btn-sm btn-success shadow-sm px-3">
                            <i class="fas fa-print fa-sm mr-1"></i> Cetak Kwitansi
                        </a>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-3 text-center mb-3 mb-md-0 border-right-md">
                                        <?php if (!empty($trx['foto_mobil'])) : ?>
                                            <img src="../uploads/<?= htmlspecialchars($trx['foto_mobil']); ?>" alt="Foto Unit" class="img-fluid rounded shadow-sm border" style="max-height: 130px; object-fit: cover; width: 100%; max-width: 220px;">
                                        <?php else : ?>
                                            <div class="p-4 bg-light text-muted rounded text-xs"><i class="fas fa-car fa-2x mb-1"></i><br>FOTO TIDAK TERSEDIA</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-9 pl-md-4 text-center text-md-left">
                                        <span class="badge badge-light text-primary text-uppercase tracking-wider px-2 py-1 mb-2 font-weight-bold" style="font-size: 11px;">Spesifikasi Produk</span>
                                        <h3 class="font-weight-bold text-gray-900 mb-1"><?= htmlspecialchars($trx['nama_mobil']); ?></h3>
                                        <p class="text-gray-600 mb-0 small">Tahun Perakitan Unit: <span class="font-weight-bold text-dark"><?= htmlspecialchars($trx['tahun']); ?></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header py-3 bg-white border-bottom-0">
                                <h6 class="m-0 font-weight-bold text-primary text-sm">
                                    <i class="fas fa-user-circle mr-2"></i>Informasi Data Pelanggan
                                </h6>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <div class="row bg-light rounded p-3 mx-0 mb-3">
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <small class="text-xs text-muted d-block text-uppercase font-weight-bold mb-1">Nama Lengkap</small>
                                        <span class="font-weight-bold text-gray-800 text-sm"><?= htmlspecialchars($trx['nama_pembeli']); ?></span>
                                    </div>
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <small class="text-xs text-muted d-block text-uppercase font-weight-bold mb-1">Kontak Telepon</small>
                                        <span class="font-weight-bold text-gray-800 text-sm text-monospace"><?= htmlspecialchars($trx['telp_pembeli'] ?? '-'); ?></span>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-xs text-muted d-block text-uppercase font-weight-bold mb-1">Dokumen KTP</small>
                                        <?php if (!empty($trx['foto_ktp'])) : ?>
                                            <a href="../uploads/<?= htmlspecialchars($trx['foto_ktp']); ?>" target="_blank" class="btn btn-xs btn-primary px-3 shadow-sm py-1" style="font-size: 11px;">
                                                <i class="fas fa-external-link-alt mr-1"></i> Buka Lampiran KTP
                                            </a>
                                        <?php else : ?>
                                            <span class="text-muted text-xs font-italic">Belum diunggah</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <small class="text-xs text-muted d-block text-uppercase font-weight-bold mb-1">Alamat Sesuai Identitas</small>
                                    <span class="text-gray-700 text-sm"><?= htmlspecialchars($trx['alamat_pembeli'] ?? '-'); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            
                            <div class="col-md-4 mb-4">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-body d-flex flex-column justify-content-center py-4">
                                        <div class="text-xs font-weight-bold text-uppercase text-muted tracking-wider text-center mb-2">Status Validasi Sistem</div>
                                        <div class="text-center mb-3">
                                            <span class="badge badge-<?= $badgeStatus; ?> px-4 py-2 font-weight-bold shadow-sm" style="font-size: 13px; border-radius: 50px;">
                                                <i class="fas fa-info-circle mr-1"></i> <?= $textStatus; ?>
                                            </span>
                                        </div>
                                        <div class="text-center border-top pt-3 mx-3">
                                            <small class="text-xs text-muted d-block mb-1">Waktu Pemesanan</small>
                                            <span class="text-gray-800 font-weight-bold small">
                                                <i class="far fa-clock mr-1 text-secondary"></i> <?= date('d F Y H:i', strtotime($trx['tanggal_pesan'])); ?> WIB
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8 mb-4">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-header py-3 bg-white border-bottom-0">
                                        <h6 class="m-0 font-weight-bold text-dark text-sm">
                                            <i class="fas fa-file-invoice-dollar mr-2"></i>Kalkulasi Neraca Pembayaran
                                        </h6>
                                    </div>
                                    <div class="card-body p-0 pb-2">
                                        <div class="table-responsive">
                                            <table class="table mb-0 text-sm table-borderless">
                                                <tbody>
                                                    <tr class="border-bottom">
                                                        <td class="pl-4 py-3 text-gray-600">Kesepakatan Nilai Harga Jual</td>
                                                        <td class="text-right pr-4 font-weight-bold text-gray-900 text-monospace py-3">
                                                            <?= rupiah($totalHarga); ?>
                                                        </td>
                                                    </tr>
                                                    <tr class="bg-light-success border-bottom" style="background-color: #f8fff9;">
                                                        <td class="pl-4 font-weight-bold text-success py-3">Total Dana Masuk (Terverifikasi)</td>
                                                        <td class="text-right pr-4 font-weight-bold text-success text-monospace py-3">
                                                            <?= rupiah($totalBayar); ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="pl-4 font-weight-bold text-danger py-3">Sisa Margin Kekurangan Pembayaran</td>
                                                        <td class="text-right pr-4 font-weight-bold text-danger text-monospace py-3">
                                                            <?= rupiah($sisaKekurangan); ?>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div> </div>
                </div> </div>
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
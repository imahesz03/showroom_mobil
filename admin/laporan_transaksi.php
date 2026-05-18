<?php
session_start();
include "../config/koneksi.php";

date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit;
}

$tanggal_awal  = $_GET['tanggal_awal'] ?? date('Y-m-01');
$tanggal_akhir = $_GET['tanggal_akhir'] ?? date('Y-m-d');
$status        = $_GET['status'] ?? 'semua';
$keyword       = $_GET['keyword'] ?? '';

$tanggal_awal_safe  = mysqli_real_escape_string($koneksi, $tanggal_awal);
$tanggal_akhir_safe = mysqli_real_escape_string($koneksi, $tanggal_akhir);
$status_safe        = mysqli_real_escape_string($koneksi, $status);
$keyword_safe       = mysqli_real_escape_string($koneksi, $keyword);

$where = "
    WHERE DATE(p.tanggal_pesan) BETWEEN '$tanggal_awal_safe' AND '$tanggal_akhir_safe'
";

if ($status_safe != "semua") {
    $where .= " AND p.status = '$status_safe'";
}

if ($keyword_safe != "") {
    $where .= "
        AND (
            pb.nama LIKE '%$keyword_safe%' OR
            m.nama_mobil LIKE '%$keyword_safe%' OR
            py.metode_bayar LIKE '%$keyword_safe%' OR
            p.status LIKE '%$keyword_safe%'
        )
    ";
}

$queryTransaksi = mysqli_query($koneksi, "
    SELECT 
        p.id_pemesanan,
        p.tanggal_pesan,
        p.total_harga,
        p.status AS status_pemesanan,
        pb.nama AS nama_pembeli,
        m.nama_mobil,
        py.metode_bayar,
        py.status AS status_pembayaran
    FROM pemesanan p
    LEFT JOIN pembeli pb ON p.id_pembeli = pb.id_pembeli
    LEFT JOIN mobil m ON p.id_mobil = m.id_mobil
    LEFT JOIN pembayaran py ON p.id_pemesanan = py.id_pemesanan
    $where
    ORDER BY p.tanggal_pesan DESC
");

$totalTransaksi = 0;
$totalPendapatan = 0;

$dataTransaksi = [];

if ($queryTransaksi) {
    while ($row = mysqli_fetch_assoc($queryTransaksi)) {
        $dataTransaksi[] = $row;
        $totalTransaksi++;

        if (($row['status_pemesanan'] ?? '') == 'lunas') {
            $totalPendapatan += $row['total_harga'];
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi</title>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .page-title{
            font-weight:700;
            color:#2e3648;
        }

        .page-subtitle{
            color:#6c757d;
            font-size:14px;
        }

        .filter-card{
            border-radius:10px;
        }

        .table th{
            font-size:13px;
            color:#4e5d78;
            background:#f8f9fc;
            white-space:nowrap;
        }

        .table td{
            font-size:14px;
            vertical-align:middle;
            color:#333;
        }

        .status-box{
            display:inline-block;
            min-width:90px;
            padding:5px 10px;
            border-radius:6px;
            background:#f1f3f7;
            color:#333;
            font-weight:600;
            text-align:center;
            font-size:13px;
        }

        .status-success{
            background:#eafaf1;
            color:#1e7e34;
        }

        .status-warning{
            background:#fff8e1;
            color:#9a6a00;
        }

        .status-info{
            background:#eaf6fb;
            color:#117a8b;
        }

        .status-danger{
            background:#fdecec;
            color:#b02a37;
        }

        .status-secondary{
            background:#f1f3f7;
            color:#555;
        }

        @media print{
            .sidebar,
            .topbar,
            .btn,
            .filter-laporan{
                display:none !important;
            }

            body{
                background:#fff !important;
            }

            .card{
                box-shadow:none !important;
                border:none !important;
            }

            .container-fluid{
                padding:0 !important;
            }
        }
    </style>
</head>

<body id="page-top">

<div id="wrapper">

    <?php include '../includes/sidebar_admin.php'; ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php include '../includes/topbar.php'; ?>

            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h1 class="h3 mb-1 page-title">Laporan Transaksi</h1>
                        <p class="mb-0 page-subtitle">
                            Daftar transaksi pemesanan dan pembayaran showroom mobil.
                        </p>
                    </div>

                    <button onclick="window.print()" class="btn btn-primary btn-sm shadow-sm">
                        <i class="fas fa-print mr-1"></i>
                        Cetak Laporan
                    </button>
                </div>

                <div class="card shadow-sm mb-4 filter-laporan filter-card">
                    <div class="card-body">

                        <form method="GET">

                            <div class="row align-items-end">

                                <div class="col-md-3 mb-3 mb-md-0">
                                    <label class="font-weight-bold text-gray-700">
                                        Tanggal Awal
                                    </label>

                                    <input type="date"
                                           name="tanggal_awal"
                                           class="form-control"
                                           value="<?= htmlspecialchars($tanggal_awal); ?>">
                                </div>

                                <div class="col-md-3 mb-3 mb-md-0">
                                    <label class="font-weight-bold text-gray-700">
                                        Tanggal Akhir
                                    </label>

                                    <input type="date"
                                           name="tanggal_akhir"
                                           class="form-control"
                                           value="<?= htmlspecialchars($tanggal_akhir); ?>">
                                </div>

                                <div class="col-md-3 mb-3 mb-md-0">
                                    <label class="font-weight-bold text-gray-700">
                                        Status Transaksi
                                    </label>

                                    <select name="status" class="form-control">

                                        <option value="semua" <?= ($status == 'semua') ? 'selected' : ''; ?>>
                                            Semua
                                        </option>

                                        <option value="booking" <?= ($status == 'booking') ? 'selected' : ''; ?>>
                                            Booking
                                        </option>

                                        <option value="dp" <?= ($status == 'dp') ? 'selected' : ''; ?>>
                                            DP
                                        </option>

                                        <option value="lunas" <?= ($status == 'lunas') ? 'selected' : ''; ?>>
                                            Lunas
                                        </option>

                                        <option value="batal" <?= ($status == 'batal') ? 'selected' : ''; ?>>
                                            Batal
                                        </option>

                                    </select>
                                </div>

                                <div class="col-md-3 mb-3 mb-md-0">
                                    <label class="font-weight-bold text-gray-700">
                                        Cari Transaksi
                                    </label>

                                    <input type="text"
                                           name="keyword"
                                           class="form-control"
                                           placeholder="Nama pembeli / mobil..."
                                           value="<?= htmlspecialchars($keyword); ?>">
                                </div>

                            </div>

                            <div class="mt-3">

                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-search mr-1"></i>
                                    Cari
                                </button>

                                <a href="laporan_transaksi.php" class="btn btn-secondary">
                                    Reset
                                </a>

                            </div>

                        </form>

                    </div>
                </div>

                <div class="card shadow-sm mb-4">

                    <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">

                        <h6 class="m-0 font-weight-bold text-primary">
                            Detail Data Transaksi
                        </h6>

                        <span class="text-muted small">
                            <?= date('d/m/Y H:i'); ?> WIB
                        </span>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover mb-0">

                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Pembeli</th>
                                        <th>Mobil</th>
                                        <th>Total Harga</th>
                                        <th>Metode Bayar</th>
                                        <th>Status Transaksi</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php if (count($dataTransaksi) > 0): ?>

                                        <?php $no = 1; ?>

                                        <?php foreach ($dataTransaksi as $row): ?>

                                            <tr>

                                                <td><?= $no++; ?></td>

                                                <td>
                                                    <?= !empty($row['tanggal_pesan']) ? date('d/m/Y H:i', strtotime($row['tanggal_pesan'])) : '-'; ?>
                                                </td>

                                                <td class="font-weight-bold">
                                                    <?= htmlspecialchars($row['nama_pembeli'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($row['nama_mobil'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    Rp <?= number_format($row['total_harga'] ?? 0, 0, ',', '.'); ?>
                                                </td>

                                                <td>
                                                    <?= !empty($row['metode_bayar']) ? htmlspecialchars($row['metode_bayar']) : 'cash'; ?>
                                                </td>

                                                <td>
                                                    <?php if (($row['status_pemesanan'] ?? '') == 'lunas'): ?>
                                                        <span class="status-box status-success">Lunas</span>
                                                    <?php elseif (($row['status_pemesanan'] ?? '') == 'booking'): ?>
                                                        <span class="status-box status-warning">Booking</span>
                                                    <?php elseif (($row['status_pemesanan'] ?? '') == 'dp'): ?>
                                                        <span class="status-box status-info">DP</span>
                                                    <?php elseif (($row['status_pemesanan'] ?? '') == 'batal'): ?>
                                                        <span class="status-box status-danger">Batal</span>
                                                    <?php else: ?>
                                                        <span class="status-box status-secondary">-</span>
                                                    <?php endif; ?>
                                                </td>

                                            </tr>

                                        <?php endforeach; ?>

                                    <?php else: ?>

                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                Data transaksi tidak ditemukan.
                                            </td>
                                        </tr>

                                    <?php endif; ?>

                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right">
                                            Total Transaksi
                                        </th>

                                        <th colspan="4">
                                            <?= $totalTransaksi; ?> Data
                                        </th>
                                    </tr>

                                    <tr>
                                        <th colspan="4" class="text-right">
                                            Total Pendapatan Lunas
                                        </th>

                                        <th colspan="4">
                                            Rp <?= number_format($totalPendapatan, 0, ',', '.'); ?>
                                        </th>
                                    </tr>
                                </tfoot>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sb-admin-2.min.js"></script>

</body>
</html>
<?php
session_start();
include "../config/koneksi.php";

date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit;
}

$keyword = $_GET['keyword'] ?? '';
$status  = $_GET['status'] ?? 'semua';

$keyword_safe = mysqli_real_escape_string($koneksi, $keyword);
$status_safe  = mysqli_real_escape_string($koneksi, $status);

$where = "WHERE 1=1";

if ($keyword_safe != "") {
    $where .= "
        AND (
            nama_mobil LIKE '%$keyword_safe%' OR
            tahun LIKE '%$keyword_safe%' OR
            harga LIKE '%$keyword_safe%' OR
            status LIKE '%$keyword_safe%'
        )
    ";
}

if ($status_safe != "semua") {
    $where .= " AND status = '$status_safe'";
}

$queryMobil = mysqli_query($koneksi, "
    SELECT 
        id_mobil,
        nama_mobil,
        tahun,
        harga,
        stok,
        status
    FROM mobil
    $where
    ORDER BY id_mobil DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Mobil</title>

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
            min-width:85px;
            padding:5px 10px;
            border-radius:6px;
            background:#f1f3f7;
            color:#333;
            font-weight:600;
            text-align:center;
            font-size:13px;
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
                        <h1 class="h3 mb-1 page-title">
                            Laporan Data Mobil
                        </h1>

                        <p class="mb-0 page-subtitle">
                            Daftar data mobil showroom berdasarkan status kendaraan.
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

                                <div class="col-md-5 mb-3 mb-md-0">
                                    <label class="font-weight-bold text-gray-700">
                                        Cari Mobil
                                    </label>

                                    <input type="text"
                                           name="keyword"
                                           class="form-control"
                                           placeholder="Cari nama mobil, tahun, harga..."
                                           value="<?= htmlspecialchars($keyword); ?>">
                                </div>

                                <div class="col-md-3 mb-3 mb-md-0">
                                    <label class="font-weight-bold text-gray-700">
                                        Status Mobil
                                    </label>

                                    <select name="status" class="form-control">

                                        <option value="semua" <?= ($status == 'semua') ? 'selected' : ''; ?>>
                                            Semua
                                        </option>

                                        <option value="tersedia" <?= ($status == 'tersedia') ? 'selected' : ''; ?>>
                                            Tersedia
                                        </option>

                                        <option value="dipesan" <?= ($status == 'dipesan') ? 'selected' : ''; ?>>
                                            Dipesan
                                        </option>

                                        <option value="terjual" <?= ($status == 'terjual') ? 'selected' : ''; ?>>
                                            Terjual
                                        </option>

                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-search mr-1"></i>
                                        Cari
                                    </button>

                                    <a href="laporan_mobil.php" class="btn btn-secondary">
                                        Reset
                                    </a>
                                </div>

                            </div>

                        </form>

                    </div>
                </div>

                <div class="card shadow-sm mb-4">

                    <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">

                        <h6 class="m-0 font-weight-bold text-primary">
                            Detail Data Mobil
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
                                        <th>Nama Mobil</th>
                                        <th>Tahun</th>
                                        <th>Harga</th>
                                        <th>Stok</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php if ($queryMobil && mysqli_num_rows($queryMobil) > 0): ?>

                                        <?php $no = 1; ?>

                                        <?php while ($row = mysqli_fetch_assoc($queryMobil)): ?>

                                            <tr>

                                                <td><?= $no++; ?></td>

                                                <td class="font-weight-bold">
                                                    <?= htmlspecialchars($row['nama_mobil'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($row['tahun'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    Rp <?= number_format($row['harga'] ?? 0, 0, ',', '.'); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($row['stok'] ?? '0'); ?>
                                                </td>

                                                <td>
                                                    <span class="status-box">
                                                        <?= ucfirst(htmlspecialchars($row['status'] ?? '-')); ?>
                                                    </span>
                                                </td>

                                            </tr>

                                        <?php endwhile; ?>

                                    <?php else: ?>

                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                Data mobil tidak ditemukan.
                                            </td>
                                        </tr>

                                    <?php endif; ?>

                                </tbody>

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
<?php
session_start();
include "../config/koneksi.php";

date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit;
}

$keyword = $_GET['keyword'] ?? '';
$keyword_safe = mysqli_real_escape_string($koneksi, $keyword);

$whereKeyword = "";
if ($keyword_safe != "") {
    $whereKeyword = "
        WHERE 
        pb.nama LIKE '%$keyword_safe%' OR
        pb.no_hp LIKE '%$keyword_safe%' OR
        pb.alamat LIKE '%$keyword_safe%'
    ";
}

$queryPembeli = mysqli_query($koneksi, "
    SELECT 
        pb.id_pembeli,
        pb.nama,
        pb.alamat,
        pb.no_hp,
        u.username,
        COUNT(p.id_pemesanan) AS total_pemesanan,
        SUM(CASE WHEN p.status = 'booking' THEN 1 ELSE 0 END) AS total_booking,
        SUM(CASE WHEN p.status = 'dp' THEN 1 ELSE 0 END) AS total_dp,
        SUM(CASE WHEN p.status = 'lunas' THEN 1 ELSE 0 END) AS total_lunas,
        SUM(CASE WHEN p.status = 'batal' THEN 1 ELSE 0 END) AS total_batal
    FROM pembeli pb
    LEFT JOIN users u ON pb.id_user = u.id_user
    LEFT JOIN pemesanan p ON pb.id_pembeli = p.id_pembeli
    $whereKeyword
    GROUP BY pb.id_pembeli
    ORDER BY pb.id_pembeli DESC
");

$totalPembeli = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM pembeli"));

$totalPembeliTransaksi = mysqli_num_rows(mysqli_query($koneksi, "
    SELECT DISTINCT id_pembeli 
    FROM pemesanan 
    WHERE id_pembeli IS NOT NULL
"));

$totalPembeliLunas = mysqli_num_rows(mysqli_query($koneksi, "
    SELECT DISTINCT id_pembeli 
    FROM pemesanan 
    WHERE status = 'lunas' AND id_pembeli IS NOT NULL
"));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Pembeli</title>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
    
    <style>
        .page-title {
            font-weight: 700;
            color: #2e3648;
        }

        .page-subtitle {
            color: #6c757d;
            font-size: 14px;
        }

        .summary-card {
            border: 1px solid #e3e6f0;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.04);
            height: 100%;
        }

        .summary-card .card-body {
            padding: 18px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .summary-label {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .summary-value {
            font-size: 26px;
            font-weight: 700;
            color: #2e3648;
            line-height: 1;
        }

        .summary-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: #f1f3f7;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4e73df;
            font-size: 18px;
        }

        .filter-card {
            border-radius: 10px;
        }

        .table th {
            font-size: 13px;
            color: #4e5d78;
            background: #f8f9fc;
            white-space: nowrap;
        }

        .table td {
            font-size: 14px;
            vertical-align: middle;
            color: #333;
        }

        .number-box {
            display: inline-block;
            min-width: 32px;
            padding: 4px 8px;
            border-radius: 6px;
            background: #f1f3f7;
            color: #333;
            font-weight: 600;
            text-align: center;
        }

        @media print {
            .sidebar,
            .topbar,
            .btn,
            .filter-laporan {
                display: none !important;
            }

            body {
                background: #fff !important;
            }

            .card {
                box-shadow: none !important;
                border: none !important;
            }

            .container-fluid {
                padding: 0 !important;
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
                        <h1 class="h3 mb-1 page-title">Laporan Data Pembeli</h1>
                        <p class="mb-0 page-subtitle">
                            Ringkasan data pembeli dan jumlah transaksi yang pernah dilakukan.
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

                                <div class="col-md-8 mb-3 mb-md-0">
                                    <label class="font-weight-bold text-gray-700">Cari Pembeli</label>
                                    <input type="text" name="keyword" class="form-control"
                                           placeholder="Cari berdasarkan nama, nomor HP, atau alamat..."
                                           value="<?= htmlspecialchars($keyword); ?>">
                                </div>

                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-search mr-1"></i>
                                        Cari
                                    </button>

                                    <a href="laporan_pembeli.php" class="btn btn-secondary">
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
                            Detail Data Pembeli
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
                                        <th>Nama Pembeli</th>
                                        <th>Username</th>
                                        <th>No HP</th>
                                        <th>Alamat</th>
                                        <th>Total Pesanan</th>
                                        <th>Booking</th>
                                        <th>DP</th>
                                        <th>Lunas</th>
                                        <th>Batal</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php if ($queryPembeli && mysqli_num_rows($queryPembeli) > 0): ?>
                                        <?php $no = 1; ?>
                                        <?php while ($row = mysqli_fetch_assoc($queryPembeli)): ?>
                                            <tr>
                                                <td><?= $no++; ?></td>

                                                <td class="font-weight-bold">
                                                    <?= htmlspecialchars($row['nama'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($row['username'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($row['no_hp'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($row['alamat'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    <span class="number-box">
                                                        <?= $row['total_pemesanan'] ?? 0; ?>
                                                    </span>
                                                </td>

                                                <td>
                                                    <span class="number-box">
                                                        <?= $row['total_booking'] ?? 0; ?>
                                                    </span>
                                                </td>

                                                <td>
                                                    <span class="number-box">
                                                        <?= $row['total_dp'] ?? 0; ?>
                                                    </span>
                                                </td>

                                                <td>
                                                    <span class="number-box">
                                                        <?= $row['total_lunas'] ?? 0; ?>
                                                    </span>
                                                </td>

                                                <td>
                                                    <span class="number-box">
                                                        <?= $row['total_batal'] ?? 0; ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-4">
                                                Data pembeli tidak ditemukan.
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
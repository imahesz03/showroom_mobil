<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../auth/login.php");
    exit;
}

function rupiah($angka){
    return "Rp " . number_format((float)$angka, 0, ',', '.');
}

function badgeStatus($status){
    if($status == "booking") return "warning";
    if($status == "dp") return "info";
    if($status == "lunas") return "success";
    if($status == "batal") return "danger";
    return "secondary";
}

$query = mysqli_query($koneksi, "
    SELECT 
        p.id_pemesanan,
        p.tanggal_pesan,
        p.total_harga,
        p.status AS status_pemesanan,
        p.deadline_dp,
        p.foto_ktp,

        b.nama AS nama_pembeli,
        b.no_hp,

        m.nama_mobil,
        m.tahun,

        SUM(CASE WHEN py.jenis_pembayaran='booking' THEN py.jumlah ELSE 0 END) AS booking,
        SUM(CASE WHEN py.jenis_pembayaran='dp' THEN py.jumlah ELSE 0 END) AS dp,
        SUM(CASE WHEN py.jenis_pembayaran='pelunasan' THEN py.jumlah ELSE 0 END) AS pelunasan,

        MAX(py.metode_bayar) AS metode_bayar,
        MAX(py.bukti_pembayaran) AS bukti_pembayaran

    FROM pemesanan p
    JOIN pembeli b ON p.id_pembeli = b.id_pembeli
    JOIN mobil m ON p.id_mobil = m.id_mobil
    LEFT JOIN pembayaran py ON p.id_pemesanan = py.id_pemesanan
    GROUP BY p.id_pemesanan
    ORDER BY p.id_pemesanan DESC
");

if(!$query){
    die("Query Error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Administrasi Kendaraan</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">

    <style>
        .table th{
            font-size:12px;
            white-space:nowrap;
            text-align:center;
            vertical-align:middle;
        }

        .table td{
            font-size:13px;
            vertical-align:middle;
        }

        .table-responsive{
            overflow-x:auto;
        }

        #tableAdministrasi{
            min-width:1000px;
        }

        .badge{
            font-size:11px;
            padding:6px 9px;
        }

        .main-text{
            font-weight:700;
            color:#2f3542;
        }

        .muted-text{
            font-size:12px;
            color:#858796;
        }

        .pay-box{
            line-height:1.6;
        }

        .btn-icon{
            width:34px;
            height:34px;
            border-radius:10px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
        }
    </style>
</head>

<body id="page-top">

<div id="wrapper">

    <?php include "../includes/sidebar_admin.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php 
            $pageTitle = "Administrasi Kendaraan";
            include "../includes/topbar.php"; 
            ?>

            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold">
                            Administrasi Kendaraan
                        </h1>
                        <p class="mb-0 text-gray-600">
                            Ringkasan proses pembayaran dan dokumen kendaraan.
                        </p>
                    </div>
                </div>

                <div class="card shadow mb-4">

                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Data Administrasi
                        </h6>

                        <input type="text"
                               id="searchInput"
                               class="form-control form-control-sm"
                               style="max-width:300px;"
                               placeholder="Cari pembeli / mobil..."
                               onkeyup="filterTable()">
                    </div>

                    <div class="card-body">

                        <?php if(mysqli_num_rows($query) > 0){ ?>

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover" id="tableAdministrasi" width="100%" cellspacing="0">

                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Pembeli</th>
                                        <th>Mobil</th>
                                        <th>Pembayaran</th>
                                        <th>Dokumen</th>
                                        <th>Proses</th>
                                        <th>Bukti</th>
                                    </tr>
                                </thead>

                                <tbody>

                                <?php
                                $no = 1;

                                while($row = mysqli_fetch_assoc($query)){

                                    $status = $row['status_pemesanan'];

                                    $booking = (float)$row['booking'];
                                    $dp = (float)$row['dp'];
                                    $pelunasan = (float)$row['pelunasan'];
                                    $totalBayar = $booking + $dp + $pelunasan;

                                    if($status == "booking"){
                                        $proses = "Menunggu DP";
                                        $prosesBadge = "warning";
                                    } elseif($status == "dp"){
                                        $proses = "Proses STNK";
                                        $prosesBadge = "info";
                                    } elseif($status == "lunas"){
                                        $proses = "Siap Serah Terima";
                                        $prosesBadge = "success";
                                    } elseif($status == "batal"){
                                        $proses = "Dibatalkan";
                                        $prosesBadge = "danger";
                                    } else {
                                        $proses = "-";
                                        $prosesBadge = "secondary";
                                    }

                                    $ktpAda = !empty($row['foto_ktp']);
                                ?>

                                    <tr>
                                        <td class="text-center"><?= $no++; ?></td>

                                        <td>
                                            <div class="main-text">
                                                <?= htmlspecialchars($row['nama_pembeli']); ?>
                                            </div>
                                            <div class="muted-text">
                                                <?= htmlspecialchars($row['no_hp'] ?? '-'); ?>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="main-text">
                                                <?= htmlspecialchars($row['nama_mobil']); ?>
                                            </div>
                                            <div class="muted-text">
                                                Tahun <?= htmlspecialchars($row['tahun']); ?>
                                            </div>
                                            <div class="muted-text">
                                                <?= date('d-m-Y H:i', strtotime($row['tanggal_pesan'])); ?>
                                            </div>
                                        </td>

                                        <td class="pay-box">
                                            <span class="badge badge-<?= badgeStatus($status); ?> mb-2">
                                                <?= ucfirst($status); ?>
                                            </span>

                                            <div class="muted-text">
                                                Booking: <?= rupiah($booking); ?>
                                            </div>

                                            <div class="muted-text">
                                                DP: <?= rupiah($dp); ?>
                                            </div>

                                            <div class="muted-text">
                                                Pelunasan: <?= rupiah($pelunasan); ?>
                                            </div>

                                            <strong>
                                                Total: <?= rupiah($totalBayar); ?>
                                            </strong>

                                            <?php if($status == "booking" && !empty($row['deadline_dp'])){ ?>
                                                <div class="text-danger muted-text mt-1">
                                                    Batas DP: <?= date('d-m-Y', strtotime($row['deadline_dp'])); ?>
                                                </div>
                                            <?php } ?>
                                        </td>

                                        <td class="text-center">
                                            <?php if($ktpAda){ ?>
                                                <span class="badge badge-success mb-2">KTP Ada</span>
                                                <br>
                                                <a href="../uploads/<?= htmlspecialchars($row['foto_ktp']); ?>"
                                                   target="_blank"
                                                   class="btn btn-sm btn-primary btn-icon">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            <?php } else { ?>
                                                <span class="badge badge-secondary">KTP Belum</span>
                                            <?php } ?>
                                        </td>

                                        <td class="text-center">
                                            <span class="badge badge-<?= $prosesBadge; ?>">
                                                <?= $proses; ?>
                                            </span>

                                            <?php if($status == "dp"){ ?>
                                                <div class="muted-text mt-2">
                                                    STNK ± 2 minggu
                                                </div>
                                            <?php } elseif($status == "lunas"){ ?>
                                                <div class="muted-text mt-2">
                                                    BPKB ± 2 bulan
                                                </div>
                                            <?php } ?>
                                        </td>

                                        <td class="text-center">
                                            <?php if($row['metode_bayar'] == "transfer" && !empty($row['bukti_pembayaran']) && $row['bukti_pembayaran'] != "-"){ ?>
                                                <a href="../uploads/<?= htmlspecialchars($row['bukti_pembayaran']); ?>"
                                                   target="_blank"
                                                   class="btn btn-sm btn-primary btn-icon">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            <?php } else { ?>
                                                -
                                            <?php } ?>
                                        </td>
                                    </tr>

                                <?php } ?>

                                </tbody>

                            </table>

                        </div>

                        <?php } else { ?>

                            <div class="text-center py-5">
                                <i class="fas fa-folder-open fa-3x text-gray-400 mb-3"></i>
                                <h5 class="text-gray-800">Belum Ada Data Administrasi</h5>
                                <p class="text-muted mb-0">Data akan muncul setelah pembeli melakukan booking.</p>
                            </div>

                        <?php } ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../assets/js/sb-admin-2.min.js"></script>

<script>
function filterTable(){
    let input = document.getElementById("searchInput");
    let filter = input.value.toLowerCase();
    let table = document.getElementById("tableAdministrasi");
    let rows = table.getElementsByTagName("tr");

    for(let i = 1; i < rows.length; i++){
        let text = rows[i].innerText.toLowerCase();

        if(text.includes(filter)){
            rows[i].style.display = "";
        } else {
            rows[i].style.display = "none";
        }
    }
}
</script>

</body>
</html>
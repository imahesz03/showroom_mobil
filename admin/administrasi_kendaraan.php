<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../auth/login.php");
    exit;
}

/*
|------------------------------------------------------
| AMBIL DATA ADMINISTRASI DARI DATABASE YANG SUDAH ADA
|------------------------------------------------------
*/
$query = mysqli_query($koneksi, "
    SELECT 
        p.id_pemesanan,
        p.tanggal_pesan,
        p.total_harga,
        p.status AS status_pemesanan,

        b.nama AS nama_pembeli,
        b.alamat,
        b.no_hp,

        m.nama_mobil,
        m.tahun,

        py.metode_bayar,
        py.bukti_pembayaran

    FROM pemesanan p
    JOIN pembeli b ON p.id_pembeli = b.id_pembeli
    JOIN mobil m ON p.id_mobil = m.id_mobil
    LEFT JOIN pembayaran py ON p.id_pemesanan = py.id_pemesanan
    ORDER BY p.id_pemesanan DESC
");

if(!$query){
    die("Query error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Administrasi Kendaraan</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .table td{
            vertical-align: middle;
            font-size: 14px;
        }

        .table th{
            font-size: 14px;
            white-space: nowrap;
        }

        .badge{
            font-size: 12px;
            padding: 6px 9px;
        }

        .info-box{
            border-left: 4px solid #4e73df;
            background: #f8f9fc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .small-text{
            font-size: 12px;
            color: #858796;
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

                <h1 class="h3 mb-4 text-gray-800">
                    Administrasi Kendaraan
                </h1>

                <div class="info-box">
                    <strong>Monitoring Administrasi Kendaraan</strong><br>
                    <span class="small-text">
                        Halaman ini digunakan admin untuk melihat proses administrasi kendaraan berdasarkan transaksi pembeli,
                        seperti status pesanan, pembayaran, STNK, BPKB, dan bukti transfer.
                    </span>
                </div>

                <div class="card shadow mb-4">

                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Data Administrasi Kendaraan
                        </h6>

                        <input type="text"
                               id="searchInput"
                               class="form-control form-control-sm"
                               style="max-width: 300px;"
                               placeholder="Cari pembeli / mobil..."
                               onkeyup="filterTable()">
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover text-center" id="tableAdministrasi" width="100%" cellspacing="0">

                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Pembeli</th>
                                        <th>Mobil</th>
                                        <th>Tanggal Pesan</th>
                                        <th>Total</th>
                                        <th>Metode Bayar</th>
                                        <th>Bukti Transfer</th>
                                        <th>Status Pesanan</th>
                                        <th>STNK</th>
                                        <th>BPKB</th>
                                        <th>Status Administrasi</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php
                                    $no = 1;

                                    while($row = mysqli_fetch_assoc($query)){

                                        $status = $row['status_pemesanan'];

                                        /*
                                        |----------------------------------
                                        | STATUS PESANAN
                                        |----------------------------------
                                        */
                                        if($status == "booking"){
                                            $badgePesanan = "warning";
                                            $textPesanan  = "Booking";
                                        } elseif($status == "dp"){
                                            $badgePesanan = "info";
                                            $textPesanan  = "DP";
                                        } elseif($status == "lunas"){
                                            $badgePesanan = "success";
                                            $textPesanan  = "Lunas";
                                        } elseif($status == "batal"){
                                            $badgePesanan = "danger";
                                            $textPesanan  = "Batal";
                                        } else {
                                            $badgePesanan = "secondary";
                                            $textPesanan  = "-";
                                        }

                                        /*
                                        |----------------------------------
                                        | METODE BAYAR
                                        |----------------------------------
                                        */
                                        if($row['metode_bayar'] == "transfer"){
                                            $metode = "Transfer";
                                        } else {
                                            $metode = "Cash";
                                        }

                                        /*
                                        |----------------------------------
                                        | STATUS STNK & BPKB SIMPEL
                                        |----------------------------------
                                        */
                                        if($status == "booking"){
                                            $stnkBadge = "secondary";
                                            $stnkText  = "Belum Diproses";

                                            $bpkbBadge = "secondary";
                                            $bpkbText  = "Belum Diproses";

                                            $adminBadge = "warning";
                                            $adminText  = "Menunggu DP & KTP";
                                        } elseif($status == "dp"){
                                            $stnkBadge = "warning";
                                            $stnkText  = "Diproses";

                                            $bpkbBadge = "secondary";
                                            $bpkbText  = "Belum Diproses";

                                            $adminBadge = "info";
                                            $adminText  = "Pengurusan STNK";
                                        } elseif($status == "lunas"){
                                            $stnkBadge = "success";
                                            $stnkText  = "Selesai";

                                            $bpkbBadge = "warning";
                                            $bpkbText  = "Diproses";

                                            $adminBadge = "success";
                                            $adminText  = "Mobil Siap Diserahkan";
                                        } elseif($status == "batal"){
                                            $stnkBadge = "danger";
                                            $stnkText  = "Batal";

                                            $bpkbBadge = "danger";
                                            $bpkbText  = "Batal";

                                            $adminBadge = "danger";
                                            $adminText  = "Pesanan Batal";
                                        } else {
                                            $stnkBadge = "secondary";
                                            $stnkText  = "-";

                                            $bpkbBadge = "secondary";
                                            $bpkbText  = "-";

                                            $adminBadge = "secondary";
                                            $adminText  = "-";
                                        }
                                    ?>

                                    <tr>
                                        <td><?= $no++; ?></td>

                                        <td>
                                            <strong><?= htmlspecialchars($row['nama_pembeli']); ?></strong><br>
                                            <small class="small-text">
                                                <?= htmlspecialchars($row['no_hp'] ?? '-'); ?>
                                            </small>
                                        </td>

                                        <td>
                                            <strong><?= htmlspecialchars($row['nama_mobil']); ?></strong><br>
                                            <small class="small-text">
                                                Tahun <?= htmlspecialchars($row['tahun']); ?>
                                            </small>
                                        </td>

                                        <td>
                                            <?= date('d-m-Y H:i', strtotime($row['tanggal_pesan'])); ?>
                                        </td>

                                        <td>
                                            Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?>
                                        </td>

                                        <td>
                                            <?= $metode; ?>
                                        </td>

                                        <td>
                                            <?php if($row['metode_bayar'] == "transfer"){ ?>

                                                <?php if(!empty($row['bukti_pembayaran'])){ ?>
                                                    <a href="../uploads/<?= htmlspecialchars($row['bukti_pembayaran']); ?>"
                                                       target="_blank"
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> Lihat
                                                    </a>
                                                <?php } else { ?>
                                                    <span class="text-danger">Belum Upload</span>
                                                <?php } ?>

                                            <?php } else { ?>
                                                -
                                            <?php } ?>
                                        </td>

                                        <td>
                                            <span class="badge badge-<?= $badgePesanan; ?>">
                                                <?= $textPesanan; ?>
                                            </span>
                                        </td>

                                        <td>
                                            <span class="badge badge-<?= $stnkBadge; ?>">
                                                <?= $stnkText; ?>
                                            </span>
                                        </td>

                                        <td>
                                            <span class="badge badge-<?= $bpkbBadge; ?>">
                                                <?= $bpkbText; ?>
                                            </span>
                                        </td>

                                        <td>
                                            <span class="badge badge-<?= $adminBadge; ?>">
                                                <?= $adminText; ?>
                                            </span>
                                        </td>
                                    </tr>

                                    <?php } ?>

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
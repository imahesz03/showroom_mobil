<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../auth/login.php");
    exit;
}

$query = mysqli_query($koneksi,
"SELECT 
    p.id_pemesanan,
    p.tanggal_pesan,
    p.total_harga,
    p.status AS status_pemesanan,

    m.nama_mobil,
    b.nama AS nama_pembeli,

    py.metode_bayar,
    py.bukti_pembayaran

FROM pemesanan p
JOIN mobil m ON p.id_mobil = m.id_mobil
JOIN pembeli b ON p.id_pembeli = b.id_pembeli
LEFT JOIN pembayaran py ON p.id_pemesanan = py.id_pemesanan
ORDER BY p.id_pemesanan DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi Admin</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

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
                    <h1 class="h3 mb-0 text-gray-800">Kelola Transaksi</h1>
                </div>

                <div class="card shadow mb-4">

                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Data Transaksi
                        </h6>

                        <input type="text"
                               id="searchInput"
                               class="form-control form-control-sm"
                               style="max-width: 300px;"
                               placeholder="Cari nama pembeli..."
                               onkeyup="filterTable()">
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-center" id="tableTransaksi" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Pembeli</th>
                                        <th>Mobil</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                        <th>Metode</th>
                                        <th>Bukti</th>
                                        <th>Status Pesanan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    $no = 1;
                                    while($row = mysqli_fetch_assoc($query)){

                                        $status = $row['status_pemesanan'];

                                        if($status == "lunas"){
                                            $badge = "success";
                                        } elseif($status == "booking"){
                                            $badge = "warning";
                                        } elseif($status == "dp"){
                                            $badge = "info";
                                        } elseif($status == "batal"){
                                            $badge = "danger";
                                        } else {
                                            $badge = "secondary";
                                        }

                                        if($row['metode_bayar'] == "transfer"){
                                            $metode = "Transfer";
                                        } else {
                                            $metode = "Cash";
                                        }
                                    ?>

                                    <tr>
                                        <td><?= $no++; ?></td>

                                        <td><?= htmlspecialchars($row['nama_pembeli']); ?></td>

                                        <td><?= htmlspecialchars($row['nama_mobil']); ?></td>

                                        <td>
                                            <?= date('d-m-Y H:i', strtotime($row['tanggal_pesan'])); ?>
                                        </td>

                                        <td>
                                            Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?>
                                        </td>

                                        <td><?= $metode; ?></td>

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
                                            <span class="badge badge-<?= $badge; ?>">
                                                <?= ucfirst($status); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?php if($status == "booking"){ ?>

                                                <span class="badge badge-warning mb-2">
                                                    Booking
                                                </span>

                                            <?php } elseif($status == "dp"){ ?>

                                                <span class="badge badge-info mb-2">
                                                    DP
                                                </span>

                                            <?php } elseif($status == "lunas"){ ?>

                                                <a href="kwitansi.php?id=<?= $row['id_pemesanan']; ?>"
                                                   target="_blank"
                                                   class="btn btn-sm btn-success">
                                                    <i class="fas fa-print"></i> Cetak
                                                </a>

                                            <?php } elseif($status == "batal"){ ?>

                                                <span class="badge badge-danger">
                                                    Batal
                                                </span>

                                            <?php } else { ?>

                                                <span class="badge badge-secondary">
                                                    <?= ucfirst($status); ?>
                                                </span>

                                            <?php } ?>
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
function filterTable() {
    let input = document.getElementById("searchInput");
    let filter = input.value.toLowerCase();
    let table = document.getElementById("tableTransaksi");
    let rows = table.getElementsByTagName("tr");

    for(let i = 1; i < rows.length; i++){
        let rowText = rows[i].innerText.toLowerCase();

        if(rowText.includes(filter)){
            rows[i].style.display = "";
        } else {
            rows[i].style.display = "none";
        }
    }
}
</script>

</body>
</html>
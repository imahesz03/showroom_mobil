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
            min-width:1100px;
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
            width:32px;
            height:32px;
            border-radius:6px;
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
                            Ringkasan verifikasi pembayaran, kelengkapan berkas KTP, dan estimasi dokumen kendaraan.
                        </p>
                    </div>
                </div>

                <div class="card shadow mb-4">

                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-folder-open mr-2"></i>Data Administrasi Aktif
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
                                        <th width="40">No</th>
                                        <th width="180">Data Pembeli</th>
                                        <th width="180">Unit Kendaraan</th>
                                        <th>Rincian Finansial</th>
                                        <th width="130">Berkas Utama</th>
                                        <th width="160">Status & Dokumen</th>
                                        <th width="120">Aksi</th>
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
                                    $totalHarga = (float)$row['total_harga'];
                                    $sisaTagihan = $totalHarga - $totalBayar;

                                    if($status == "booking"){
                                        $proses = "Menunggu Setoran DP";
                                        $prosesBadge = "warning";
                                    } elseif($status == "dp"){
                                        $proses = "Proses Penerbitan STNK";
                                        $prosesBadge = "info";
                                    } elseif($status == "lunas"){
                                        $proses = "Unit Siap Diambil / Kirim";
                                        $prosesBadge = "success";
                                    } elseif($status == "batal"){
                                        $proses = "Transaksi Hangus";
                                        $prosesBadge = "danger";
                                    } else {
                                        $proses = "-";
                                        $prosesBadge = "secondary";
                                    }

                                    $ktpAda = !empty($row['foto_ktp']);
                                ?>

                                    <tr>
                                        <td class="text-center font-weight-bold text-gray-700"><?= $no++; ?></td>

                                        <td>
                                            <div class="main-text"><?= htmlspecialchars($row['nama_pembeli']); ?></div>
                                            <div class="muted-text text-primary font-weight-bold">
                                                <i class="fab fa-whatsapp mr-1"></i><?= htmlspecialchars($row['no_hp'] ?? '-'); ?>
                                            </div>
                                            <div class="muted-text text-xs text-gray-500 mt-1">
                                                ID: #INV-<?= $row['id_pemesanan']; ?>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="main-text"><?= htmlspecialchars($row['nama_mobil']); ?></div>
                                            <div class="muted-text font-weight-bold text-gray-700">Th. <?= htmlspecialchars($row['tahun']); ?></div>
                                            <div class="muted-text text-xs mt-1">
                                                <i class="far fa-calendar-alt mr-1"></i><?= date('d M Y H:i', strtotime($row['tanggal_pesan'])); ?>
                                            </div>
                                        </td>

                                        <td class="pay-box">
                                            <div class="d-flex justify-content-between text-xs">
                                                <span class="text-gray-600">Harga Unit:</span>
                                                <span class="font-weight-bold text-gray-800"><?= rupiah($totalHarga); ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between text-xs border-bottom pb-1 mb-1">
                                                <span class="text-success">Dana Masuk:</span>
                                                <span class="font-weight-bold text-success"><?= rupiah($totalBayar); ?></span>
                                            </div>
                                            
                                            <?php if($sisaTagihan > 0 && $status != "batal"){ ?>
                                                <div class="d-flex justify-content-between text-xs font-weight-bold text-danger">
                                                    <span>Sisa Piutang:</span>
                                                    <span><?= rupiah($sisaTagihan); ?></span>
                                                </div>
                                            <?php } else { ?>
                                                <div class="text-right text-xs font-weight-bold text-success">
                                                    <i class="fas fa-check-circle mr-1"></i>Lunas Terbayar
                                                </div>
                                            <?php } ?>
                                        </td>

                                        <td class="text-center">
                                            <div class="mb-1">
                                                <?php if($ktpAda){ ?>
                                                    <span class="badge badge-success btn-block text-xs">
                                                        <i class="fas fa-check mr-1"></i>KTP Terlampir
                                                    </span>
                                                <?php } else { ?>
                                                    <span class="badge badge-danger btn-block text-xs">
                                                        <i class="fas fa-times mr-1"></i>KTP Belum Ada
                                                    </span>
                                                <?php } ?>
                                            </div>

                                            <?php if(!empty($row['bukti_pembayaran']) && $row['bukti_pembayaran'] != "-"){ ?>
                                                <span class="badge badge-light border text-primary text-xs btn-block">
                                                    <i class="fas fa-receipt mr-1"></i>Ada Bukti Transfer
                                                </span>
                                            <?php } else { ?>
                                                <span class="badge badge-light border text-muted text-xs btn-block">
                                                    <i class="fas fa-wallet mr-1"></i>Bayar Tunai / Slip (-)
                                                </span>
                                            <?php } ?>
                                        </td>

                                        <td>
                                            <div class="mb-1 text-center">
                                                <span class="badge badge-flat text-xs text-<?= badgeStatus($status); ?> font-weight-bold">
                                                    ● <?= strtoupper($status); ?>
                                                </span>
                                            </div>
                                            <div class="text-center border-top pt-1 mt-1 small">
                                                <span class="text-xs text-dark font-weight-bold d-block"><?= $proses; ?></span>
                                                
                                                <?php if($status == "booking" && !empty($row['deadline_dp'])){ ?>
                                                    <span class="text-xs text-danger font-weight-bold bg-danger-light px-1 rounded">
                                                        Batas DP: <?= date('d/m/Y', strtotime($row['deadline_dp'])); ?>
                                                    </span>
                                                <?php } elseif($status == "dp"){ ?>
                                                    <span class="text-xs text-muted">Est. STNK: ± 14 Hari</span>
                                                <?php } elseif($status == "lunas"){ ?>
                                                    <span class="text-xs text-muted">Est. BPKB: ± 60 Hari</span>
                                                <?php } ?>
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            <a href="detail_transaksi.php?id=<?= $row['id_pemesanan']; ?>" 
                                               class="btn btn-sm btn-info btn-icon" 
                                               title="Buka Berkas & Log Detail Pembayaran">
                                                <i class="fas fa-folder-open"></i>
                                            </a>

                                            <?php if($ktpAda){ ?>
                                                <a href="../uploads/<?= htmlspecialchars($row['foto_ktp']); ?>"
                                                   target="_blank"
                                                   class="btn btn-sm btn-secondary btn-icon"
                                                   title="Pratinjau KTP Pembeli">
                                                    <i class="fas fa-id-card"></i>
                                                </a>
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
                                <p class="text-muted mb-0">Data transaksi berkas kendaraan otomatis muncul di sini setelah pembeli membooking unit.</p>
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
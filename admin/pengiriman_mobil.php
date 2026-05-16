<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../auth/login.php");
    exit;
}

mysqli_query($koneksi, "ALTER TABLE pengiriman MODIFY status ENUM('diproses','dikirim','selesai','terkirim') DEFAULT 'diproses'");
mysqli_query($koneksi, "ALTER TABLE pengiriman ADD COLUMN IF NOT EXISTS bukti_pengiriman VARCHAR(255) DEFAULT NULL");
mysqli_query($koneksi, "ALTER TABLE pengiriman ADD COLUMN IF NOT EXISTS tanggal_terkirim DATETIME DEFAULT NULL");

/* HAPUS */
if(isset($_GET['hapus'])){
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);

    mysqli_query($koneksi, "DELETE FROM surat_jalan WHERE id_pengiriman='$id'");
    mysqli_query($koneksi, "DELETE FROM pengiriman WHERE id_pengiriman='$id'");

    $_SESSION['success'] = "Data pengiriman berhasil dihapus.";
    header("Location: pengiriman_mobil.php");
    exit;
}

/* CETAK SURAT JALAN */
if(isset($_GET['surat_jalan'])){
    $id_pengiriman = mysqli_real_escape_string($koneksi, $_GET['surat_jalan']);

    $cek = mysqli_query($koneksi, "
        SELECT id_suratjalan 
        FROM surat_jalan 
        WHERE id_pengiriman='$id_pengiriman'
    ");

    if(mysqli_num_rows($cek) == 0){
        mysqli_query($koneksi, "
            INSERT INTO surat_jalan 
            (id_pengiriman, tanggal_cetak)
            VALUES
            ('$id_pengiriman', NOW())
        ");
    }

    header("Location: surat_jalan.php?id=$id_pengiriman");
    exit;
}

/* DATA PENGIRIMAN */
$data = mysqli_query($koneksi, "
    SELECT 
        pg.*,

        p.total_harga,
        p.status AS status_pemesanan,

        b.nama AS nama_pembeli,
        b.no_hp AS no_hp_pembeli,

        m.nama_mobil,
        m.tahun,

        k.nama AS nama_kurir,
        k.no_hp AS no_hp_kurir,

        sj.id_suratjalan,
        sj.tanggal_cetak

    FROM pengiriman pg
    JOIN pemesanan p ON pg.id_pemesanan = p.id_pemesanan
    JOIN pembeli b ON p.id_pembeli = b.id_pembeli
    JOIN mobil m ON p.id_mobil = m.id_mobil
    LEFT JOIN kurir k ON pg.id_kurir = k.id_kurir
    LEFT JOIN surat_jalan sj ON pg.id_pengiriman = sj.id_pengiriman

    ORDER BY pg.id_pengiriman DESC
");

if(!$data){
    die("Query error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengiriman Mobil</title>

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
            $pageTitle = "Pengiriman Mobil";
            include "../includes/topbar.php"; 
            ?>

            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Pengiriman Mobil</h1>

                    <a href="tambah_pengiriman.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Pengiriman
                    </a>
                </div>

                <?php if(isset($_SESSION['success'])){ ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php } ?>

                <?php if(isset($_SESSION['error'])){ ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php } ?>

                <div class="card shadow mb-4">

                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Monitoring Pengiriman Mobil
                        </h6>

                        <input type="text"
                               id="searchInput"
                               class="form-control form-control-sm"
                               style="max-width:280px;"
                               placeholder="Cari pembeli / mobil / kurir..."
                               onkeyup="filterTable()">
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover text-center" id="tablePengiriman">

                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Pembeli</th>
                                        <th>Mobil</th>
                                        <th>Kurir</th>
                                        <th>Alamat Kirim</th>
                                        <th>Status</th>
                                        <th>Bukti</th>
                                        <th>Surat Jalan</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php if(mysqli_num_rows($data) > 0){ ?>

                                        <?php
                                        $no = 1;
                                        while($row = mysqli_fetch_assoc($data)){

                                            if($row['status'] == 'diproses'){
                                                $badge = 'warning';
                                                $textStatus = 'Diproses';
                                            } elseif($row['status'] == 'dikirim'){
                                                $badge = 'info';
                                                $textStatus = 'Dikirim';
                                            } elseif($row['status'] == 'terkirim' || $row['status'] == 'selesai'){
                                                $badge = 'success';
                                                $textStatus = 'Telah Terkirim';
                                            } else {
                                                $badge = 'secondary';
                                                $textStatus = '-';
                                            }
                                        ?>

                                        <tr>
                                            <td><?= $no++; ?></td>

                                            <td>
                                                <strong><?= htmlspecialchars($row['nama_pembeli']); ?></strong><br>
                                                <small class="small-text">
                                                    <?= htmlspecialchars($row['no_hp_pembeli']); ?>
                                                </small>
                                            </td>

                                            <td>
                                                <strong><?= htmlspecialchars($row['nama_mobil']); ?></strong><br>
                                                <small class="small-text">
                                                    Tahun <?= htmlspecialchars($row['tahun']); ?>
                                                </small>
                                            </td>

                                            <td>
                                                <strong><?= htmlspecialchars($row['nama_kurir'] ?? '-'); ?></strong><br>
                                                <small class="small-text">
                                                    <?= htmlspecialchars($row['no_hp_kurir'] ?? '-'); ?>
                                                </small>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['alamat_kirim']); ?>
                                            </td>

                                            <td>
                                                <span class="badge badge-<?= $badge; ?>">
                                                    <?= $textStatus; ?>
                                                </span>

                                                <?php if(!empty($row['tanggal_terkirim'])){ ?>
                                                    <br>
                                                    <small class="small-text">
                                                        <?= date('d-m-Y H:i', strtotime($row['tanggal_terkirim'])); ?>
                                                    </small>
                                                <?php } ?>
                                            </td>

                                            <td>
                                                <?php if(!empty($row['bukti_pengiriman'])){ ?>
                                                    <a href="../uploads/<?= htmlspecialchars($row['bukti_pengiriman']); ?>"
                                                       target="_blank"
                                                       class="btn btn-sm btn-success">
                                                        <i class="fas fa-eye"></i> Lihat
                                                    </a>
                                                <?php } else { ?>
                                                    -
                                                <?php } ?>
                                            </td>

                                            <td>
                                                <a href="pengiriman_mobil.php?surat_jalan=<?= $row['id_pengiriman']; ?>"
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-print"></i> Cetak
                                                </a>
                                            </td>
                                        </tr>

                                        <?php } ?>

                                    <?php } else { ?>

                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">
                                                Belum ada data pengiriman.
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
    let input = document.getElementById("searchInput").value.toLowerCase();
    let rows = document.querySelectorAll("#tablePengiriman tbody tr");

    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(input) ? "" : "none";
    });
}
</script>

</body>
</html>
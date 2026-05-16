<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "penjual"){
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

$q_penjual = mysqli_query($koneksi, "
    SELECT * FROM penjual 
    WHERE id_user='$id_user'
    LIMIT 1
");

$penjual = mysqli_fetch_assoc($q_penjual);

if(!$penjual){
    die("Data penjual tidak ditemukan.");
}

$id_penjual = $penjual['id_penjual'];

/* HAPUS PENAWARAN */
if(isset($_GET['hapus'])){
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);

    mysqli_query($koneksi, "
        DELETE FROM penawaran
        WHERE id_penawaran='$id'
        AND id_penjual='$id_penjual'
        AND status='menunggu'
    ");

    $_SESSION['success'] = "Penawaran berhasil dihapus.";
    header("Location: penawaran_mobil.php");
    exit;
}

/* DATA PENAWARAN */
$data = mysqli_query($koneksi, "
    SELECT 
        pn.*,
        m.nama_mobil,
        m.tahun,
        m.harga,
        m.foto
    FROM penawaran pn
    LEFT JOIN mobil m ON pn.id_mobil = m.id_mobil
    WHERE pn.id_penjual='$id_penjual'
    ORDER BY pn.id_penawaran DESC
");

if(!$data){
    die("Query error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Penawaran Mobil</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .table td{ vertical-align:middle; font-size:14px; }
        .table th{ font-size:14px; white-space:nowrap; }
        .badge{ font-size:12px; padding:6px 9px; }
        .mobil-img{
            width:75px;
            height:55px;
            object-fit:cover;
            border-radius:8px;
        }
        .small-text{ font-size:12px; color:#858796; }
    </style>
</head>

<body id="page-top">

<div id="wrapper">

    <?php include "../includes/sidebar_penjual.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php 
            $pageTitle = "Penawaran Mobil";
            include "../includes/topbar.php"; 
            ?>

            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Penawaran Mobil</h1>

                    <a href="tambah_penawaran.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Penawaran
                    </a>
                </div>

                <?php if(isset($_SESSION['success'])){ ?>
                    <div class="alert alert-success">
                        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php } ?>

                <?php if(isset($_SESSION['error'])){ ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php } ?>

                <div class="card shadow mb-4">

                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Riwayat Penawaran Saya
                        </h6>

                        <input type="text"
                               id="searchInput"
                               class="form-control form-control-sm"
                               style="max-width:260px;"
                               placeholder="Cari mobil..."
                               onkeyup="filterTable()">
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover text-center" id="tablePenawaran">

                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Foto</th>
                                        <th>Mobil</th>
                                        <th>Harga Mobil</th>
                                        <th>Harga Tawar</th>
                                        <th>Status</th>
                                        <th>Metode</th>
                                        <th>Catatan Admin</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php if(mysqli_num_rows($data) > 0){ ?>

                                        <?php
                                        $no = 1;
                                        while($row = mysqli_fetch_assoc($data)){

                                            $status = $row['status'] ?? 'menunggu';

                                            if($status == 'diterima'){
                                                $badge = 'success';
                                                $text = 'Diterima';
                                            } elseif($status == 'ditolak'){
                                                $badge = 'danger';
                                                $text = 'Ditolak';
                                            } else {
                                                $badge = 'warning';
                                                $text = 'Menunggu';
                                            }

                                            $foto = !empty($row['foto']) ? "../uploads/" . $row['foto'] : "../assets/img/no-image.png";
                                        ?>

                                        <tr>
                                            <td><?= $no++; ?></td>

                                            <td>
                                                <img src="<?= htmlspecialchars($foto); ?>" class="mobil-img">
                                            </td>

                                            <td>
                                                <strong><?= htmlspecialchars($row['nama_mobil'] ?? '-'); ?></strong><br>
                                                <small class="small-text">
                                                    Tahun <?= htmlspecialchars($row['tahun'] ?? '-'); ?>
                                                </small>
                                            </td>

                                            <td>
                                                Rp <?= number_format($row['harga'] ?? 0, 0, ',', '.'); ?>
                                            </td>

                                            <td>
                                                <strong>
                                                    Rp <?= number_format($row['harga_tawar'] ?? 0, 0, ',', '.'); ?>
                                                </strong>
                                            </td>

                                            <td>
                                                <span class="badge badge-<?= $badge; ?>">
                                                    <?= $text; ?>
                                                </span>
                                            </td>

                                            <td>
                                                <?= !empty($row['metode_pembayaran']) ? ucfirst($row['metode_pembayaran']) : '-'; ?>
                                            </td>

                                            <td>
                                                <?= !empty($row['catatan_admin']) ? htmlspecialchars($row['catatan_admin']) : '-'; ?>
                                            </td>

                                            <td>
                                                <?php if($status == 'menunggu'){ ?>
                                                    <a href="penawaran_mobil.php?hapus=<?= $row['id_penawaran']; ?>"
                                                       class="btn btn-sm btn-danger"
                                                       onclick="return confirm('Yakin ingin menghapus penawaran ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                <?php } elseif($status == 'diterima'){ ?>
                                                    <span class="badge badge-success">
                                                        Disetujui
                                                    </span>
                                                <?php } else { ?>
                                                    <span class="badge badge-danger">
                                                        Ditolak
                                                    </span>
                                                <?php } ?>
                                            </td>
                                        </tr>

                                        <?php } ?>

                                    <?php } else { ?>

                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">
                                                Belum ada penawaran mobil.
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
    let rows = document.querySelectorAll("#tablePenawaran tbody tr");

    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(input) ? "" : "none";
    });
}
</script>

</body>
</html>
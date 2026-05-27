<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "penjual"){
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$q_penjual = mysqli_query($koneksi, "SELECT id_penjual FROM penjual WHERE id_user='$id_user' LIMIT 1");
$penjual = mysqli_fetch_assoc($q_penjual);

if(!$penjual){ die("Data penjual tidak ditemukan."); }
$id_penjual = $penjual['id_penjual'];

if(isset($_GET['hapus'])){
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM penawaran WHERE id_penawaran='$id' AND id_penjual='$id_penjual' AND status='menunggu'");
    $_SESSION['success'] = "Penawaran berhasil dihapus.";
    header("Location: penawaran_mobil.php");
    exit;
}

$data = mysqli_query($koneksi, "
    SELECT 
        m.id_mobil, m.nama_mobil, m.tahun, m.harga, m.foto,
        pn.id_penawaran, pn.status
    FROM mobil m
    LEFT JOIN penawaran pn ON m.id_mobil = pn.id_mobil AND pn.id_penjual = '$id_penjual'
    WHERE m.id_penjual = '$id_penjual' 
    ORDER BY m.id_mobil DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Mobil Saya & Penawaran</title>
    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
    <style>
        .mobil-img { width:80px; height:60px; object-fit:cover; border-radius:5px; cursor: pointer; transition: 0.3s; }
        .mobil-img:hover { opacity: 0.8; transform: scale(1.05); }
        .table td { vertical-align:middle; }
    </style>
</head>
<body id="page-top">
<div id="wrapper">
    <?php include "../includes/sidebar_penjual.php"; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include "../includes/topbar.php"; ?>
            <div class="container-fluid">
                <h1 class="h3 mb-4 text-gray-800">Daftar Mobil Milik Saya</h1>

                <?php if(isset($_SESSION['success'])){ ?>
                    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php } ?>

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th>Foto</th>
                                        <th>Mobil</th>
                                        <th>Harga</th>
                                        <th>Status Penawaran</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($data)){ 
                                        $foto = !empty($row['foto']) ? "../uploads/" . $row['foto'] : "../assets/img/no-image.png";
                                    ?>
                                    <tr>
                                        <td><img src="<?= $foto ?>" class="mobil-img" onclick="previewImage('<?= $foto ?>', '<?= $row['nama_mobil'] ?>')"></td>
                                        <td><strong><?= $row['nama_mobil'] ?></strong><br><small><?= $row['tahun'] ?></small></td>
                                        <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                        <td>
                                            <?php if(empty($row['id_penawaran'])): ?>
                                                <span class="badge badge-secondary">Belum ada penawaran</span>
                                            <?php else: ?>
                                                <span class="badge badge-<?= ($row['status']=='diterima')?'success':(($row['status']=='ditolak')?'danger':'warning') ?>">
                                                    <?= ucfirst($row['status']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="detail_mobil.php?id=<?= $row['id_mobil'] ?>" class="btn btn-sm btn-info mb-1"><i class="fas fa-info-circle"></i> Detail</a>
                                            
                                            <?php if(empty($row['id_penawaran'])): ?>
                                                <a href="tambah_penawaran.php?id_mobil=<?= $row['id_mobil'] ?>" class="btn btn-sm btn-primary mb-1">Tawar Mobil</a>
                                            <?php else: ?>
                                                <a href="#" class="btn btn-sm btn-secondary mb-1 disabled" aria-disabled="true" style="pointer-events: none;">Tawar Mobil</a>
                                                <?php if($row['status'] == 'menunggu'): ?>
                                                    <a href="penawaran_mobil.php?hapus=<?= $row['id_penawaran'] ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Yakin hapus?')">Hapus</a>
                                                <?php endif; ?>
                                            <?php endif; ?>
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

<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Foto Mobil</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" style="max-height: 500px;">
            </div>
        </div>
    </div>
</div>

<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
    function previewImage(src, title) {
        document.getElementById('modalImage').src = src;
        document.getElementById('modalTitle').innerText = title;
        $('#imageModal').modal('show');
    }
</script>
</body>
</html>
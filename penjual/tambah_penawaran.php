<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "penjual"){
    header("Location: ../auth/login.php");
    exit;
}

// Menangkap ID mobil dari URL jika ada
$id_terpilih = isset($_GET['id_mobil']) ? $_GET['id_mobil'] : '';

/*
|------------------------------------------------------
| MOBIL YANG BELUM PERNAH DITAWARKAN
|------------------------------------------------------
*/
$mobil = mysqli_query($koneksi, "
    SELECT 
        m.id_mobil,
        m.nama_mobil,
        m.tahun,
        m.harga
    FROM mobil m
    WHERE m.id_penjual = (SELECT id_penjual FROM penjual WHERE id_user = '{$_SESSION['id_user']}')
    AND NOT EXISTS (
        SELECT 1 
        FROM penawaran p 
        WHERE p.id_mobil = m.id_mobil
    )
    ORDER BY m.id_mobil DESC
");

if(!$mobil){
    die("Query mobil error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Penawaran</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>

<body id="page-top">

<div id="wrapper">

    <?php include "../includes/sidebar_penjual.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php 
            $pageTitle = "Tambah Penawaran";
            include "../includes/topbar.php"; 
            ?>

            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Tambah Penawaran Mobil</h1>

                    <a href="penawaran_mobil.php" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <?php if(isset($_SESSION['error'])){ ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php } ?>

                <div class="card shadow mb-4">

                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Form Tambah Penawaran
                        </h6>
                    </div>

                    <div class="card-body">

                        <form action="proses_tambah_penawaran.php" method="POST">

                            <div class="form-group">
                                <label>Pilih Mobil</label>

                                <select name="id_mobil" class="form-control" required>
                                    <option value="">-- Pilih Mobil --</option>

                                    <?php if(mysqli_num_rows($mobil) > 0){ ?>

                                        <?php while($m = mysqli_fetch_assoc($mobil)){ ?>
                                            <option value="<?= $m['id_mobil']; ?>" <?= ($id_terpilih == $m['id_mobil']) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($m['nama_mobil']); ?> -
                                                <?= htmlspecialchars($m['tahun']); ?> |
                                                Rp <?= number_format($m['harga'], 0, ',', '.'); ?>
                                            </option>
                                        <?php } ?>

                                    <?php } else { ?>

                                        <option value="" disabled>
                                            Semua mobil sudah pernah ditawarkan atau tidak tersedia
                                        </option>

                                    <?php } ?>

                                </select>
                            </div>

                            <div class="form-group">
                                <label>Harga Tawar</label>

                                <input type="number"
                                       name="harga_tawar"
                                       class="form-control"
                                       placeholder="Masukkan harga tawar"
                                       required>
                            </div>

                            <div class="form-group">
                                <label>Catatan</label>

                                <textarea name="catatan"
                                          class="form-control"
                                          rows="4"
                                          placeholder="Contoh: kondisi mobil bagus, surat lengkap, pajak hidup"></textarea>
                            </div>

                            <button type="submit"
                                    name="simpan"
                                    class="btn btn-primary"
                                    <?= (mysqli_num_rows($mobil) == 0) ? 'disabled' : ''; ?>>
                                <i class="fas fa-save"></i>
                                Simpan Penawaran
                            </button>

                            <a href="penawaran_mobil.php" class="btn btn-secondary">
                                Batal
                            </a>

                        </form>

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

</body>
</html>
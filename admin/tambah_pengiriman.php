<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../auth/login.php");
    exit;
}

/* PEMESANAN LUNAS YANG BELUM ADA PENGIRIMAN */
$pemesanan = mysqli_query($koneksi, "
    SELECT 
        p.id_pemesanan,
        p.total_harga,
        p.status,

        b.nama AS nama_pembeli,
        b.alamat,
        b.no_hp,

        m.nama_mobil,
        m.tahun

    FROM pemesanan p
    JOIN pembeli b ON p.id_pembeli = b.id_pembeli
    JOIN mobil m ON p.id_mobil = m.id_mobil
    LEFT JOIN pengiriman pg ON p.id_pemesanan = pg.id_pemesanan

    WHERE p.status='lunas'
    AND pg.id_pengiriman IS NULL

    ORDER BY p.id_pemesanan DESC
");

if(!$pemesanan){
    die("Query pemesanan error: " . mysqli_error($koneksi));
}

/* DATA KURIR */
$kurir = mysqli_query($koneksi, "
    SELECT 
        id_kurir,
        nama,
        no_hp
    FROM kurir
    ORDER BY nama ASC
");

if(!$kurir){
    die("Query kurir error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pengiriman</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
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
            $pageTitle = "Tambah Pengiriman";
            include "../includes/topbar.php"; 
            ?>

            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Tambah Pengiriman</h1>

                    <a href="pengiriman_mobil.php" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <?php if(isset($_SESSION['error'])){ ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php } ?>

                <div class="card shadow mb-4">

                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Form Tambah Pengiriman Mobil
                        </h6>
                    </div>

                    <div class="card-body">

                        <form action="proses_tambah_pengiriman.php" method="POST">

                            <div class="form-group">
                                <label>Pemesanan Lunas</label>

                                <select name="id_pemesanan"
                                        id="selectPemesanan"
                                        class="form-control"
                                        required>

                                    <option value="">-- Pilih Pemesanan --</option>

                                    <?php if(mysqli_num_rows($pemesanan) > 0){ ?>

                                        <?php while($p = mysqli_fetch_assoc($pemesanan)){ ?>
                                            <option value="<?= $p['id_pemesanan']; ?>"
                                                    data-alamat="<?= htmlspecialchars($p['alamat']); ?>">

                                                #<?= $p['id_pemesanan']; ?> -
                                                <?= htmlspecialchars($p['nama_pembeli']); ?> |
                                                <?= htmlspecialchars($p['nama_mobil']); ?> |
                                                Rp <?= number_format($p['total_harga'], 0, ',', '.'); ?>

                                            </option>
                                        <?php } ?>

                                    <?php } else { ?>
                                        <option value="" disabled>
                                            Tidak ada pemesanan lunas yang bisa dikirim
                                        </option>
                                    <?php } ?>

                                </select>

                                <small class="small-text">
                                    Hanya transaksi dengan status lunas dan belum punya pengiriman yang muncul.
                                </small>
                            </div>

                            <div class="form-group">
                                <label>Kurir</label>

                                <select name="id_kurir" class="form-control" required>
                                    <option value="">-- Pilih Kurir --</option>

                                    <?php if(mysqli_num_rows($kurir) > 0){ ?>

                                        <?php while($k = mysqli_fetch_assoc($kurir)){ ?>
                                            <option value="<?= $k['id_kurir']; ?>">
                                                <?= htmlspecialchars($k['nama']); ?> - <?= htmlspecialchars($k['no_hp']); ?>
                                            </option>
                                        <?php } ?>

                                    <?php } else { ?>
                                        <option value="" disabled>
                                            Data kurir belum tersedia
                                        </option>
                                    <?php } ?>

                                </select>

                                <?php if(mysqli_num_rows($kurir) == 0){ ?>
                                    <small class="text-danger">
                                        Tambahkan data kurir terlebih dahulu.
                                    </small>
                                <?php } ?>
                            </div>

                            <div class="form-group">
                                <label>Alamat Kirim</label>

                                <textarea name="alamat_kirim"
                                          id="alamatKirim"
                                          class="form-control"
                                          rows="4"
                                          required
                                          placeholder="Alamat pengiriman akan otomatis terisi dari alamat pembeli"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Status Awal</label>

                                <select name="status" class="form-control" required>
                                    <option value="diproses">Diproses</option>
                                    <option value="dikirim">Dikirim</option>
                                </select>
                            </div>

                            <button type="submit" name="simpan" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Pengiriman
                            </button>

                            <a href="pengiriman_mobil.php" class="btn btn-secondary">
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

<script>
const selectPemesanan = document.getElementById("selectPemesanan");
const alamatKirim = document.getElementById("alamatKirim");

if(selectPemesanan){
    selectPemesanan.addEventListener("change", function(){
        const selected = this.options[this.selectedIndex];
        const alamat = selected.getAttribute("data-alamat");

        if(alamat){
            alamatKirim.value = alamat;
        } else {
            alamatKirim.value = "";
        }
    });
}
</script>

</body>
</html>
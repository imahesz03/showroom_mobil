<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "pembeli") {
    header("Location: ../auth/login.php");
    exit;
}

$id_mobil = $_GET['id'] ?? '';

if (empty($id_mobil)) {
    echo "<script>alert('ID mobil tidak ditemukan!'); window.location='lihat_mobil.php';</script>";
    exit;
}

$id_mobil = mysqli_real_escape_string($koneksi, $id_mobil);

$qMobil = mysqli_query($koneksi, "
    SELECT * FROM mobil 
    WHERE id_mobil='$id_mobil' 
    LIMIT 1
");

if (!$qMobil || mysqli_num_rows($qMobil) == 0) {
    echo "<script>alert('Data mobil tidak ditemukan!'); window.location='lihat_mobil.php';</script>";
    exit;
}

$mobil = mysqli_fetch_assoc($qMobil);

if ((int)$mobil['stok'] <= 0) {
    echo "<script>alert('Stok mobil habis!'); window.location='lihat_mobil.php';</script>";
    exit;
}

function rupiah($angka){
    return "Rp " . number_format((float)$angka, 0, ',', '.');
}

$booking_fee = 500000;
$harga_mobil = (float)$mobil['harga'];
$dp30 = $harga_mobil * 0.30;
$sisa_dp = max($dp30 - $booking_fee, 0);
$pelunasan = $harga_mobil - $dp30;

$foto = !empty($mobil['foto']) ? "../uploads/" . $mobil['foto'] : "../assets/img/undraw_posting_photo.svg";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesan Mobil</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>

<body id="page-top">

<div id="wrapper">

    <?php include "../includes/sidebar_pembeli.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php 
            $pageTitle = "Pesan Mobil"; 
            include "../includes/topbar.php"; 
            ?>

            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Pesan Mobil</h1>
                        <p class="mb-0 text-gray-600">
                            Booking mobil terlebih dahulu sebesar Rp 500.000.
                        </p>
                    </div>

                    <a href="lihat_mobil.php" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>

                <?php if(isset($_SESSION['error'])){ ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php } ?>

                <div class="row">

                    <div class="col-lg-5">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Detail Mobil</h6>
                            </div>

                            <div class="card-body text-center">
                                <img src="<?= htmlspecialchars($foto); ?>"
                                     class="img-fluid img-thumbnail mb-3"
                                     style="max-height:230px; object-fit:cover;"
                                     onerror="this.src='../assets/img/undraw_posting_photo.svg'">

                                <h4 class="font-weight-bold text-gray-800">
                                    <?= htmlspecialchars($mobil['nama_mobil']); ?>
                                </h4>

                                <p class="mb-1">Tahun <?= htmlspecialchars($mobil['tahun']); ?></p>
                                <p class="mb-1">Stok <?= htmlspecialchars($mobil['stok']); ?></p>

                                <h5 class="font-weight-bold text-primary mt-3">
                                    <?= rupiah($harga_mobil); ?>
                                </h5>

                                <hr>

                                <div class="text-left">
                                    <p class="mb-1">Booking: <strong><?= rupiah($booking_fee); ?></strong></p>
                                    <p class="mb-1">DP 30%: <strong><?= rupiah($dp30); ?></strong></p>
                                    <p class="mb-1">Sisa DP: <strong><?= rupiah($sisa_dp); ?></strong></p>
                                    <p class="mb-0">Pelunasan: <strong><?= rupiah($pelunasan); ?></strong></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">

                        <div class="card shadow mb-4">

                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Form Booking</h6>
                            </div>

                            <div class="card-body">

                                <div class="alert alert-info">
                                    <strong>Alur Pembayaran:</strong><br>
                                    1. Booking sebesar <strong>Rp 500.000</strong>.<br>
                                    2. Status pesanan menjadi <strong>Booking</strong>.<br>
                                    3. Lanjut bayar DP dalam waktu <strong>7 hari</strong>.<br>
                                    4. Setelah DP masuk, status menjadi <strong>DP</strong>.<br>
                                    5. Setelah pelunasan masuk, status menjadi <strong>Lunas</strong>.
                                </div>

                                <form action="proses_pesan_mobil.php" method="POST" enctype="multipart/form-data">

                                    <input type="hidden" name="id_mobil" value="<?= htmlspecialchars($mobil['id_mobil']); ?>">

                                    <div class="form-group">
                                        <label>Total Harga Mobil</label>
                                        <input type="text" class="form-control" value="<?= rupiah($harga_mobil); ?>" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label>Jumlah Booking</label>
                                        <input type="text" class="form-control" value="<?= rupiah($booking_fee); ?>" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label>Metode Bayar</label>
                                        <select name="metode_bayar" id="metodeBayar" class="form-control" required>
                                            <option value="">-- Pilih Metode --</option>
                                            <option value="tunai">Tunai</option>
                                            <option value="transfer">Transfer</option>
                                        </select>
                                    </div>

                                    <div class="form-group" id="buktiBox" style="display:none;">
                                        <label>Bukti Transfer</label>
                                        <input type="file"
                                               name="bukti_pembayaran"
                                               id="buktiPembayaran"
                                               class="form-control-file"
                                               accept="image/jpeg,image/png,image/webp">

                                        <small class="text-muted">
                                            Wajib jika transfer. Format JPG, PNG, atau WEBP maksimal 2MB.
                                        </small>
                                    </div>

                                    <button type="submit" name="pesan" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i> Simpan Booking
                                    </button>

                                    <a href="lihat_mobil.php" class="btn btn-secondary">Batal</a>

                                </form>

                            </div>

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
document.getElementById('metodeBayar').addEventListener('change', function(){
    const box = document.getElementById('buktiBox');
    const bukti = document.getElementById('buktiPembayaran');

    if(this.value === 'transfer'){
        box.style.display = 'block';
        bukti.required = true;
    } else {
        box.style.display = 'none';
        bukti.required = false;
        bukti.value = '';
    }
});
</script>

</body>
</html>
<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "pembeli") {
    header("Location: ../auth/login.php");
    exit;
}

$id_user      = $_SESSION['id_user'] ?? 0;
$id_pemesanan = $_GET['id'] ?? '';
$jenis        = $_GET['jenis'] ?? '';

if (empty($id_pemesanan) || empty($jenis)) {
    echo "<script>
        alert('Data pembayaran tidak lengkap!');
        window.location='pesanan_saya.php';
    </script>";
    exit;
}

function rupiah($angka)
{
    return "Rp " . number_format((float)$angka, 0, ',', '.');
}

$qPembeli = mysqli_query($koneksi, "
    SELECT id_pembeli 
    FROM pembeli 
    WHERE id_user='$id_user'
    LIMIT 1
");

if (!$qPembeli || mysqli_num_rows($qPembeli) == 0) {
    echo "<script>
        alert('Data pembeli tidak ditemukan!');
        window.location='../auth/login.php';
    </script>";
    exit;
}

$pembeli = mysqli_fetch_assoc($qPembeli);
$id_pembeli = $pembeli['id_pembeli'];

$qPesanan = mysqli_query($koneksi, "
    SELECT 
        p.*,
        m.nama_mobil,
        m.tahun,
        m.foto
    FROM pemesanan p
    LEFT JOIN mobil m ON p.id_mobil = m.id_mobil
    WHERE p.id_pemesanan='$id_pemesanan'
    AND p.id_pembeli='$id_pembeli'
    LIMIT 1
");

if (!$qPesanan) {
    die("Query pesanan error: " . mysqli_error($koneksi));
}

if (mysqli_num_rows($qPesanan) == 0) {
    echo "<script>
        alert('Pesanan tidak ditemukan!');
        window.location='pesanan_saya.php';
    </script>";
    exit;
}

$pesanan = mysqli_fetch_assoc($qPesanan);

$total_harga = (float)$pesanan['total_harga'];
$booking_fee = 500000;
$dp30        = $total_harga * 30 / 100;

$status_pesanan = strtolower($pesanan['status']);

if ($jenis == "dp") {

    if ($status_pesanan != "booking") {
        echo "<script>
            alert('DP hanya bisa dibayar jika status pesanan masih booking.');
            window.location='pesanan_saya.php';
        </script>";
        exit;
    }

    $judul = "Bayar DP 30%";
    $jumlah_bayar = max($dp30 - $booking_fee, 0);

} elseif ($jenis == "pelunasan") {

    if ($status_pesanan == "booking") {
        $judul = "Pelunasan Langsung";
        $jumlah_bayar = max($total_harga - $booking_fee, 0);
    } elseif ($status_pesanan == "dp") {
        $judul = "Pelunasan Pembayaran";
        $jumlah_bayar = max($total_harga - $dp30, 0);
    } else {
        echo "<script>
            alert('Pesanan ini tidak bisa dilunasi.');
            window.location='pesanan_saya.php';
        </script>";
        exit;
    }

} else {
    echo "<script>
        alert('Jenis pembayaran tidak valid!');
        window.location='pesanan_saya.php';
    </script>";
    exit;
}

$foto = !empty($pesanan['foto'])
    ? "../uploads/" . $pesanan['foto']
    : "../assets/img/undraw_posting_photo.svg";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $judul; ?> - Galaxy Showroom</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

<div id="wrapper">

    <?php include "../includes/sidebar_pembeli.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php 
            $pageTitle = $judul;
            include "../includes/topbar.php"; 
            ?>

            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold">
                            <?= $judul; ?>
                        </h1>
                        <p class="mb-0 text-gray-600">
                            Lengkapi pembayaran untuk melanjutkan proses pesanan.
                        </p>
                    </div>

                    <a href="pesanan_saya.php" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Kembali
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
                                <h6 class="m-0 font-weight-bold text-primary">
                                    Detail Pesanan
                                </h6>
                            </div>

                            <div class="card-body text-center">

                                <img src="<?= htmlspecialchars($foto); ?>"
                                     class="img-fluid img-thumbnail mb-3"
                                     style="max-height:220px; object-fit:cover;"
                                     onerror="this.src='../assets/img/undraw_posting_photo.svg'">

                                <h4 class="font-weight-bold text-gray-800">
                                    <?= htmlspecialchars($pesanan['nama_mobil'] ?? '-'); ?>
                                </h4>

                                <p class="mb-1">
                                    Tahun <?= htmlspecialchars($pesanan['tahun'] ?? '-'); ?>
                                </p>

                                <p class="mb-1">Total Harga</p>
                                <h5 class="font-weight-bold text-primary">
                                    <?= rupiah($total_harga); ?>
                                </h5>

                                <hr>

                                <p class="mb-1">Booking Awal</p>
                                <h6 class="font-weight-bold text-warning">
                                    <?= rupiah($booking_fee); ?>
                                </h6>

                                <p class="mb-1 mt-3">
                                    Jumlah Bayar Sekarang
                                </p>
                                <h4 class="font-weight-bold text-success">
                                    <?= rupiah($jumlah_bayar); ?>
                                </h4>

                            </div>

                        </div>

                    </div>

                    <div class="col-lg-7">

                        <div class="card shadow mb-4">

                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    Form Pembayaran
                                </h6>
                            </div>

                            <div class="card-body">

                                <div class="alert alert-info">
                                    <?php if($jenis == "dp"){ ?>
                                        Pembayaran DP dihitung dari 30% harga mobil dikurangi booking awal Rp 500.000.
                                    <?php } else { ?>
                                        Pelunasan akan mengubah status pesanan menjadi lunas.
                                    <?php } ?>
                                </div>

                                <form action="proses_pembayaran.php" method="POST" enctype="multipart/form-data">

                                    <input type="hidden" name="id_pemesanan" value="<?= $pesanan['id_pemesanan']; ?>">
                                    <input type="hidden" name="jenis_pembayaran" value="<?= htmlspecialchars($jenis); ?>">
                                    <input type="hidden" name="jumlah" value="<?= $jumlah_bayar; ?>">

                                    <div class="form-group">
                                        <label>Jenis Pembayaran</label>
                                        <input type="text" class="form-control" value="<?= ucfirst($jenis); ?>" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label>Jumlah Bayar</label>
                                        <input type="text" class="form-control" value="<?= rupiah($jumlah_bayar); ?>" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label>Metode Bayar</label>
                                        <select name="metode_bayar" id="metodeBayar" class="form-control" required>
                                            <option value="">-- Pilih Metode --</option>
                                            <option value="cash">Cash / Tunai</option>
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
                                    </div>

                                    <div class="form-group">
                                        <label>Upload KTP</label>
                                        <input type="file"
                                               name="foto_ktp"
                                               class="form-control-file"
                                               accept="image/jpeg,image/png,image/webp"
                                               required>
                                    </div>

                                    <button type="submit" name="bayar" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i>
                                        Simpan Pembayaran
                                    </button>

                                    <a href="pesanan_saya.php" class="btn btn-secondary">
                                        Batal
                                    </a>

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
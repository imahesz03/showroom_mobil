<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "pembeli") {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id']) || $_GET['id'] == "") {
    header("Location: lihat_mobil.php");
    exit;
}

$id_user  = $_SESSION['id_user'] ?? 0;
$id_mobil = mysqli_real_escape_string($koneksi, $_GET['id']);

function rupiah($angka)
{
    return "Rp " . number_format((float)$angka, 0, ',', '.');
}

$qPembeli = mysqli_query($koneksi, "
    SELECT * FROM pembeli 
    WHERE id_user = '$id_user'
");

if (mysqli_num_rows($qPembeli) == 0) {
    echo "<script>
        alert('Data pembeli tidak ditemukan!');
        window.location='lihat_mobil.php';
    </script>";
    exit;
}

$pembeli = mysqli_fetch_assoc($qPembeli);
$id_pembeli = $pembeli['id_pembeli'];

$qMobil = mysqli_query($koneksi, "
    SELECT * FROM mobil 
    WHERE id_mobil = '$id_mobil'
");

if (mysqli_num_rows($qMobil) == 0) {
    echo "<script>
        alert('Data mobil tidak ditemukan!');
        window.location='lihat_mobil.php';
    </script>";
    exit;
}

$mobil = mysqli_fetch_assoc($qMobil);

if ($mobil['status'] != 'tersedia' || $mobil['stok'] <= 0) {
    echo "<script>
        alert('Mobil ini tidak tersedia!');
        window.location='lihat_mobil.php';
    </script>";
    exit;
}

$foto = !empty($mobil['foto'])
    ? "../uploads/" . $mobil['foto']
    : "../assets/img/undraw_posting_photo.svg";

if (isset($_POST['pesan'])) {

    $metode_bayar = mysqli_real_escape_string($koneksi, $_POST['metode_bayar']);
    $alamat_kirim = mysqli_real_escape_string($koneksi, $_POST['alamat_kirim']);
    $total_harga  = $mobil['harga'];
    $tanggal      = date('Y-m-d H:i:s');

    if ($metode_bayar != "tunai" && $metode_bayar != "transfer") {
        echo "<script>alert('Metode pembayaran tidak valid!');</script>";
    } else {

        $bukti_pembayaran = "";

        if ($metode_bayar == "transfer") {

            if (!empty($_FILES['bukti_pembayaran']['name'])) {

                $nama_file = $_FILES['bukti_pembayaran']['name'];
                $tmp_file  = $_FILES['bukti_pembayaran']['tmp_name'];
                $ext       = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

                $allowed = ['jpg', 'jpeg', 'png'];

                if (!in_array($ext, $allowed)) {
                    echo "<script>alert('Bukti pembayaran harus JPG, JPEG, atau PNG!');</script>";
                } else {

                    $bukti_pembayaran = time() . "_bukti_" . rand(1000, 9999) . "." . $ext;
                    $upload_path = "../uploads/" . $bukti_pembayaran;

                    if (!move_uploaded_file($tmp_file, $upload_path)) {
                        echo "<script>alert('Gagal upload bukti pembayaran!');</script>";
                    }
                }

            } else {
                echo "<script>alert('Bukti transfer wajib diupload!');</script>";
            }
        }

        if ($metode_bayar == "tunai" || ($metode_bayar == "transfer" && $bukti_pembayaran != "")) {

            mysqli_begin_transaction($koneksi);

            try {

                $insertPemesanan = mysqli_query($koneksi, "
                    INSERT INTO pemesanan 
                    (id_pembeli, id_mobil, tanggal_pesan, total_harga, status)
                    VALUES
                    ('$id_pembeli', '$id_mobil', '$tanggal', '$total_harga', 'booking')
                ");

                if (!$insertPemesanan) {
                    throw new Exception(mysqli_error($koneksi));
                }

                $id_pemesanan = mysqli_insert_id($koneksi);

                $insertPembayaran = mysqli_query($koneksi, "
                    INSERT INTO pembayaran
                    (id_pemesanan, metode_bayar, jumlah, status, bukti_pembayaran)
                    VALUES
                    ('$id_pemesanan', '$metode_bayar', '$total_harga', 'pending', '$bukti_pembayaran')
                ");

                if (!$insertPembayaran) {
                    throw new Exception(mysqli_error($koneksi));
                }

                $stok_baru = $mobil['stok'] - 1;
                $status_mobil = ($stok_baru <= 0) ? 'dipesan' : 'tersedia';

                $updateMobil = mysqli_query($koneksi, "
                    UPDATE mobil 
                    SET stok = '$stok_baru',
                        status = '$status_mobil'
                    WHERE id_mobil = '$id_mobil'
                ");

                if (!$updateMobil) {
                    throw new Exception(mysqli_error($koneksi));
                }

                mysqli_commit($koneksi);

                echo "<script>
                    alert('Pemesanan mobil berhasil dibuat!');
                    window.location='pesanan_saya.php';
                </script>";
                exit;

            } catch (Exception $e) {

                mysqli_rollback($koneksi);

                echo "<script>
                    alert('Pemesanan gagal diproses!');
                    window.location='pesan_mobil.php?id=$id_mobil';
                </script>";
                exit;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pesan Mobil - Galaxy Showroom</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

<div id="wrapper">

    <?php include "../includes/sidebar_pembeli.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php include "../includes/topbar.php"; ?>

            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Pesan Mobil</h1>
                        <p class="mb-0 text-gray-600">
                            Lengkapi data pemesanan mobil sebelum melanjutkan transaksi.
                        </p>
                    </div>

                    <a href="lihat_mobil.php" class="btn btn-secondary btn-sm shadow-sm mt-3 mt-sm-0">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Kembali
                    </a>
                </div>

                <div class="row">

                    <div class="col-lg-5 mb-4">
                        <div class="card shadow border-0">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    Detail Mobil
                                </h6>
                            </div>

                            <div class="card-body">

                                <img src="<?= htmlspecialchars($foto); ?>"
                                     onerror="this.src='../assets/img/undraw_posting_photo.svg'"
                                     class="img-fluid rounded shadow-sm mb-3"
                                     style="width:100%; height:280px; object-fit:cover;">

                                <h4 class="font-weight-bold text-gray-800">
                                    <?= htmlspecialchars($mobil['nama_mobil']); ?>
                                </h4>

                                <h5 class="font-weight-bold text-primary mb-3">
                                    <?= rupiah($mobil['harga']); ?>
                                </h5>

                                <div class="mb-2">
                                    <i class="fas fa-calendar-alt text-primary mr-2"></i>
                                    Tahun <?= htmlspecialchars($mobil['tahun']); ?>
                                </div>

                                <div class="mb-2">
                                    <i class="fas fa-box text-success mr-2"></i>
                                    Stok <?= htmlspecialchars($mobil['stok']); ?> Unit
                                </div>

                                <div class="mb-3">
                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                    Status <?= ucfirst(htmlspecialchars($mobil['status'])); ?>
                                </div>

                                <p class="text-gray-600 mb-0" style="line-height:1.7;">
                                    <?= nl2br(htmlspecialchars($mobil['deskripsi'])); ?>
                                </p>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7 mb-4">
                        <div class="card shadow border-0">

                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    Form Pemesanan
                                </h6>
                            </div>

                            <div class="card-body">

                                <form action="" method="POST" enctype="multipart/form-data">

                                    <div class="form-group">
                                        <label class="font-weight-bold">Nama Pembeli</label>
                                        <input type="text"
                                               class="form-control"
                                               value="<?= htmlspecialchars($pembeli['nama']); ?>"
                                               readonly>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">No HP</label>
                                        <input type="text"
                                               class="form-control"
                                               value="<?= htmlspecialchars($pembeli['no_hp']); ?>"
                                               readonly>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Alamat Pembeli</label>
                                        <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($pembeli['alamat']); ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Alamat Pengiriman</label>
                                        <textarea name="alamat_kirim"
                                                  class="form-control"
                                                  rows="3"
                                                  required
                                                  placeholder="Masukkan alamat pengiriman mobil"><?= htmlspecialchars($pembeli['alamat']); ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Total Harga</label>
                                        <input type="text"
                                               class="form-control font-weight-bold text-primary"
                                               value="<?= rupiah($mobil['harga']); ?>"
                                               readonly>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Metode Pembayaran</label>
                                        <select name="metode_bayar"
                                                id="metode_bayar"
                                                class="form-control"
                                                required
                                                onchange="cekMetodeBayar()">
                                            <option value="">-- Pilih Metode Pembayaran --</option>
                                            <option value="tunai">Tunai</option>
                                            <option value="transfer">Transfer</option>
                                        </select>
                                    </div>

                                    <div class="form-group" id="buktiTransferBox" style="display:none;">
                                        <label class="font-weight-bold">Upload Bukti Transfer</label>
                                        <input type="file"
                                               name="bukti_pembayaran"
                                               id="bukti_pembayaran"
                                               class="form-control"
                                               accept="image/*">

                                        <small class="text-muted">
                                            Format yang diperbolehkan: JPG, JPEG, PNG.
                                        </small>
                                    </div>

                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Setelah pemesanan dibuat, status akan menjadi <b>booking</b> dan menunggu proses admin.
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-2 mb-md-0">
                                            <a href="detail_mobil.php?id=<?= $mobil['id_mobil']; ?>"
                                               class="btn btn-outline-secondary btn-block">
                                                <i class="fas fa-arrow-left mr-1"></i>
                                                Batal
                                            </a>
                                        </div>

                                        <div class="col-md-6">
                                            <button type="submit"
                                                    name="pesan"
                                                    class="btn btn-primary btn-block"
                                                    onclick="return confirm('Yakin ingin memesan mobil ini?')">
                                                <i class="fas fa-shopping-cart mr-1"></i>
                                                Buat Pesanan
                                            </button>
                                        </div>
                                    </div>

                                </form>

                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>

        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; Galaxy Showroom <?= date('Y'); ?></span>
                </div>
            </div>
        </footer>

    </div>

</div>

<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../assets/js/sb-admin-2.min.js"></script>

<script>
function cekMetodeBayar() {
    let metode = document.getElementById("metode_bayar").value;
    let box = document.getElementById("buktiTransferBox");
    let bukti = document.getElementById("bukti_pembayaran");

    if (metode === "transfer") {
        box.style.display = "block";
        bukti.setAttribute("required", "required");
    } else {
        box.style.display = "none";
        bukti.removeAttribute("required");
        bukti.value = "";
    }
}
</script>

</body>
</html>
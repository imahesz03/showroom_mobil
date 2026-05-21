<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "pembeli") {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_POST['pesan'])) {
    header("Location: lihat_mobil.php");
    exit;
}

$id_user = $_SESSION['id_user'] ?? 0;
$id_mobil = mysqli_real_escape_string($koneksi, $_POST['id_mobil'] ?? '');
$metode_bayar = mysqli_real_escape_string($koneksi, $_POST['metode_bayar'] ?? '');

$booking_fee = 500000;

if (empty($id_mobil) || empty($metode_bayar)) {
    $_SESSION['error'] = "Data booking belum lengkap.";
    header("Location: pesan_mobil.php?id=$id_mobil");
    exit;
}

if (!in_array($metode_bayar, ['tunai', 'transfer'])) {
    $_SESSION['error'] = "Metode bayar tidak valid.";
    header("Location: pesan_mobil.php?id=$id_mobil");
    exit;
}

$qPembeli = mysqli_query($koneksi, "
    SELECT id_pembeli 
    FROM pembeli 
    WHERE id_user='$id_user' 
    LIMIT 1
");

if (!$qPembeli || mysqli_num_rows($qPembeli) == 0) {
    $_SESSION['error'] = "Data pembeli tidak ditemukan.";
    header("Location: ../auth/login.php");
    exit;
}

$pembeli = mysqli_fetch_assoc($qPembeli);
$id_pembeli = $pembeli['id_pembeli'];

$qMobil = mysqli_query($koneksi, "
    SELECT * FROM mobil 
    WHERE id_mobil='$id_mobil' 
    LIMIT 1
");

if (!$qMobil || mysqli_num_rows($qMobil) == 0) {
    $_SESSION['error'] = "Mobil tidak ditemukan.";
    header("Location: lihat_mobil.php");
    exit;
}

$mobil = mysqli_fetch_assoc($qMobil);

if ((int)$mobil['stok'] <= 0) {
    $_SESSION['error'] = "Stok mobil habis.";
    header("Location: lihat_mobil.php");
    exit;
}

$folderUpload = "../uploads/";

if (!is_dir($folderUpload)) {
    mkdir($folderUpload, 0777, true);
}

function uploadFile($field, $prefix, $folderUpload)
{
    if (empty($_FILES[$field]['name'])) {
        return "";
    }

    $nama = $_FILES[$field]['name'];
    $tmp = $_FILES[$field]['tmp_name'];
    $size = $_FILES[$field]['size'];

    $ext = strtolower(pathinfo($nama, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $allowed)) {
        return "FORMAT_SALAH";
    }

    if ($size > 2 * 1024 * 1024) {
        return "UKURAN_BESAR";
    }

    $namaBaru = $prefix . "_" . time() . "_" . rand(1000,9999) . "." . $ext;
    $path = $folderUpload . $namaBaru;

    if (!move_uploaded_file($tmp, $path)) {
        return "UPLOAD_GAGAL";
    }

    return $namaBaru;
}

$bukti_pembayaran = "-";

if ($metode_bayar == "transfer") {
    $uploadBukti = uploadFile("bukti_pembayaran", "bukti_booking", $folderUpload);

    if ($uploadBukti == "" || in_array($uploadBukti, ["FORMAT_SALAH", "UKURAN_BESAR", "UPLOAD_GAGAL"])) {
        $_SESSION['error'] = "Bukti transfer wajib diupload. Format JPG/PNG/WEBP maksimal 2MB.";
        header("Location: pesan_mobil.php?id=$id_mobil");
        exit;
    }

    $bukti_pembayaran = $uploadBukti;
}

$total_harga = (float)$mobil['harga'];
$deadline_dp = date('Y-m-d', strtotime('+7 days'));

mysqli_begin_transaction($koneksi);

$simpanPemesanan = mysqli_query($koneksi, "
    INSERT INTO pemesanan
    (
        id_pembeli,
        id_mobil,
        tanggal_pesan,
        total_harga,
        status,
        deadline_dp
    )
    VALUES
    (
        '$id_pembeli',
        '$id_mobil',
        NOW(),
        '$total_harga',
        'booking',
        '$deadline_dp'
    )
");

if (!$simpanPemesanan) {
    mysqli_rollback($koneksi);
    die("Gagal simpan pemesanan: " . mysqli_error($koneksi));
}

$id_pemesanan = mysqli_insert_id($koneksi);

$simpanPembayaran = mysqli_query($koneksi, "
    INSERT INTO pembayaran
    (
        id_pemesanan,
        metode_bayar,
        jumlah,
        status,
        bukti_pembayaran,
        jenis_pembayaran
    )
    VALUES
    (
        '$id_pemesanan',
        '$metode_bayar',
        '$booking_fee',
        'diterima',
        '$bukti_pembayaran',
        'booking'
    )
");

if (!$simpanPembayaran) {
    mysqli_rollback($koneksi);
    die("Gagal simpan pembayaran: " . mysqli_error($koneksi));
}

$updateMobil = mysqli_query($koneksi, "
    UPDATE mobil SET
        stok = stok - 1,
        status = CASE 
            WHEN stok - 1 <= 0 THEN 'terjual'
            ELSE 'tersedia'
        END
    WHERE id_mobil='$id_mobil'
");

if (!$updateMobil) {
    mysqli_rollback($koneksi);
    die("Gagal update stok mobil: " . mysqli_error($koneksi));
}

mysqli_commit($koneksi);

echo "<script>
    alert('Booking berhasil. Status pesanan kamu sekarang Booking.');
    window.location='pesanan_saya.php';
</script>";
exit;
?>
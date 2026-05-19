<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "pembeli") {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_POST['bayar'])) {
    header("Location: pesanan_saya.php");
    exit;
}

$id_user = $_SESSION['id_user'] ?? 0;

function tambahKolomJikaBelumAda($koneksi, $tabel, $kolom, $sql)
{
    $cek = mysqli_query($koneksi, "SHOW COLUMNS FROM `$tabel` LIKE '$kolom'");
    if ($cek && mysqli_num_rows($cek) == 0) {
        mysqli_query($koneksi, $sql);
    }
}

tambahKolomJikaBelumAda($koneksi, "pemesanan", "foto_ktp",
    "ALTER TABLE pemesanan ADD foto_ktp VARCHAR(255) DEFAULT NULL"
);

tambahKolomJikaBelumAda($koneksi, "pembayaran", "jenis_pembayaran",
    "ALTER TABLE pembayaran ADD jenis_pembayaran VARCHAR(50) DEFAULT 'booking'"
);

$id_pemesanan = mysqli_real_escape_string($koneksi, $_POST['id_pemesanan'] ?? '');
$jenis_pembayaran = mysqli_real_escape_string($koneksi, $_POST['jenis_pembayaran'] ?? '');
$metode_bayar = mysqli_real_escape_string($koneksi, $_POST['metode_bayar'] ?? '');
$jumlah = (float) ($_POST['jumlah'] ?? 0);

if (empty($id_pemesanan) || empty($jenis_pembayaran) || empty($metode_bayar) || $jumlah < 0) {
    $_SESSION['error'] = "Data pembayaran belum lengkap.";
    header("Location: pesanan_saya.php");
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

$qPesanan = mysqli_query($koneksi, "
    SELECT *
    FROM pemesanan
    WHERE id_pemesanan='$id_pemesanan'
    AND id_pembeli='$id_pembeli'
    LIMIT 1
");

if (!$qPesanan || mysqli_num_rows($qPesanan) == 0) {
    $_SESSION['error'] = "Pesanan tidak ditemukan.";
    header("Location: pesanan_saya.php");
    exit;
}

$pesanan = mysqli_fetch_assoc($qPesanan);

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
    $allowed = ['jpg','jpeg','png','webp'];

    if (!in_array($ext, $allowed)) return "FORMAT_SALAH";
    if ($size > 2 * 1024 * 1024) return "UKURAN_BESAR";

    $namaBaru = $prefix . "_" . time() . "_" . rand(1000,9999) . "." . $ext;
    $path = $folderUpload . $namaBaru;

    if (!move_uploaded_file($tmp, $path)) return "UPLOAD_GAGAL";

    return $namaBaru;
}

$bukti_pembayaran = "-";

if ($metode_bayar == "transfer") {
    $uploadBukti = uploadFile("bukti_pembayaran", "bukti_bayar", $folderUpload);

    if ($uploadBukti == "" || in_array($uploadBukti, ["FORMAT_SALAH", "UKURAN_BESAR", "UPLOAD_GAGAL"])) {
        $_SESSION['error'] = "Bukti transfer wajib diupload.";
        header("Location: pembayaran.php?id=$id_pemesanan&jenis=$jenis_pembayaran");
        exit;
    }

    $bukti_pembayaran = $uploadBukti;
}

$uploadKtp = uploadFile("foto_ktp", "ktp", $folderUpload);

if ($uploadKtp == "" || in_array($uploadKtp, ["FORMAT_SALAH", "UKURAN_BESAR", "UPLOAD_GAGAL"])) {
    $_SESSION['error'] = "KTP wajib diupload.";
    header("Location: pembayaran.php?id=$id_pemesanan&jenis=$jenis_pembayaran");
    exit;
}

if ($jenis_pembayaran == "dp") {
    $statusBaru = "dp";
} elseif ($jenis_pembayaran == "pelunasan") {
    $statusBaru = "lunas";
} else {
    $_SESSION['error'] = "Jenis pembayaran tidak valid.";
    header("Location: pesanan_saya.php");
    exit;
}

mysqli_begin_transaction($koneksi);

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
        '$jumlah',
        'diterima',
        '$bukti_pembayaran',
        '$jenis_pembayaran'
    )
");

$updatePesanan = mysqli_query($koneksi, "
    UPDATE pemesanan SET
        status='$statusBaru',
        foto_ktp='$uploadKtp'
    WHERE id_pemesanan='$id_pemesanan'
    AND id_pembeli='$id_pembeli'
");

if ($simpanPembayaran && $updatePesanan) {
    mysqli_commit($koneksi);

    echo "<script>
        alert('Pembayaran berhasil disimpan.');
        window.location='pesanan_saya.php';
    </script>";
    exit;
} else {
    mysqli_rollback($koneksi);

    echo "<script>
        alert('Gagal menyimpan pembayaran!');
        window.location='pembayaran.php?id=$id_pemesanan&jenis=$jenis_pembayaran';
    </script>";
    exit;
}
?>
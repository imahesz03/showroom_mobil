<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "pembeli") {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['id_user'] ?? 0;
$id_pemesanan = $_GET['id'] ?? '';

if (empty($id_pemesanan)) {
    echo "<script>
        alert('ID pesanan tidak ditemukan!');
        window.location='pesanan_saya.php';
    </script>";
    exit;
}

/*
|------------------------------------------------------
| AMBIL DATA PEMBELI LOGIN
|------------------------------------------------------
*/
$qPembeli = mysqli_query($koneksi, "
    SELECT id_pembeli
    FROM pembeli
    WHERE id_user = '$id_user'
    LIMIT 1
");

if (!$qPembeli) {
    die("Query pembeli error: " . mysqli_error($koneksi));
}

if (mysqli_num_rows($qPembeli) == 0) {
    echo "<script>
        alert('Data pembeli tidak ditemukan!');
        window.location='../auth/login.php';
    </script>";
    exit;
}

$pembeli = mysqli_fetch_assoc($qPembeli);
$id_pembeli = $pembeli['id_pembeli'];

/*
|------------------------------------------------------
| CEK PESANAN MILIK PEMBELI
|------------------------------------------------------
*/
$qPesanan = mysqli_query($koneksi, "
    SELECT 
        id_pemesanan,
        id_mobil,
        status
    FROM pemesanan
    WHERE id_pemesanan = '$id_pemesanan'
    AND id_pembeli = '$id_pembeli'
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

if ($pesanan['status'] != "booking") {
    echo "<script>
        alert('Pesanan hanya bisa dibatalkan jika status masih booking!');
        window.location='pesanan_saya.php';
    </script>";
    exit;
}

$id_mobil = $pesanan['id_mobil'];

/*
|------------------------------------------------------
| PROSES BATAL
|------------------------------------------------------
*/
mysqli_begin_transaction($koneksi);

$updatePesanan = mysqli_query($koneksi, "
    UPDATE pemesanan SET
        status = 'batal'
    WHERE id_pemesanan = '$id_pemesanan'
    AND id_pembeli = '$id_pembeli'
");

$updateMobil = mysqli_query($koneksi, "
    UPDATE mobil SET
        stok = stok + 1,
        status = 'tersedia'
    WHERE id_mobil = '$id_mobil'
");

$updatePembayaran = mysqli_query($koneksi, "
    UPDATE pembayaran SET
        status = 'ditolak'
    WHERE id_pemesanan = '$id_pemesanan'
");

if ($updatePesanan && $updateMobil && $updatePembayaran) {
    mysqli_commit($koneksi);

    echo "<script>
        alert('Pesanan berhasil dibatalkan. Stok mobil dikembalikan dan status mobil menjadi tersedia.');
        window.location='pesanan_saya.php';
    </script>";
    exit;
} else {
    mysqli_rollback($koneksi);

    echo "<script>
        alert('Gagal membatalkan pesanan!');
        window.location='pesanan_saya.php';
    </script>";
    exit;
}
?>
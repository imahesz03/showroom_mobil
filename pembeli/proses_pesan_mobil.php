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

$id_user      = $_SESSION['id_user'] ?? 0;
$id_mobil     = mysqli_real_escape_string($koneksi, $_POST['id_mobil']);
$metode_bayar = mysqli_real_escape_string($koneksi, $_POST['metode_bayar']);
$total_harga  = mysqli_real_escape_string($koneksi, $_POST['total_harga']);
$tanggal      = date('Y-m-d H:i:s');

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
        alert('Mobil sudah tidak tersedia!');
        window.location='lihat_mobil.php';
    </script>";
    exit;
}

if ($metode_bayar != "cash" && $metode_bayar != "transfer") {
    echo "<script>
        alert('Metode pembayaran tidak valid!');
        window.location='pesan_mobil.php?id=$id_mobil';
    </script>";
    exit;
}

$bukti_transfer = "-";

if ($metode_bayar == "transfer") {

    if (!isset($_FILES['bukti_transfer']) || $_FILES['bukti_transfer']['name'] == "") {
        echo "<script>
            alert('Bukti transfer wajib diupload!');
            window.location='pesan_mobil.php?id=$id_mobil';
        </script>";
        exit;
    }

    $nama_file = $_FILES['bukti_transfer']['name'];
    $tmp_file  = $_FILES['bukti_transfer']['tmp_name'];
    $ukuran    = $_FILES['bukti_transfer']['size'];
    $ext       = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

    $allowed = ['jpg', 'jpeg', 'png'];

    if (!in_array($ext, $allowed)) {
        echo "<script>
            alert('Format bukti transfer harus JPG, JPEG, atau PNG!');
            window.location='pesan_mobil.php?id=$id_mobil';
        </script>";
        exit;
    }

    if ($ukuran > 2000000) {
        echo "<script>
            alert('Ukuran bukti transfer maksimal 2MB!');
            window.location='pesan_mobil.php?id=$id_mobil';
        </script>";
        exit;
    }

    $bukti_transfer = time() . "_bukti_" . rand(1000, 9999) . "." . $ext;
    $upload_path = "../uploads/" . $bukti_transfer;

    if (!move_uploaded_file($tmp_file, $upload_path)) {
        echo "<script>
            alert('Gagal upload bukti transfer!');
            window.location='pesan_mobil.php?id=$id_mobil';
        </script>";
        exit;
    }
}

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

    $status_pembayaran = ($metode_bayar == "cash") ? "pending" : "pending";

    $insertPembayaran = mysqli_query($koneksi, "
        INSERT INTO pembayaran
        (id_pemesanan, metode_bayar, jumlah, status, bukti_transfer)
        VALUES
        ('$id_pemesanan', '$metode_bayar', '$total_harga', '$status_pembayaran', '$bukti_transfer')
    ");

    if (!$insertPembayaran) {
        throw new Exception(mysqli_error($koneksi));
    }

    $stok_baru = $mobil['stok'] - 1;
    $status_mobil = ($stok_baru <= 0) ? "terjual" : "tersedia";

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
        alert('Pemesanan berhasil dibuat!');
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
?>
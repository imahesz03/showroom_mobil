<?php

session_start();
include "../config/koneksi.php";

/*
|--------------------------------------------------------------------------
| CEK LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['role']) || $_SESSION['role'] != "pembeli") {

    header("Location: ../auth/login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| CEK ID MOBIL
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id'])) {

    header("Location: lihat_mobil.php");
    exit;
}

$id_mobil = $_GET['id'];


/*
|--------------------------------------------------------------------------
| AMBIL DATA MOBIL
|--------------------------------------------------------------------------
*/

$queryMobil = mysqli_query($koneksi, "
    SELECT * FROM mobil
    WHERE id_mobil='$id_mobil'
");

$dataMobil = mysqli_fetch_assoc($queryMobil);


/*
|--------------------------------------------------------------------------
| JIKA MOBIL TIDAK ADA
|--------------------------------------------------------------------------
*/

if (!$dataMobil) {

    echo "
    <script>
        alert('Mobil tidak ditemukan!');
        window.location='lihat_mobil.php';
    </script>
    ";

    exit;
}


/*
|--------------------------------------------------------------------------
| AMBIL DATA PEMBELI
|--------------------------------------------------------------------------
*/

$id_user = $_SESSION['id_user'];

$queryPembeli = mysqli_query($koneksi, "
    SELECT * FROM pembeli
    WHERE id_user='$id_user'
");

$dataPembeli = mysqli_fetch_assoc($queryPembeli);

$id_pembeli = $dataPembeli['id_pembeli'];


/*
|--------------------------------------------------------------------------
| PROSES PESAN
|--------------------------------------------------------------------------
*/

if (isset($_POST['pesan'])) {

    $total_harga = $dataMobil['harga'];

    mysqli_query($koneksi, "
        INSERT INTO pemesanan (
            id_pembeli,
            id_mobil,
            tanggal_pesan,
            total_harga,
            status
        ) VALUES (
            '$id_pembeli',
            '$id_mobil',
            NOW(),
            '$total_harga',
            'booking'
        )
    ");

    header("Location: pesanan_saya.php?pesan=success");
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Pesan Mobil</title>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet"
        href="../assets/style.css">

</head>

<body>

    <div class="container">

        <h2>Pesan Mobil</h2>

        <form method="POST">

            <!-- NAMA MOBIL -->
            <div class="input-group">

                <label>Nama Mobil</label>

                <input type="text"
                    value="<?= $dataMobil['nama_mobil']; ?>"
                    readonly>

            </div>


            <!-- HARGA -->
            <div class="input-group">

                <label>Harga Mobil</label>

                <input type="text"
                    value="Rp <?= number_format($dataMobil['harga']); ?>"
                    readonly>

            </div>


            <!-- STATUS -->
            <div class="input-group">

                <label>Status Pesanan</label>

                <input type="text"
                    value="Masuk Keranjang / Booking"
                    readonly>

            </div>


            <!-- BUTTON -->
            <div style="
                display:flex;
                gap:15px;
                margin-top:20px;
            ">

                <a href="lihat_mobil.php"
                    style="
                    flex:1;
                    text-align:center;
                    text-decoration:none;
                    padding:14px;
                    border-radius:14px;
                    background:#374151;
                    color:white;
                    font-weight:600;
                ">

                    Kembali

                </a>


                <button type="submit"
                    name="pesan"
                    class="btn"
                    style="flex:1;">

                    Tambah ke Pesanan

                </button>

            </div>

        </form>

    </div>

</body>

</html>
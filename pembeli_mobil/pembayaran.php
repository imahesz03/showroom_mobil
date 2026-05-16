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
| CEK ID PEMESANAN
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id'])) {

    header("Location: pesanan_saya.php");
    exit;
}

$id_pemesanan = $_GET['id'];


/*
|--------------------------------------------------------------------------
| AMBIL DATA PESANAN
|--------------------------------------------------------------------------
*/

$query = mysqli_query($koneksi, "
    SELECT pemesanan.*, mobil.nama_mobil, mobil.harga
    FROM pemesanan
    JOIN mobil
    ON pemesanan.id_mobil = mobil.id_mobil
    WHERE pemesanan.id_pemesanan = '$id_pemesanan'
");

$data = mysqli_fetch_assoc($query);


/*
|--------------------------------------------------------------------------
| JIKA DATA TIDAK ADA
|--------------------------------------------------------------------------
*/

if (!$data) {

    echo "
    <script>
        alert('Data pesanan tidak ditemukan!');
        window.location='pesanan_saya.php';
    </script>
    ";

    exit;
}


/*
|--------------------------------------------------------------------------
| PROSES BAYAR
|--------------------------------------------------------------------------
*/

if (isset($_POST['bayar'])) {

    $metode = $_POST['metode'];

    $bukti = "";


    /*
    |--------------------------------------------------------------------------
    | JIKA TRANSFER
    |--------------------------------------------------------------------------
    */

    if ($metode == "Transfer") {

        if ($_FILES['bukti']['name'] != "") {

            $bukti = time() . '_' . $_FILES['bukti']['name'];

            $tmp = $_FILES['bukti']['tmp_name'];

            move_uploaded_file(
                $tmp,
                "../uploads/" . $bukti
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN PEMBAYARAN
    |--------------------------------------------------------------------------
    */

    mysqli_query($koneksi, "
        INSERT INTO pembayaran (
            id_pemesanan,
            metode_bayar,
            bukti_pembayaran,
            status
        ) VALUES (
            '$id_pemesanan',
            '$metode',
            '$bukti',
            'Sudah Dibayar'
        )
    ");


    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS PESANAN
    |--------------------------------------------------------------------------
    */

    mysqli_query($koneksi, "
        UPDATE pemesanan
        SET status='lunas'
        WHERE id_pemesanan='$id_pemesanan'
    ");


    /*
    |--------------------------------------------------------------------------
    | KURANGI STOK MOBIL
    |--------------------------------------------------------------------------
    */

    $queryMobil = mysqli_query($koneksi, "
        SELECT * FROM mobil
        WHERE id_mobil='".$data['id_mobil']."'
    ");

    $dataMobil = mysqli_fetch_assoc($queryMobil);

    $stok_sekarang = $dataMobil['stok'];

    $stok_baru = $stok_sekarang - 1;


    /*
    |--------------------------------------------------------------------------
    | JIKA STOK HABIS
    |--------------------------------------------------------------------------
    */

    if ($stok_baru <= 0) {

        mysqli_query($koneksi, "
            UPDATE mobil
            SET 
                stok='0',
                status='terjual'
            WHERE id_mobil='".$data['id_mobil']."'
        ");

    } else {

        /*
        |--------------------------------------------------------------------------
        | JIKA STOK MASIH ADA
        |--------------------------------------------------------------------------
        */

        mysqli_query($koneksi, "
            UPDATE mobil
            SET stok='$stok_baru'
            WHERE id_mobil='".$data['id_mobil']."'
        ");

    }


    /*
    |--------------------------------------------------------------------------
    | REDIRECT
    |--------------------------------------------------------------------------
    */

    header("Location: pesanan_saya.php?bayar=success");
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Pembayaran</title>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet"
        href="../assets/style.css">

</head>

<body>

<div class="container">

    <h2>Pembayaran</h2>

    <form method="POST" enctype="multipart/form-data">

        <!-- MOBIL -->
        <div class="input-group">

            <label>Nama Mobil</label>

            <input type="text"
                value="<?= $data['nama_mobil']; ?>"
                readonly>

        </div>


        <!-- TOTAL -->
        <div class="input-group">

            <label>Total Pembayaran</label>

            <input type="text"
                value="Rp <?= number_format($data['harga']); ?>"
                readonly>

        </div>


        <!-- METODE -->
        <div class="input-group">

            <label>Metode Pembayaran</label>

            <select name="metode"
                id="metode"
                required>

                <option value="">
                    -- Pilih Metode --
                </option>

                <option value="Cash">
                    Cash
                </option>

                <option value="Transfer">
                    Transfer
                </option>

            </select>

        </div>


        <!-- REKENING -->
        <div class="input-group"
            id="rekeningBox"
            style="display:none;">

            <label>No Rekening Admin</label>

            <input type="text"
                value="BCA - 1234567890 a/n SHOWROOM MOBIL"
                readonly>

        </div>


        <!-- BUKTI -->
        <div class="input-group"
            id="buktiBox"
            style="display:none;">

            <label>Upload Bukti Pembayaran</label>

            <input type="file"
                name="bukti">

        </div>


        <!-- BUTTON -->
        <div style="
            display:flex;
            gap:15px;
            margin-top:20px;
        ">

            <a href="pesanan_saya.php"
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
                name="bayar"
                class="btn"
                style="flex:1;">

                Bayar Sekarang

            </button>

        </div>

    </form>

</div>


<script>

let metode = document.getElementById("metode");

let rekeningBox = document.getElementById("rekeningBox");

let buktiBox = document.getElementById("buktiBox");

metode.addEventListener("change", function(){

    if(this.value == "Transfer"){

        rekeningBox.style.display = "block";

        buktiBox.style.display = "block";

        document.querySelector("input[name='bukti']").required = true;

    }else{

        rekeningBox.style.display = "none";

        buktiBox.style.display = "none";

        document.querySelector("input[name='bukti']").required = false;

    }

});

</script>

</body>
</html>
<?php

session_start();
include "../config/koneksi.php";

/* ================= VALIDASI LOGIN ================= */

if(!isset($_SESSION['role'])){

    header("Location: ../auth/login.php");
    exit;

}

if($_SESSION['role'] != "pembeli"){

    header("Location: ../auth/login.php");
    exit;

}

/* ================= SEARCH ================= */

$keyword = "";

if(isset($_GET['keyword'])){

    $keyword = $_GET['keyword'];

    $query = mysqli_query($koneksi,
    "SELECT * FROM mobil
    WHERE status='tersedia'
    AND nama_mobil LIKE '%$keyword%'");

}else{

    $query = mysqli_query($koneksi,
    "SELECT * FROM mobil
    WHERE status='tersedia'");

}

/* ================= CEK QUERY ================= */

if(!$query){

    die("Query Error : " . mysqli_error($koneksi));

}

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <title>Lihat Mobil</title>

    <meta charset="UTF-8">

    <meta name="viewport"
    content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet"
    href="../assets/style.css">

    <style>

        .mobil-image{
            width:100%;
            height:220px;
            object-fit:cover;
            border-radius:18px;
            margin-bottom:18px;
            border:1px solid rgba(99,102,241,0.2);
        }

        .mobil-info{
            margin-bottom:12px;
            color:#cbd5e1;
            line-height:1.7;
        }

        .mobil-info b{
            color:white;
        }

        .deskripsi{
            color:#94a3b8;
            line-height:1.8;
            margin-bottom:20px;
        }

        .kosong-box{
            background:rgba(17,24,39,0.92);
            padding:45px;
            border-radius:24px;
            text-align:center;
            border:1px solid rgba(99,102,241,0.2);
            box-shadow:
            0 0 20px rgba(99,102,241,0.15);
        }

        .kosong-box h3{
            margin-bottom:12px;
            color:white;
            font-size:28px;
        }

        .kosong-box p{
            color:#94a3b8;
            line-height:1.8;
        }

    </style>

</head>
<body>

<div class="sidebar-layout">

    <!-- SIDEBAR -->
    <?php include '../includes/sidebar_pembeli.php'; ?>


    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- TOPBAR -->
        <div class="topbar">

            <div>

                <h2>Daftar Mobil</h2>

                <p>

                    Halo,
                    <b><?= $_SESSION['username']; ?></b>

                </p>

            </div>

        </div>


        <!-- SEARCH BAR -->
        <div class="search-bar">

            <form method="GET">

                <input 
                    type="text"
                    name="keyword"
                    placeholder="Cari nama mobil..."
                    value="<?= $keyword; ?>"
                >

                <button type="submit">

                    Cari

                </button>

            </form>

        </div>


        <!-- CONTENT -->
        <div class="content">

            <?php if(mysqli_num_rows($query) > 0){ ?>

                <div class="menu-cards">

                    <?php while($data = mysqli_fetch_assoc($query)){ ?>

                    <div class="menu-card">

                        <!-- FOTO MOBIL -->
                        <?php if(!empty($data['foto'])){ ?>

                            <img 
                            src="../uploads/<?= $data['foto']; ?>"
                            alt="<?= $data['nama_mobil']; ?>"
                            class="mobil-image">

                        <?php } ?>


                        <!-- NAMA MOBIL -->
                        <h3>

                            <?= $data['nama_mobil']; ?>

                        </h3>

                        <!-- TAHUN -->
                        <p class="mobil-info">

                            Tahun :
                            <b>

                                <?= $data['tahun']; ?>

                            </b>

                        </p>


                        <!-- HARGA -->
                        <p class="mobil-info">

                            Harga :
                            <b>

                                Rp <?= number_format($data['harga']); ?>

                            </b>

                        </p>

                        <!-- BUTTON DETAIL -->
                        <a href="detail_mobil.php?id=<?= $data['id_mobil']; ?>">

                            Lihat Detail

                        </a>

                    </div>

                    <?php } ?>

                </div>

            <?php } else { ?>

                <!-- DATA KOSONG -->
                <div class="kosong-box">

                    <h3>

                        Mobil Tidak Tersedia

                    </h3>

                    <p>

                        Saat ini stok mobil sedang kosong
                        atau mobil yang dicari tidak ditemukan.

                    </p>

                </div>

            <?php } ?>

        </div>

    </div>

</div>

</body>
</html>
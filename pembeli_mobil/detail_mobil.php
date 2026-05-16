<?php

session_start();
include "../config/koneksi.php";

$id = $_GET['id'];

$query = mysqli_query($koneksi,
"SELECT * FROM mobil
WHERE id_mobil='$id'");

$data = mysqli_fetch_assoc($query);

?>

<!DOCTYPE html>
<html>
<head>

    <title>Detail Mobil</title>

    <link rel="stylesheet"
    href="../assets/style.css">

</head>
<body>

<div class="sidebar-layout">

    <?php include '../includes/sidebar_pembeli.php'; ?>

    <div class="main-content">

        <div class="topbar">

            <h2>Detail Mobil</h2>

        </div>

        <div class="content">

            <div class="menu-card">
                                        <!-- FOTO MOBIL -->
                        <?php if(!empty($data['foto'])){ ?>

                            <img 
                            src="../uploads/<?= $data['foto']; ?>"
                            alt="<?= $data['nama_mobil']; ?>"
                            class="mobil-image">

                        <?php } ?>

                <h1 style="margin-bottom:20px;">

                    <?= $data['nama_mobil']; ?>

                </h1>

                                <p>

                    Deskripsi
                    <b>

                        <?= $data['deskripsi']; ?>

                    </b>

                </p>

                        <p class="mobil-info">

                            Tahun :
                            <b>

                                <?= $data['tahun']; ?>

                            </b>

                        </p>

                <p>

                    Harga:
                    <b>

                        Rp <?= number_format($data['harga']); ?>

                    </b>

                </p>

                <p>

                    Stok:
                    <b>

                        <?= $data['stok']; ?>

                    </b>

                </p>

                <p>

                    Status:
                    <b>

                        <?= $data['status']; ?>

                    </b>

                </p>

                <br>

                <div style="
                display:flex;
                gap:15px;
                ">

                    <a href="lihat_mobil.php">

                        Kembali

                    </a>

                    <a href="pesan_mobil.php?id=<?= $data['id_mobil']; ?>">

                        Pesan Mobil

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>
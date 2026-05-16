<?php

session_start();
include "../config/koneksi.php";

if($_SESSION['role'] != "pembeli"){
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['id_user'];


/*
|--------------------------------------------------------------------------
| AMBIL DATA PEMBELI
|--------------------------------------------------------------------------
*/

$queryPembeli = mysqli_query($koneksi,
"SELECT * FROM pembeli
WHERE id_user='$id_user'");

$dataPembeli = mysqli_fetch_assoc($queryPembeli);

$id_pembeli = $dataPembeli['id_pembeli'];


/*
|--------------------------------------------------------------------------
| AMBIL DATA PESANAN
|--------------------------------------------------------------------------
*/

$query = mysqli_query($koneksi,
"SELECT pemesanan.*, mobil.nama_mobil

FROM pemesanan

JOIN mobil
ON pemesanan.id_mobil = mobil.id_mobil

WHERE pemesanan.id_pembeli='$id_pembeli'

ORDER BY pemesanan.id_pemesanan DESC");

?>

<!DOCTYPE html>
<html>
<head>

    <title>Pesanan Saya</title>

    <meta charset="UTF-8">

    <meta name="viewport"
    content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet"
    href="../assets/style.css">

</head>
<body>

<div class="sidebar-layout">

    <!-- SIDEBAR -->
    <?php include "../includes/sidebar_pembeli.php"; ?>


    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- TOPBAR -->
        <div class="topbar">

            <h2>Pesanan Saya</h2>

            <p>
                Halo,
                <b><?= $_SESSION['username']; ?></b>
            </p>

        </div>


        <!-- CONTENT -->
        <div class="content">

            <!-- NOTIF -->
            <?php if(isset($_GET['pesan'])){ ?>

                <div class="alert-success">

                    Pesanan berhasil dibuat!

                </div>

            <?php } ?>


            <?php if(isset($_GET['bayar'])){ ?>

                <div class="alert-success">

                    Pembayaran berhasil dilakukan!

                </div>

            <?php } ?>


            <!-- TABLE -->
            <table>

                <tr>

                    <th>No</th>
                    <th>Mobil</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>

                </tr>

                <?php
                $no = 1;

                while($data = mysqli_fetch_assoc($query)){
                ?>

                <tr>

                    <!-- NO -->
                    <td>
                        <?= $no++; ?>
                    </td>


                    <!-- MOBIL -->
                    <td>
                        <?= $data['nama_mobil']; ?>
                    </td>


                    <!-- TANGGAL -->
                    <td>
                        <?= $data['tanggal_pesan']; ?>
                    </td>


                    <!-- TOTAL -->
                    <td>
                        Rp <?= number_format($data['total_harga']); ?>
                    </td>


                    <!-- STATUS -->
                    <td>

                        <?php

                        if($data['status'] == "booking"){

                            echo "
                            <span style='
                            background:orange;
                            color:white;
                            padding:8px 14px;
                            border-radius:10px;
                            font-size:13px;
                            font-weight:600;
                            '>

                            Booking

                            </span>
                            ";

                        }

                        elseif($data['status'] == "dp"){

                            echo "
                            <span style='
                            background:#3b82f6;
                            color:white;
                            padding:8px 14px;
                            border-radius:10px;
                            font-size:13px;
                            font-weight:600;
                            '>

                            DP

                            </span>
                            ";

                        }

                        elseif($data['status'] == "lunas"){

                            echo "
                            <span style='
                            background:#10b981;
                            color:white;
                            padding:8px 14px;
                            border-radius:10px;
                            font-size:13px;
                            font-weight:600;
                            '>

                            Lunas

                            </span>
                            ";

                        }

                        elseif($data['status'] == "batal"){

                            echo "
                            <span style='
                            background:#ef4444;
                            color:white;
                            padding:8px 14px;
                            border-radius:10px;
                            font-size:13px;
                            font-weight:600;
                            '>

                            Batal

                            </span>
                            ";

                        }

                        ?>

                    </td>


                    <!-- AKSI -->
                    <td>

                        <?php if($data['status'] == "booking"){ ?>

                            <a href="pembayaran.php?id=<?= $data['id_pemesanan']; ?>"
                            class="btn"
                            style="
                            padding:10px 18px;
                            font-size:14px;
                            text-decoration:none;
                            display:inline-block;
                            width:auto;
                            ">

                                Bayar

                            </a>

                        <?php }else{ ?>

                            <span style="
                            color:lime;
                            font-weight:600;
                            ">

                                Sudah Dibayar

                            </span>

                        <?php } ?>

                    </td>

                </tr>

                <?php } ?>

            </table>

        </div>

    </div>

</div>

</body>
</html>
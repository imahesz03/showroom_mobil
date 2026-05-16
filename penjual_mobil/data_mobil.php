<?php

session_start();
include "../config/koneksi.php";

if($_SESSION['role'] != "penjual"){
    header("Location: ../auth/login.php");
}

$id_user = $_SESSION['id_user'];

$queryPenjual = mysqli_query($koneksi,
"SELECT * FROM penjual
WHERE id_user='$id_user'");

$dataPenjual = mysqli_fetch_assoc($queryPenjual);

$id_penjual = $dataPenjual['id_penjual'];

$query = mysqli_query($koneksi,
"SELECT * FROM mobil
WHERE id_penjual='$id_penjual'");

?>

<!DOCTYPE html>
<html>
<head>

    <title>Data Mobil Saya</title>

    <meta charset="UTF-8">

    <meta name="viewport"
    content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet"
    href="../assets/style.css">

</head>
<body>

<div class="sidebar-layout">

    <!-- SIDEBAR -->
    <?php include '../includes/sidebar_penjual.php'; ?>


    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- TOPBAR -->
        <div class="topbar">

            <h2>Data Mobil Saya</h2>

            <p>

                Halo,
                <b><?= $_SESSION['username']; ?></b>

            </p>

        </div>


        <!-- CONTENT -->
        <div class="content">

            <!-- HEADER -->
            <div class="menu-card">

                <div style="
                display:flex;
                justify-content:space-between;
                align-items:center;
                margin-bottom:20px;
                flex-wrap:wrap;
                gap:15px;
                ">

                    <div>

                        <h2 style="margin-bottom:5px;">

                            Daftar Mobil

                        </h2>

                        <p style="color:#6b7280;">

                            Kelola seluruh mobil yang anda jual.

                        </p>

                    </div>

                    <a href="tambah_mobil.php"
                    class="btn"
                    style="
                    width:auto;
                    padding:12px 20px;
                    text-decoration:none;
                    ">

                        + Tambah Mobil

                    </a>

                </div>


                <!-- TABLE -->
                <div style="overflow-x:auto;">

                    <table>

                        <tr>

                            <th>No</th>
                            <th>Nama Mobil</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th>Aksi</th>

                        </tr>

                        <?php
                        $no = 1;

                        while($data = mysqli_fetch_assoc($query)){
                        ?>

                        <tr>

                            <td>
                                <?= $no++; ?>
                            </td>

                            <td>
                                <?= $data['nama_mobil']; ?>
                            </td>

                            <td>
                                Rp <?= number_format($data['harga']); ?>
                            </td>

                            <td>
                                <?= $data['stok']; ?>
                            </td>

                            <td>

                                <?php if($data['status']=="tersedia"){ ?>

                                    <span style="
                                    background:#dcfce7;
                                    color:#16a34a;
                                    padding:8px 12px;
                                    border-radius:10px;
                                    font-size:13px;
                                    font-weight:600;
                                    ">

                                        Tersedia

                                    </span>

                                <?php }else{ ?>

                                    <span style="
                                    background:#fee2e2;
                                    color:#dc2626;
                                    padding:8px 12px;
                                    border-radius:10px;
                                    font-size:13px;
                                    font-weight:600;
                                    ">

                                        Tidak Tersedia

                                    </span>

                                <?php } ?>

                            </td>

                            <td>

                                <div style="
                                display:flex;
                                gap:10px;
                                flex-wrap:wrap;
                                ">

                                    <a href="edit_mobil.php?id=<?= $data['id_mobil']; ?>"
                                    style="
                                    text-decoration:none;
                                    background:#3b82f6;
                                    color:white;
                                    padding:8px 14px;
                                    border-radius:10px;
                                    font-size:13px;
                                    font-weight:600;
                                    ">

                                        Edit

                                    </a>

                                    <a href="hapus_mobil.php?id=<?= $data['id_mobil']; ?>"
                                    onclick="return confirm('Yakin hapus mobil ini?')"
                                    style="
                                    text-decoration:none;
                                    background:#ef4444;
                                    color:white;
                                    padding:8px 14px;
                                    border-radius:10px;
                                    font-size:13px;
                                    font-weight:600;
                                    ">

                                        Hapus

                                    </a>

                                </div>

                            </td>

                        </tr>

                        <?php } ?>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>
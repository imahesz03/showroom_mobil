<?php
include "../config/koneksi.php";

$id = $_GET['id'];

$data = mysqli_fetch_assoc(mysqli_query($koneksi,
"SELECT p.*, m.nama_mobil, b.nama, b.no_hp, b.alamat
FROM pemesanan p
JOIN mobil m ON p.id_mobil = m.id_mobil
JOIN pembeli b ON p.id_pembeli = b.id_pembeli
WHERE p.id_pemesanan='$id'"));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kwitansi Pembelian</title>

    <style>

        body{
            font-family: Arial, sans-serif;
            background:#f3f4f6;
            padding:40px;
        }

        .kwitansi{
            max-width:700px;
            margin:auto;
            background:white;
            padding:30px;
            border-radius:16px;
            box-shadow:0 10px 30px rgba(0,0,0,0.1);
        }

        .header{
            text-align:center;
            border-bottom:2px solid #e5e7eb;
            padding-bottom:15px;
            margin-bottom:20px;
        }

        .header h1{
            margin:0;
            color:#111827;
        }

        .header p{
            margin:5px 0;
            color:#6b7280;
            font-size:14px;
        }

        .info{
            margin-bottom:20px;
        }

        .info p{
            margin:6px 0;
            color:#111827;
        }

        .box{
            background:#f9fafb;
            padding:15px;
            border-radius:10px;
            margin-bottom:15px;
        }

        .row{
            display:flex;
            justify-content:space-between;
            padding:8px 0;
            border-bottom:1px dashed #e5e7eb;
        }

        .total{
            font-size:20px;
            font-weight:bold;
            color:#111827;
            text-align:right;
            margin-top:10px;
        }

        .status{
            display:inline-block;
            padding:6px 12px;
            border-radius:8px;
            background:#22c55e;
            color:white;
            font-size:13px;
        }

        .btn-print{
            margin-top:20px;
            display:block;
            text-align:center;
        }

        .btn-print button{
            padding:10px 18px;
            border:none;
            background:#6366f1;
            color:white;
            border-radius:10px;
            cursor:pointer;
            font-size:14px;
        }

        .btn-print button:hover{
            background:#4f46e5;
        }

        @media print{
            .btn-print{display:none;}
            body{background:white;}
            .kwitansi{box-shadow:none;}
        }

    </style>
</head>

<body>

<div class="kwitansi">

    <!-- HEADER -->
    <div class="header">
        <h1>KWITANSI PEMBELIAN MOBIL</h1>
        <p>Showroom Mobil - Sistem Admin</p>
    </div>

    <!-- INFO PEMBELI -->
    <div class="box">
        <div class="row">
            <span>Nama Pembeli</span>
            <span><?= $data['nama']; ?></span>
        </div>

        <div class="row">
            <span>No HP</span>
            <span><?= $data['no_hp']; ?></span>
        </div>

        <div class="row">
            <span>Alamat</span>
            <span><?= $data['alamat']; ?></span>
        </div>
    </div>

    <!-- DETAIL TRANSAKSI -->
    <div class="box">
        <div class="row">
            <span>Mobil</span>
            <span><?= $data['nama_mobil']; ?></span>
        </div>

        <div class="row">
            <span>Tanggal Pemesanan</span>
            <span><?= $data['tanggal_pesan']; ?></span>
        </div>

        <div class="row">
            <span>Status</span>
            <span class="status"><?= $data['status']; ?></span>
        </div>
    </div>

    <!-- TOTAL -->
    <div class="total">
        TOTAL: Rp <?= number_format($data['total_harga']); ?>
    </div>

    <!-- PRINT BUTTON -->
    <div class="btn-print">
        <button onclick="window.print()">🖨 Cetak Kwitansi</button>
    </div>

</div>

</body>
</html>
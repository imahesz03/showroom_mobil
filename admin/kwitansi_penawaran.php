<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../auth/login.php");
    exit;
}

$id = $_GET['id'] ?? '';

if(empty($id)){
    die("ID penawaran tidak ditemukan.");
}

/* DATA PENAWARAN */
$query = mysqli_query($koneksi, "
    SELECT 
        pn.*,

        p.nama AS nama_penjual,
        p.no_hp AS no_hp_penjual,
        p.alamat AS alamat_penjual,

        m.nama_mobil,
        m.tahun,
        m.harga AS harga_mobil,
        m.foto

    FROM penawaran pn

    LEFT JOIN penjual p 
    ON pn.id_penjual = p.id_penjual

    LEFT JOIN mobil m 
    ON pn.id_mobil = m.id_mobil

    WHERE pn.id_penawaran='$id'
    LIMIT 1
");

if(!$query){
    die("Query error: " . mysqli_error($koneksi));
}

$data = mysqli_fetch_assoc($query);

if(!$data){
    die("Data penawaran tidak ditemukan.");
}

if(($data['status'] ?? '') != 'diterima'){
    die("Kwitansi hanya bisa dicetak untuk penawaran yang diterima.");
}

if(($data['metode_pembayaran'] ?? '') == 'transfer' && empty($data['bukti_pembayaran'])){
    die("Kwitansi belum bisa dicetak karena bukti pembayaran transfer belum diupload.");
}

$tanggal = !empty($data['tanggal_keputusan'])
    ? date('d F Y', strtotime($data['tanggal_keputusan']))
    : date('d F Y');

$nomor_kwitansi = "KW-PNW-" . str_pad($data['id_penawaran'], 5, '0', STR_PAD_LEFT);

$harga_tawar = (int)($data['harga_tawar'] ?? 0);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi Penawaran</title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family: Arial, sans-serif;
        }

        body{
            background:#f5f5f5;
            padding:40px;
        }

        .kwitansi{
            width:900px;
            margin:auto;
            background:white;
            padding:40px;
            border-radius:12px;
            box-shadow:0 0 20px rgba(0,0,0,.08);
        }

        .header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            border-bottom:3px solid #4e73df;
            padding-bottom:20px;
            margin-bottom:30px;
        }

        .logo{
            font-size:28px;
            font-weight:800;
            color:#4e73df;
        }

        .header-right{
            text-align:right;
        }

        .header-right h2{
            color:#4e73df;
            margin-bottom:6px;
        }

        .info-grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:30px;
            margin-bottom:30px;
        }

        .info-box{
            background:#f8f9fc;
            padding:20px;
            border-radius:10px;
        }

        .info-box h4{
            margin-bottom:12px;
            color:#4e73df;
        }

        .info-box p{
            margin-bottom:8px;
            font-size:14px;
            line-height:1.7;
        }

        .mobil-section{
            margin-bottom:30px;
        }

        .mobil-section h3{
            margin-bottom:15px;
            color:#4e73df;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        table th{
            background:#4e73df;
            color:white;
            padding:12px;
            font-size:14px;
        }

        table td{
            border:1px solid #ddd;
            padding:12px;
            font-size:14px;
        }

        .total-box{
            margin-top:20px;
            text-align:right;
        }

        .total-box h2{
            color:#1cc88a;
        }

        .status-box{
            margin-top:30px;
            padding:18px;
            background:#e8fff2;
            border:2px solid #1cc88a;
            border-radius:10px;
            text-align:center;
        }

        .status-box h3{
            color:#1cc88a;
            margin-bottom:8px;
        }

        .footer{
            margin-top:50px;
            display:flex;
            justify-content:space-between;
            align-items:flex-end;
        }

        .signature{
            text-align:center;
            width:250px;
        }

        .signature-space{
            height:90px;
        }

        .print-btn{
            margin-top:30px;
            text-align:center;
        }

        .print-btn button{
            background:#4e73df;
            color:white;
            border:none;
            padding:12px 28px;
            border-radius:8px;
            cursor:pointer;
            font-size:14px;
            font-weight:700;
        }

        .print-btn button:hover{
            opacity:.9;
        }

        @media print{
            body{
                background:white;
                padding:0;
            }

            .kwitansi{
                width:100%;
                box-shadow:none;
                border-radius:0;
            }

            .print-btn{
                display:none;
            }
        }
    </style>
</head>

<body>

<div class="kwitansi">

    <div class="header">

        <div class="logo">
            GALAXY SHOWROOM
        </div>

        <div class="header-right">
            <h2>KWITANSI PENAWARAN</h2>
            <p><?= $nomor_kwitansi; ?></p>
        </div>

    </div>

    <div class="info-grid">

        <div class="info-box">

            <h4>Data Penjual</h4>

            <p>
                <strong>Nama:</strong><br>
                <?= htmlspecialchars($data['nama_penjual'] ?? '-'); ?>
            </p>

            <p>
                <strong>No HP:</strong><br>
                <?= htmlspecialchars($data['no_hp_penjual'] ?? '-'); ?>
            </p>

            <p>
                <strong>Alamat:</strong><br>
                <?= htmlspecialchars($data['alamat_penjual'] ?? '-'); ?>
            </p>

        </div>

        <div class="info-box">

            <h4>Informasi Penawaran</h4>

            <p>
                <strong>Tanggal:</strong><br>
                <?= $tanggal; ?>
            </p>

            <p>
                <strong>Status:</strong><br>
                Diterima
            </p>

            <p>
                <strong>Metode Pembayaran:</strong><br>
                <?= ucfirst($data['metode_pembayaran'] ?? '-'); ?>
            </p>

        </div>

    </div>

    <div class="mobil-section">

        <h3>Detail Mobil</h3>

        <table>

            <thead>
                <tr>
                    <th>Nama Mobil</th>
                    <th>Tahun</th>
                    <th>Harga Pasaran</th>
                    <th>Harga Penawaran</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td><?= htmlspecialchars($data['nama_mobil'] ?? '-'); ?></td>

                    <td><?= htmlspecialchars($data['tahun'] ?? '-'); ?></td>

                    <td>
                        Rp <?= number_format($data['harga_mobil'] ?? 0, 0, ',', '.'); ?>
                    </td>

                    <td>
                        <strong>
                            Rp <?= number_format($harga_tawar, 0, ',', '.'); ?>
                        </strong>
                    </td>
                </tr>
            </tbody>

        </table>

    </div>

    <div class="total-box">

        <p>Total Penawaran Disetujui</p>

        <h2>
            Rp <?= number_format($harga_tawar, 0, ',', '.'); ?>
        </h2>

    </div>

    <div class="status-box">

        <h3>
            PENAWARAN MOBIL DITERIMA
        </h3>

        <p>
            Mobil telah disetujui oleh pihak showroom dan siap diproses lebih lanjut.
        </p>

    </div>

    <?php if(!empty($data['catatan_admin'])){ ?>

        <div style="margin-top:30px;">

            <h4 style="margin-bottom:10px; color:#4e73df;">
                Catatan Admin
            </h4>

            <div style="
                background:#f8f9fc;
                padding:15px;
                border-radius:8px;
                line-height:1.7;
                font-size:14px;
            ">
                <?= nl2br(htmlspecialchars($data['catatan_admin'])); ?>
            </div>

        </div>

    <?php } ?>

    <div class="footer">

        <div>
            <p style="font-size:13px; color:#666;">
                Dicetak otomatis oleh sistem Galaxy Showroom
            </p>
        </div>

        <div class="signature">

            <p>Tangerang, <?= date('d/m/Y'); ?></p>

            <div class="signature-space"></div>

            <strong>Admin Showroom</strong>

        </div>

    </div>

    <div class="print-btn">
        <button onclick="window.print()">
            🖨 Cetak Kwitansi
        </button>
    </div>

</div>

</body>
</html>
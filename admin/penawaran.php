<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../auth/login.php");
    exit;
}

/*
|------------------------------------------------------
| TAMBAH KOLOM JIKA BELUM ADA
|------------------------------------------------------
*/
$columns = [
    "status" => "ALTER TABLE penawaran ADD status ENUM('menunggu','diterima','ditolak') DEFAULT 'menunggu'",
    "metode_pembayaran" => "ALTER TABLE penawaran ADD metode_pembayaran ENUM('tunai','transfer') DEFAULT NULL",
    "bukti_pembayaran" => "ALTER TABLE penawaran ADD bukti_pembayaran VARCHAR(255) DEFAULT NULL",
    "tanggal_keputusan" => "ALTER TABLE penawaran ADD tanggal_keputusan DATETIME DEFAULT NULL",
    "catatan_admin" => "ALTER TABLE penawaran ADD catatan_admin TEXT DEFAULT NULL",
    "catatan" => "ALTER TABLE penawaran ADD catatan TEXT DEFAULT NULL"
];

foreach($columns as $col => $sql){
    $cek = mysqli_query($koneksi, "SHOW COLUMNS FROM penawaran LIKE '$col'");
    if(mysqli_num_rows($cek) == 0){
        mysqli_query($koneksi, $sql);
    }
}

/*
|------------------------------------------------------
| DATA PENAWARAN
|------------------------------------------------------
*/
$query = mysqli_query($koneksi, "
    SELECT 
        pn.*,

        p.nama AS nama_penjual,
        p.no_hp AS no_hp_penjual,
        p.alamat AS alamat_penjual,

        m.nama_mobil,
        m.tahun,
        m.harga,
        m.foto

    FROM penawaran pn
    LEFT JOIN penjual p ON pn.id_penjual = p.id_penjual
    LEFT JOIN mobil m ON pn.id_mobil = m.id_mobil
    ORDER BY pn.id_penawaran DESC
");

if(!$query){
    die("Query error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Penawaran Mobil</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">

    <style>
        .table td{
            vertical-align: middle;
            font-size: 14px;
        }

        .table th{
            font-size: 14px;
            white-space: nowrap;
        }

        .badge{
            font-size: 12px;
            padding: 6px 9px;
        }

        .mobil-img{
            width: 75px;
            height: 55px;
            object-fit: cover;
            border-radius: 8px;
        }

        .small-text{
            font-size: 12px;
            color: #858796;
        }

        .aksi-box{
            min-width: 50px;
        }
    </style>
</head>

<body id="page-top">

<div id="wrapper">

    <?php include "../includes/sidebar_admin.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php 
            $pageTitle = "Penawaran Mobil";
            include "../includes/topbar.php"; 
            ?>

            <div class="container-fluid">

                <h1 class="h3 mb-4 text-gray-800 font-weight-bold">
                    Penawaran Mobil dari Penjual
                </h1>

                <?php if(isset($_SESSION['success'])){ ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php } ?>

                <?php if(isset($_SESSION['error'])){ ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php } ?>

                <div class="card shadow mb-4">

                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Data Penawaran Mobil
                        </h6>

                        <input type="text"
                               id="searchInput"
                               class="form-control form-control-sm"
                               style="max-width:300px;"
                               placeholder="Cari penjual / mobil..."
                               onkeyup="filterTable()">
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover text-center" id="tablePenawaran">

                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Foto</th>
                                        <th>Penjual</th>
                                        <th>Mobil</th>
                                        <th>Harga Mobil</th>
                                        <th>Harga Tawar</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Metode</th>
                                        <th>Bukti Bayar</th>
                                        <th>Catatan Admin</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php if(mysqli_num_rows($query) > 0){ ?>

                                        <?php 
                                        $no = 1;
                                        while($row = mysqli_fetch_assoc($query)){ 

                                            $status = $row['status'] ?? 'menunggu';

                                            if($status == 'diterima'){
                                                $badge = 'success';
                                                $textStatus = 'Diterima';
                                            } elseif($status == 'ditolak'){
                                                $badge = 'danger';
                                                $textStatus = 'Ditolak';
                                            } else {
                                                $badge = 'warning';
                                                $textStatus = 'Menunggu';
                                            }

                                            $foto = !empty($row['foto']) ? "../uploads/" . $row['foto'] : "../assets/img/no-image.png";

                                            $metode = $row['metode_pembayaran'] ?? '';

                                            $bolehCetak = false;

                                            if($status == 'diterima'){
                                                if($metode == 'tunai'){
                                                    $bolehCetak = true;
                                                } elseif($metode == 'transfer' && !empty($row['bukti_pembayaran'])){
                                                    $bolehCetak = true;
                                                }
                                            }
                                        ?>

                                        <tr>
                                            <td><?= $no++; ?></td>

                                            <td>
                                                <img src="<?= htmlspecialchars($foto); ?>" class="mobil-img">
                                            </td>

                                            <td>
                                                <strong><?= htmlspecialchars($row['nama_penjual'] ?? '-'); ?></strong><br>
                                                <small class="small-text">
                                                    <?= htmlspecialchars($row['no_hp_penjual'] ?? '-'); ?>
                                                </small>
                                            </td>

                                            <td>
                                                <strong><?= htmlspecialchars($row['nama_mobil'] ?? '-'); ?></strong><br>
                                                <small class="small-text">
                                                    Tahun <?= htmlspecialchars($row['tahun'] ?? '-'); ?>
                                                </small>
                                            </td>

                                            <td>
                                                Rp <?= number_format($row['harga'] ?? 0, 0, ',', '.'); ?>
                                            </td>

                                            <td>
                                                <strong>
                                                    Rp <?= number_format($row['harga_tawar'] ?? 0, 0, ',', '.'); ?>
                                                </strong>
                                            </td>

                                            <td>
                                                <?= !empty($row['tanggal']) ? date('d-m-Y', strtotime($row['tanggal'])) : '-'; ?>
                                            </td>

                                            <td>
                                                <span class="badge badge-<?= $badge; ?>">
                                                    <?= $textStatus; ?>
                                                </span>
                                            </td>

                                            <td>
                                                <?= !empty($metode) ? ucfirst($metode) : '-'; ?>
                                            </td>

                                            <td>
                                                <?php if($metode == 'transfer'){ ?>

                                                    <?php if(!empty($row['bukti_pembayaran'])){ ?>

                                                        <a href="../uploads/<?= htmlspecialchars($row['bukti_pembayaran']); ?>"
                                                           target="_blank"
                                                           class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i> Lihat
                                                        </a>

                                                    <?php } else { ?>

                                                        <span class="badge badge-danger">
                                                            Belum Upload
                                                        </span>

                                                    <?php } ?>

                                                <?php } else { ?>

                                                    -

                                                <?php } ?>
                                            </td>

                                            <td>
                                                <?= !empty($row['catatan_admin']) ? htmlspecialchars($row['catatan_admin']) : '-'; ?>
                                            </td>

                                            <td class="aksi-box">

                                                <?php if($status == 'menunggu'){ ?>

                                                    <form method="POST"
                                                          action="proses_penawaran.php"
                                                          enctype="multipart/form-data"
                                                          class="mb-2">

                                                        <input type="hidden"
                                                               name="id_penawaran"
                                                               value="<?= $row['id_penawaran']; ?>">

                                                        <select name="metode_pembayaran"
                                                                class="form-control form-control-sm mb-2 metodeBayar"
                                                                required>
                                                            <option value="">-- Metode Bayar --</option>
                                                            <option value="tunai">Cash / Tunai</option>
                                                            <option value="transfer">Transfer</option>
                                                        </select>

                                                        <input type="file"
                                                               name="bukti_pembayaran"
                                                               class="form-control-file mb-2 buktiBayar"
                                                               accept="image/jpeg,image/png,image/webp"
                                                               style="display:none;">

                                                        <small class="text-danger d-none infoBukti">
                                                            Bukti bayar wajib diupload jika metode transfer.
                                                        </small>

                                                        <textarea name="catatan_admin"
                                                                  class="form-control form-control-sm mb-2"
                                                                  rows="2"
                                                                  placeholder="Catatan penerimaan"></textarea>

                                                        <button type="submit"
                                                                name="terima"
                                                                class="btn btn-sm btn-success"
                                                                onclick="return confirm('Terima penawaran ini?')">
                                                            <i class="fas fa-check"></i> Terima
                                                        </button>
                                                    </form>

                                                    <form method="POST" action="proses_penawaran.php">
                                                        <input type="hidden"
                                                               name="id_penawaran"
                                                               value="<?= $row['id_penawaran']; ?>">

                                                        <textarea name="catatan_admin"
                                                                  class="form-control form-control-sm mb-2"
                                                                  rows="2"
                                                                  placeholder="Alasan penolakan"></textarea>

                                                        <button type="submit"
                                                                name="tolak"
                                                                class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Tolak penawaran ini?')">
                                                            <i class="fas fa-times"></i> Tolak
                                                        </button>
                                                    </form>

                                                <?php } elseif($status == 'diterima'){ ?>

                                                    <?php if($bolehCetak){ ?>

                                                        <a href="kwitansi_penawaran.php?id=<?= $row['id_penawaran']; ?>"
                                                           target="_blank"
                                                           class="btn btn-sm btn-success">
                                                            <i class="fas fa-print"></i> Cetak Kwitansi
                                                        </a>

                                                    <?php } else { ?>

                                                        <button class="btn btn-sm btn-secondary" disabled>
                                                            <i class="fas fa-lock"></i> Cetak Terkunci
                                                        </button>

                                                        <br>

                                                        <small class="text-danger">
                                                            Upload bukti pembayaran transfer terlebih dahulu.
                                                        </small>

                                                    <?php } ?>

                                                <?php } else { ?>

                                                    <span class="text-danger font-weight-bold">
                                                        Penawaran Ditolak
                                                    </span>

                                                <?php } ?>

                                            </td>
                                        </tr>

                                        <?php } ?>

                                    <?php } else { ?>

                                        <tr>
                                            <td colspan="12" class="text-center text-muted py-4">
                                                Belum ada data penawaran mobil.
                                            </td>
                                        </tr>

                                    <?php } ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../assets/js/sb-admin-2.min.js"></script>

<script>
function filterTable(){
    let input = document.getElementById("searchInput").value.toLowerCase();
    let rows = document.querySelectorAll("#tablePenawaran tbody tr");

    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(input) ? "" : "none";
    });
}

document.querySelectorAll(".metodeBayar").forEach(function(select){
    select.addEventListener("change", function(){
        let form = this.closest("form");
        let bukti = form.querySelector(".buktiBayar");
        let info = form.querySelector(".infoBukti");

        if(this.value === "transfer"){
            bukti.style.display = "block";
            bukti.required = true;
            info.classList.remove("d-none");
        } else {
            bukti.style.display = "none";
            bukti.required = false;
            bukti.value = "";
            info.classList.add("d-none");
        }
    });
});
</script>

</body>
</html>
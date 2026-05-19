<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "pembeli") {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['id_user'] ?? 0;

/*
|------------------------------------------------------
| AMBIL DATA PEMBELI LOGIN
|------------------------------------------------------
*/
$qPembeli = mysqli_query($koneksi, "
    SELECT id_pembeli
    FROM pembeli
    WHERE id_user = '$id_user'
    LIMIT 1
");

if (!$qPembeli || mysqli_num_rows($qPembeli) == 0) {
    echo "<script>
        alert('Data pembeli tidak ditemukan!');
        window.location='../auth/login.php';
    </script>";
    exit;
}

$pembeli = mysqli_fetch_assoc($qPembeli);
$id_pembeli = $pembeli['id_pembeli'];

/*
|------------------------------------------------------
| DATA STATUS ADMINISTRASI
| Catatan:
| Jika tabel administrasi_kendaraan belum ada / belum dipakai,
| status ditampilkan otomatis berdasarkan status pemesanan.
|------------------------------------------------------
*/
$query = mysqli_query($koneksi, "
    SELECT 
        p.id_pemesanan,
        p.tanggal_pesan,
        p.status AS status_pesanan,
        p.foto_ktp,

        m.nama_mobil,
        m.tahun,
        m.foto

    FROM pemesanan p
    JOIN mobil m ON p.id_mobil = m.id_mobil

    WHERE p.id_pembeli = '$id_pembeli'
    AND p.status IN ('dp','lunas')

    ORDER BY p.id_pemesanan DESC
");

if (!$query) {
    die("Query status administrasi error: " . mysqli_error($koneksi));
}

function statusBadge($status)
{
    if ($status == "lunas") {
        return [
            "badge" => "success",
            "text"  => "Selesai / Siap Pengiriman",
            "icon"  => "fas fa-check-circle"
        ];
    }

    if ($status == "dp") {
        return [
            "badge" => "info",
            "text"  => "Sedang Diproses",
            "icon"  => "fas fa-spinner"
        ];
    }

    return [
        "badge" => "secondary",
        "text"  => "Belum Diproses",
        "icon"  => "fas fa-clock"
    ];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Status Administrasi - Galaxy Showroom</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

<div id="wrapper">

    <?php include "../includes/sidebar_pembeli.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php 
            $pageTitle = "Status STNK/BPKB";
            include "../includes/topbar.php"; 
            ?>

            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">

                    <div>
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold">
                            Status STNK/BPKB
                        </h1>

                        <p class="mb-0 text-gray-600">
                            Pantau status administrasi kendaraan kamu secara sederhana.
                        </p>
                    </div>

                    <a href="pesanan_saya.php" class="btn btn-primary btn-sm shadow-sm mt-3 mt-sm-0">
                        <i class="fas fa-shopping-cart mr-1"></i>
                        Pesanan Saya
                    </a>

                </div>

                <div class="card shadow mb-4">

                    <div class="card-header py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">

                        <h6 class="m-0 font-weight-bold text-primary mb-3 mb-md-0">
                            <i class="fas fa-file-signature mr-1"></i>
                            Data Administrasi Kendaraan
                        </h6>

                        <input type="text"
                               id="searchInput"
                               class="form-control form-control-sm"
                               style="max-width: 320px;"
                               placeholder="Cari mobil atau status..."
                               onkeyup="filterTable()">

                    </div>

                    <div class="card-body">

                        <?php if (mysqli_num_rows($query) > 0) : ?>

                            <div class="table-responsive">

                                <table class="table table-bordered table-hover" id="adminTable" width="100%" cellspacing="0">

                                    <thead class="thead-light text-center">
                                        <tr>
                                            <th width="80">Foto</th>
                                            <th>Mobil</th>
                                            <th>Tanggal Pesan</th>
                                            <th>KTP</th>
                                            <th>Status STNK/BPKB</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                    <?php while ($row = mysqli_fetch_assoc($query)) : ?>

                                        <?php
                                        $foto = !empty($row['foto'])
                                            ? "../uploads/" . $row['foto']
                                            : "../assets/img/undraw_posting_photo.svg";

                                        $status = strtolower($row['status_pesanan']);
                                        $info = statusBadge($status);
                                        ?>

                                        <tr>

                                            <td class="text-center align-middle">

                                                <img src="<?= htmlspecialchars($foto); ?>"
                                                     class="img-thumbnail"
                                                     width="70"
                                                     height="55"
                                                     style="object-fit:cover;"
                                                     onerror="this.src='../assets/img/undraw_posting_photo.svg'">

                                            </td>

                                            <td class="align-middle">

                                                <div class="font-weight-bold text-gray-800">
                                                    <?= htmlspecialchars($row['nama_mobil'] ?? '-'); ?>
                                                </div>

                                                <div class="small text-gray-500">
                                                    Tahun <?= htmlspecialchars($row['tahun'] ?? '-'); ?>
                                                </div>

                                            </td>

                                            <td class="text-center align-middle">
                                                <?= date('d M Y', strtotime($row['tanggal_pesan'])); ?>
                                            </td>

                                            <td class="text-center align-middle">

                                                <?php if (!empty($row['foto_ktp'])) : ?>

                                                    <a href="../uploads/<?= htmlspecialchars($row['foto_ktp']); ?>"
                                                       target="_blank"
                                                       class="btn btn-info btn-sm">
                                                        <i class="fas fa-id-card mr-1"></i>
                                                        Lihat
                                                    </a>

                                                <?php else : ?>

                                                    <span class="badge badge-warning px-3 py-2">
                                                        Belum Upload
                                                    </span>

                                                <?php endif; ?>

                                            </td>

                                            <td class="text-center align-middle">

                                                <span class="badge badge-<?= $info['badge']; ?> px-3 py-2">
                                                    <i class="<?= $info['icon']; ?> mr-1"></i>
                                                    <?= $info['text']; ?>
                                                </span>

                                            </td>

                                            <td class="align-middle">

                                                <?php if ($status == "dp") : ?>

                                                    <span class="text-gray-700">
                                                        Administrasi STNK/BPKB sedang diproses. Silakan lakukan pelunasan agar mobil bisa masuk tahap pengiriman.
                                                    </span>

                                                <?php elseif ($status == "lunas") : ?>

                                                    <span class="text-gray-700">
                                                        Pembayaran sudah lunas. Administrasi dianggap selesai dan mobil siap masuk tahap pengiriman.
                                                    </span>

                                                <?php else : ?>

                                                    <span class="text-muted">
                                                        Administrasi belum diproses.
                                                    </span>

                                                <?php endif; ?>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                    </tbody>

                                </table>

                            </div>

                        <?php else : ?>

                            <div class="text-center py-5">

                                <i class="fas fa-file-alt fa-3x text-gray-300 mb-3"></i>

                                <h5 class="text-gray-800">
                                    Belum Ada Administrasi
                                </h5>

                                <p class="text-muted mb-4">
                                    Status STNK/BPKB akan muncul setelah kamu membayar DP atau melunasi pesanan.
                                </p>

                                <a href="pesanan_saya.php" class="btn btn-primary">
                                    <i class="fas fa-shopping-cart mr-1"></i>
                                    Pesanan Saya
                                </a>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../assets/js/sb-admin-2.min.js"></script>

<script>
function filterTable() {
    let input = document.getElementById("searchInput").value.toLowerCase();
    let table = document.getElementById("adminTable");

    if (!table) return;

    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let text = tr[i].innerText.toLowerCase();
        tr[i].style.display = text.includes(input) ? "" : "none";
    }
}
</script>

</body>
</html>
<?php
session_start();
include "../config/koneksi.php";

if ($_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit;
}

$query = mysqli_query($koneksi, "SELECT * FROM pembeli ORDER BY id_pembeli DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Pembeli Admin</title>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
    
</head>

<body id="page-top">

<div id="wrapper">

    <?php include "../includes/sidebar_admin.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php include "../includes/topbar.php"; ?>

            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold tracking-tight">Data Pembeli</h1>
                        <p class="mb-0 text-muted small">
                            Halaman manajemen untuk memantau data profil dan riwayat aktivitas pelanggan.
                        </p>
                    </div>
                </div>

                <div class="card shadow border-0 mb-4 rounded-lg">

                    <div class="card-header bg-white border-bottom py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                        <div class="d-flex align-items-center mb-3 mb-md-0">
                            <div class="bg-light p-2 rounded mr-3">
                                <i class="fas fa-users text-primary"></i>
                            </div>
                            <div>
                                <h6 class="m-0 font-weight-bold text-gray-800">Daftar Pelanggan</h6>
                                <p class="m-0 text-muted small">Total profil pembeli tercatat dalam database</p>
                            </div>
                        </div>

                        <div class="input-group shadow-sm" style="max-width: 340px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0 text-muted">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                            <input type="text"
                                   id="searchInput"
                                   class="form-control bg-light border-left-0 text-sm"
                                   placeholder="Cari nama, no HP, alamat..."
                                   onkeyup="filterTable()">
                        </div>
                    </div>

                    <div class="card-body p-0">

                        <?php if (mysqli_num_rows($query) > 0) { ?>

                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="tablePembeli" width="100%" cellspacing="0">

                                    <thead class="bg-light text-muted text-uppercase small font-weight-bold border-top-0">
                                        <tr>
                                            <th class="text-center border-0 py-3" style="width: 8%;">No</th>
                                            <th class="border-0 py-3" style="width: 32%;">Nama Pembeli</th>
                                            <th class="border-0 py-3" style="width: 20%;">Kontak HP</th>
                                            <th class="border-0 py-3" style="width: 25%;">Alamat Rumah</th>
                                            <th class="text-center border-0 py-3" style="width: 15%;">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody class="text-gray-700">
                                        <?php
                                        $no = 1;
                                        while ($data = mysqli_fetch_assoc($query)) {
                                        ?>

                                        <tr class="align-middle">
                                            <td class="text-center align-middle font-weight-bold text-muted"><?= $no++; ?></td>

                                            <td class="align-middle">
                                                <div class="font-weight-bold text-gray-800 text-base"><?= htmlspecialchars($data['nama']); ?></div>
                                                <small class="text-muted">ID Client: #<?= $data['id_pembeli']; ?></small>
                                            </td>

                                            <td class="align-middle text-dark font-weight-bold">
                                                <?= htmlspecialchars($data['no_hp']); ?>
                                            </td>

                                            <td class="align-middle text-muted small">
                                                <?= htmlspecialchars($data['alamat']); ?>
                                            </td>

                                            <td class="text-center align-middle">
                                                <a href="riwayat_pembelian.php?id=<?= $data['id_pembeli']; ?>"
                                                   class="btn btn-sm btn-white text-success border shadow-sm px-3 font-weight-bold"
                                                   title="Lihat Riwayat">
                                                    <i class="fas fa-history mr-1"></i> Riwayat
                                                </a>
                                            </td>
                                        </tr>

                                        <?php } ?>
                                    </tbody>

                                </table>
                            </div>

                            <div id="no-result" class="text-center text-muted py-5 mx-3" style="display:none;">
                                <div class="p-3 bg-light d-inline-block rounded-circle mb-3">
                                    <i class="fas fa-search-minus fa-2x text-gray-400"></i>
                                </div>
                                <h6 class="font-weight-bold text-gray-800 mb-1">Data tidak cocok</h6>
                                <p class="small text-muted mb-0">Periksa kembali ejaan atau kata kunci pencarian Anda.</p>
                            </div>

                        <?php } else { ?>

                            <div class="text-center py-5 my-5">
                                <div class="p-4 bg-light d-inline-block rounded-circle mb-4">
                                    <i class="fas fa-users-slash fa-3x text-gray-300"></i>
                                </div>
                                <h5 class="text-gray-800 font-weight-bold mb-1">Data Pembeli Kosong</h5>
                                <p class="text-muted small mb-0 mx-auto" style="max-width: 360px;">
                                    Belum ada profil akun pembeli atau data pelanggan yang terdaftar dalam database showroom.
                                </p>
                            </div>

                        <?php } ?>

                    </div>

                </div>

            </div>
            </div>
        </div>
    </div>
<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sb-admin-2.min.js"></script>

<script>
function filterTable() {
    const input = document.getElementById('searchInput');
    const table = document.getElementById('tablePembeli');
    const noResult = document.getElementById('no-result');

    if (!input || !table) return;

    const filter = input.value.toLowerCase();
    const tbody = table.getElementsByTagName('tbody')[0];
    if (!tbody) return;
    
    const rows = tbody.getElementsByTagName('tr');
    let visibleCount = 0;

    for (let i = 0; i < rows.length; i++) {
        const rowText = rows[i].innerText.toLowerCase();

        if (rowText.includes(filter)) {
            rows[i].style.display = '';
            visibleCount++;
        } else {
            rows[i].style.display = 'none';
        }
    }

    if (noResult) {
        noResult.style.display = visibleCount === 0 ? 'block' : 'none';
    }
}
</script>

</body>
</html>
<?php
session_start();
include "../config/koneksi.php";

if ($_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit;
}

$query = mysqli_query($koneksi, "SELECT * FROM mobil ORDER BY id_mobil DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Mobil Admin</title>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SB ADMIN 2 CSS -->
    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

<div id="wrapper">

    <!-- SIDEBAR -->
    <?php include "../includes/sidebar_admin.php"; ?>

    <!-- CONTENT WRAPPER -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- MAIN CONTENT -->
        <div id="content">

            <!-- TOPBAR -->
            <?php include "../includes/topbar.php"; ?>

            <!-- PAGE CONTENT -->
            <div class="container-fluid">

                <!-- TITLE -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Data Mobil</h1>
                        <p class="mb-0 text-gray-600">
                            Kelola data mobil yang tersedia di showroom.
                        </p>
                    </div>

                    <a href="tambah_mobil.php" class="btn btn-primary shadow-sm mt-3 mt-sm-0">
                        <i class="fas fa-plus fa-sm text-white-50"></i>
                        Tambah Mobil
                    </a>
                </div>

                <!-- TABLE CARD -->
                <div class="card shadow mb-4">

                    <div class="card-header py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary mb-3 mb-md-0">
                            Daftar Mobil
                        </h6>

                        <input type="text"
                               id="searchInput"
                               class="form-control"
                               style="max-width: 320px;"
                               placeholder="Cari nama mobil, harga, stok, status..."
                               onkeyup="filterTable()">
                    </div>

                    <div class="card-body">

                        <?php if (mysqli_num_rows($query) > 0) { ?>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="tableMobil" width="100%" cellspacing="0">

                                    <thead class="thead-light">
                                        <tr class="text-center">
                                            <th>No</th>
                                            <th>Nama Mobil</th>
                                            <th>Harga</th>
                                            <th>Stok</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        $no = 1;
                                        while ($data = mysqli_fetch_assoc($query)) {
                                        ?>

                                        <tr>
                                            <td class="text-center"><?= $no++; ?></td>

                                            <td><?= htmlspecialchars($data['nama_mobil']); ?></td>

                                            <td>
                                                Rp <?= number_format($data['harga'], 0, ',', '.'); ?>
                                            </td>

                                            <td class="text-center">
                                                <?= htmlspecialchars($data['stok']); ?>
                                            </td>

                                            <td class="text-center">
                                                <?php if ($data['status'] == "tersedia") { ?>
                                                    <span class="badge badge-success px-3 py-2">
                                                        Tersedia
                                                    </span>
                                                <?php } else { ?>
                                                    <span class="badge badge-danger px-3 py-2">
                                                        Terjual
                                                    </span>
                                                <?php } ?>
                                            </td>

                                            <td class="text-center">
                                                <a href="edit_mobil.php?id=<?= $data['id_mobil']; ?>"
                                                   class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                    Edit
                                                </a>

                                                <a href="hapus_mobil_admin.php?id=<?= $data['id_mobil']; ?>"
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Yakin ingin menghapus mobil ini?')">
                                                    <i class="fas fa-trash"></i>
                                                    Hapus
                                                </a>
                                            </td>
                                        </tr>

                                        <?php } ?>
                                    </tbody>

                                </table>
                            </div>

                            <div id="no-result" class="text-center text-muted py-4" style="display:none;">
                                <i class="fas fa-search"></i>
                                Data tidak ditemukan.
                            </div>

                        <?php } else { ?>

                            <div class="text-center py-5">
                                <i class="fas fa-car fa-3x text-gray-300 mb-3"></i>
                                <h5 class="text-gray-800">Data Mobil Kosong</h5>
                                <p class="text-muted">
                                    Belum ada data mobil yang ditambahkan.
                                </p>

                                <a href="tambah_mobil.php" class="btn btn-primary">
                                    <i class="fas fa-plus"></i>
                                    Tambah Mobil
                                </a>
                            </div>

                        <?php } ?>

                    </div>

                </div>

            </div>
            <!-- END PAGE CONTENT -->

        </div>
        <!-- END MAIN CONTENT -->

    </div>
    <!-- END CONTENT WRAPPER -->

</div>
<!-- END WRAPPER -->

<!-- SB ADMIN 2 JS -->
<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sb-admin-2.min.js"></script>

<script>
function filterTable() {
    const input = document.getElementById('searchInput');
    const table = document.getElementById('tableMobil');
    const noResult = document.getElementById('no-result');

    if (!input || !table) return;

    const filter = input.value.toLowerCase();
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

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
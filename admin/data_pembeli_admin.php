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
    
    <!-- SB ADMIN 2 CSS -->
    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
    
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
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Data Pembeli</h1>
                        <p class="mb-0 text-gray-600">
                            Kelola dan lihat data pembeli showroom.
                        </p>
                    </div>
                </div>

                <!-- TABLE CARD -->
                <div class="card shadow mb-4">

                    <div class="card-header py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary mb-3 mb-md-0">
                            Daftar Pembeli
                        </h6>

                        <input type="text"
                               id="searchInput"
                               class="form-control"
                               style="max-width: 320px;"
                               placeholder="Cari nama, no HP, alamat..."
                               onkeyup="filterTable()">
                    </div>

                    <div class="card-body">

                        <?php if (mysqli_num_rows($query) > 0) { ?>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="tablePembeli" width="100%" cellspacing="0">

                                    <thead class="thead-light">
                                        <tr class="text-center">
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>No HP</th>
                                            <th>Alamat</th>
                                            <th>Detail</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        $no = 1;
                                        while ($data = mysqli_fetch_assoc($query)) {
                                        ?>

                                        <tr>
                                            <td class="text-center"><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($data['nama']); ?></td>
                                            <td><?= htmlspecialchars($data['no_hp']); ?></td>
                                            <td><?= htmlspecialchars($data['alamat']); ?></td>

                                            <td class="text-center">
                                                <a href="riwayat_pembelian.php?id=<?= $data['id_pembeli']; ?>"
                                                   class="btn btn-success btn-sm">
                                                    <i class="fas fa-history"></i>
                                                    Riwayat
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
                                <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                                <h5 class="text-gray-800">Data Pembeli Kosong</h5>
                                <p class="text-muted">
                                    Belum ada data pembeli yang terdaftar.
                                </p>
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
    const table = document.getElementById('tablePembeli');
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
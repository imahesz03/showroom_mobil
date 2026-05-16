<?php
session_start();
include "../config/koneksi.php";

if ($_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Mobil</title>

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
                        <h1 class="h3 mb-0 text-gray-800">Tambah Mobil</h1>
                        <p class="mb-0 text-gray-600">
                            Tambahkan data mobil baru ke showroom.
                        </p>
                    </div>

                    <a href="data_mobil_admin.php" class="btn btn-secondary shadow-sm mt-3 mt-sm-0">
                        <i class="fas fa-arrow-left fa-sm text-white-50"></i>
                        Kembali
                    </a>
                </div>

                <!-- FORM CARD -->
                <div class="card shadow mb-4">

                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Form Tambah Mobil
                        </h6>
                    </div>

                    <div class="card-body">

                        <form action="proses_tambah_mobil.php" method="POST" enctype="multipart/form-data">

                            <!-- NAMA MOBIL -->
                            <div class="form-group">
                                <label>Nama Mobil</label>
                                <input type="text"
                                       name="nama_mobil"
                                       class="form-control"
                                       placeholder="Masukkan nama mobil"
                                       required>
                            </div>

                            <!-- DESKRIPSI -->
                            <div class="form-group">
                                <label>Deskripsi Mobil</label>
                                <textarea name="deskripsi"
                                          class="form-control"
                                          rows="4"
                                          placeholder="Masukkan deskripsi mobil"
                                          required></textarea>
                            </div>

                            <!-- TAHUN -->
                            <div class="form-group">
                                <label>Tahun Mobil</label>
                                <input type="number"
                                       name="tahun"
                                       class="form-control"
                                       placeholder="Contoh: 2022"
                                       min="1900"
                                       max="2099"
                                       required>
                            </div>

                            <!-- HARGA -->
                            <div class="form-group">
                                <label>Harga</label>
                                <input type="number"
                                       name="harga"
                                       class="form-control"
                                       placeholder="Masukkan harga mobil"
                                       required>
                            </div>

                            <!-- STOK -->
                            <div class="form-group">
                                <label>Stok</label>
                                <input type="number"
                                       name="stok"
                                       class="form-control"
                                       placeholder="Masukkan stok mobil"
                                       required>
                            </div>

                            <!-- FOTO -->
                            <div class="form-group">
                                <label>Foto Mobil</label>
                                <input type="file"
                                       name="foto"
                                       class="form-control-file"
                                       accept="image/*"
                                       required>
                            </div>

                            <!-- BUTTON -->
                            <div class="d-flex justify-content-end mt-4">

                                <a href="data_mobil_admin.php" class="btn btn-secondary mr-2">
                                    <i class="fas fa-times"></i>
                                    Batal
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    Simpan
                                </button>

                            </div>

                        </form>

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

</body>
</html>
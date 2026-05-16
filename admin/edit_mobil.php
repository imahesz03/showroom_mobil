<?php
session_start();
include "../config/koneksi.php";

if ($_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit;
}

$id = $_GET['id'];

$query = mysqli_query($koneksi, "SELECT * FROM mobil WHERE id_mobil='$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data mobil tidak ditemukan'); window.location='data_mobil_admin.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Mobil</title>

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
                        <h1 class="h3 mb-0 text-gray-800">Edit Mobil</h1>
                        <p class="mb-0 text-gray-600">
                            Update data mobil showroom.
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
                            Form Edit Mobil
                        </h6>
                    </div>

                    <div class="card-body">

                        <form action="proses_edit.php" method="POST" enctype="multipart/form-data">

                            <input type="hidden" name="id_mobil" value="<?= $data['id_mobil']; ?>">
                            <input type="hidden" name="foto_lama" value="<?= htmlspecialchars($data['foto']); ?>">

                            <div class="form-group">
                                <label>Nama Mobil</label>
                                <input type="text"
                                       name="nama_mobil"
                                       class="form-control"
                                       value="<?= htmlspecialchars($data['nama_mobil']); ?>"
                                       placeholder="Contoh: Toyota Avanza"
                                       required>
                            </div>

                            <div class="form-group">
                                <label>Deskripsi Mobil</label>
                                <textarea name="deskripsi"
                                          class="form-control"
                                          rows="4"
                                          placeholder="Tulis deskripsi mobil..."
                                          required><?= htmlspecialchars($data['deskripsi']); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Tahun Mobil</label>
                                <input type="number"
                                       name="tahun"
                                       class="form-control"
                                       value="<?= htmlspecialchars($data['tahun']); ?>"
                                       min="1900"
                                       max="2099"
                                       required>
                            </div>

                            <div class="form-group">
                                <label>Harga</label>
                                <input type="number"
                                       name="harga"
                                       class="form-control"
                                       value="<?= htmlspecialchars($data['harga']); ?>"
                                       placeholder="Contoh: 150000000"
                                       required>
                            </div>

                            <div class="form-group">
                                <label>Stok</label>
                                <input type="number"
                                       name="stok"
                                       class="form-control"
                                       value="<?= htmlspecialchars($data['stok']); ?>"
                                       min="0"
                                       required>
                            </div>

                            <div class="form-group">
                                <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="tersedia" <?= ($data['status'] == 'tersedia') ? 'selected' : ''; ?>>
                                    Tersedia
                                </option>

                                <option value="terjual" <?= ($data['status'] == 'terjual') ? 'selected' : ''; ?>>
                                    Terjual
                                </option>
                            </select>
                            </div>

                            <div class="form-group">
                                <label>Foto Saat Ini</label>
                                <div class="mb-3">
                                    <?php if (!empty($data['foto'])) { ?>
                                        <img src="../uploads/<?= htmlspecialchars($data['foto']); ?>"
                                             alt="Foto Mobil"
                                             class="img-thumbnail"
                                             style="max-width: 280px; height: 170px; object-fit: cover;">
                                    <?php } else { ?>
                                        <div class="text-muted">
                                            Foto belum tersedia.
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Ganti Foto Mobil</label>
                                <input type="file"
                                       name="foto"
                                       class="form-control-file"
                                       accept="image/*">
                                <small class="form-text text-muted">
                                    Kosongkan jika tidak ingin mengganti foto.
                                </small>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <a href="data_mobil_admin.php" class="btn btn-secondary mr-2">
                                    <i class="fas fa-times"></i>
                                    Batal
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    Update Mobil
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
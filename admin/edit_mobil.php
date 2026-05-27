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
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold tracking-tight">Edit Mobil</h1>
                        <p class="mb-0 text-muted small">
                            Perbarui rincian spesifikasi, harga, status, dan dokumentasi foto kendaraan.
                        </p>
                    </div>

                    <a href="data_mobil_admin.php" class="btn btn-light btn-icon-split border shadow-sm mt-3 mt-sm-0">
                        <span class="icon text-gray-600 bg-light">
                            <i class="fas fa-arrow-left"></i>
                        </span>
                        <span class="text font-weight-bold text-gray-700">Kembali</span>
                    </a>
                </div>

                <div class="card shadow border-0 mb-4 rounded-lg">

                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-light p-2 rounded mr-3">
                                <i class="fas fa-edit text-primary"></i>
                            </div>
                            <div>
                                <h6 class="m-0 font-weight-bold text-gray-800">Form Perubahan Data</h6>
                                <p class="m-0 text-muted small">ID Mobil: #<?= $data['id_mobil']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">

                        <form action="proses_edit.php" method="POST" enctype="multipart/form-data">

                            <input type="hidden" name="id_mobil" value="<?= $data['id_mobil']; ?>">
                            <input type="hidden" name="foto_lama" value="<?= htmlspecialchars($data['foto']); ?>">

                            <div class="row">
                                <div class="col-lg-7 border-right-lg pr-lg-4">
                                    
                                    <div class="form-group mb-4">
                                        <label class="text-xs font-weight-bold text-gray-700 text-uppercase tracking-wider">Nama Kendaraan</label>
                                        <input type="text"
                                               name="nama_mobil"
                                               class="form-control bg-light border-0 py-4 font-weight-bold text-gray-800"
                                               value="<?= htmlspecialchars($data['nama_mobil']); ?>"
                                               placeholder="Contoh: Honda Civic Type R"
                                               required>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="text-xs font-weight-bold text-gray-700 text-uppercase tracking-wider">Tahun Perakitan</label>
                                                <input type="number"
                                                       name="tahun"
                                                       class="form-control bg-light border-0 py-4 font-weight-bold text-gray-800"
                                                       value="<?= htmlspecialchars($data['tahun']); ?>"
                                                       min="1900"
                                                       max="2099"
                                                       required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="text-xs font-weight-bold text-gray-700 text-uppercase tracking-wider">Persediaan Stok</label>
                                                <input type="number"
                                                       name="stok"
                                                       class="form-control bg-light border-0 py-4 font-weight-bold text-gray-800"
                                                       value="<?= htmlspecialchars($data['stok']); ?>"
                                                       min="0"
                                                       required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="text-xs font-weight-bold text-gray-700 text-uppercase tracking-wider">Nilai Jual (Rp)</label>
                                                <input type="number"
                                                       name="harga"
                                                       class="form-control bg-light border-0 py-4 font-weight-bold text-primary"
                                                       value="<?= htmlspecialchars($data['harga']); ?>"
                                                       placeholder="Contoh: 250000000"
                                                       step="any"
                                                       required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="text-xs font-weight-bold text-gray-700 text-uppercase tracking-wider">Status Unit</label>
                                                <select name="status" class="form-control bg-light border-0 custom-select font-weight-bold text-gray-800" style="height: calc(1.5em + .75rem + 14px);" required>
                                                    <option value="tersedia" <?= ($data['status'] == 'tersedia') ? 'selected' : ''; ?>>Tersedia</option>
                                                    <option value="terjual" <?= ($data['status'] == 'terjual') ? 'selected' : ''; ?>>Terjual</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="text-xs font-weight-bold text-gray-700 text-uppercase tracking-wider">Deskripsi & Spesifikasi</label>
                                        <textarea name="deskripsi"
                                                  class="form-control bg-light border-0 text-gray-800"
                                                  rows="4"
                                                  placeholder="Jelaskan kondisi mesin, kelengkapan surat, maupun fitur tambahan..."
                                                  required><?= htmlspecialchars($data['deskripsi']); ?></textarea>
                                    </div>

                                </div>

                                <div class="col-lg-5 pl-lg-4 mt-4 mt-lg-0">
                                    
                                    <div class="form-group mb-4">
                                        <label class="text-xs font-weight-bold text-gray-700 text-uppercase tracking-wider">Preview Gambar Saat Ini</label>
                                        <div class="p-2 bg-light rounded text-center border">
                                            <?php if (!empty($data['foto'])) { ?>
                                                <img src="../uploads/<?= htmlspecialchars($data['foto']); ?>"
                                                     alt="Foto Mobil"
                                                     class="img-fluid rounded shadow-sm"
                                                     style="max-height: 230px; width: 100%; object-fit: cover;">
                                            <?php } else { ?>
                                                <div class="text-muted py-5">
                                                    <i class="fas fa-image fa-3x text-gray-300 mb-2"></i>
                                                    <p class="small mb-0">Berkas gambar belum diunggah</p>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="text-xs font-weight-bold text-gray-700 text-uppercase tracking-wider">Unggah Foto Baru</label>
                                        <div class="custom-file">
                                            <input type="file"
                                                   name="foto"
                                                   class="custom-file-input"
                                                   id="customFile"
                                                   accept="image/*">
                                            <label class="custom-file-label bg-light border-0" for="customFile">Pilih berkas gambar...</label>
                                        </div>
                                        <small class="form-text text-muted small mt-2">
                                            <i class="fas fa-info-circle mr-1"></i> Biarkan kosong jika tidak bermaksud mengubah gambar utama.
                                        </small>
                                    </div>

                                </div>
                            </div>

                            <div class="card-footer bg-white border-top px-0 pb-0 pt-4 d-flex justify-content-end">
                                <a href="data_mobil_admin.php" class="btn btn-light font-weight-bold text-gray-700 border px-4 mr-2">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary btn-icon-split shadow-sm">
                                    <span class="icon text-white-50">
                                        <i class="fas fa-save"></i>
                                    </span>
                                    <span class="text font-weight-bold">Simpan Perubahan</span>
                                </button>
                            </div>

                        </form>

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
document.getElementById('customFile').addEventListener('change', function(e) {
    var fileName = e.target.files[0].name;
    var nextSibling = e.target.nextElementSibling;
    nextSibling.innerText = fileName;
});
</script>

</body>
</html>
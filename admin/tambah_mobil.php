<?php
session_start();
include "../config/koneksi.php";

if ($_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit;
}

$query_penjual = mysqli_query($koneksi, "SELECT id_penjual, nama FROM penjual ORDER BY nama ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Mobil</title>

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
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold tracking-tight">Tambah Mobil</h1>
                        <p class="mb-0 text-muted small">
                            Daftarkan unit kendaraan baru ke dalam sistem inventaris showroom.
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
                                <i class="fas fa-plus text-primary"></i>
                            </div>
                            <div>
                                <h6 class="m-0 font-weight-bold text-gray-800">Form Unit Baru</h6>
                                <p class="m-0 text-muted small">Pastikan data spesifikasi kendaraan telah sesuai</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">

                        <form action="proses_tambah_mobil.php" method="POST" enctype="multipart/form-data">

                            <div class="row">
                                <div class="col-lg-7 border-right-lg pr-lg-4">
                                    
                                    <div class="form-group mb-4">
                                        <label class="text-xs font-weight-bold text-gray-700 text-uppercase tracking-wider">Pilih Pemilik / Penjual</label>
                                        <select name="id_penjual" class="form-control bg-light border-0 text-gray-800 font-weight-bold" style="height: auto; padding: 12px 20px;" required>
                                            <option value="" disabled selected>-- Pilih Penjual --</option>
                                            <?php while ($penjual = mysqli_fetch_assoc($query_penjual)) : ?>
                                                <option value="<?php echo $penjual['id_penjual']; ?>">
                                                    <?php echo $penjual['nama']; ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group mb-4">
                                        <label class="text-xs font-weight-bold text-gray-700 text-uppercase tracking-wider">Nama Kendaraan</label>
                                        <input type="text"
                                               name="nama_mobil"
                                               class="form-control bg-light border-0 py-4 font-weight-bold text-gray-800"
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
                                                       placeholder="Contoh: 2023"
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
                                                       placeholder="Jumlah unit"
                                                       min="0"
                                                       required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="text-xs font-weight-bold text-gray-700 text-uppercase tracking-wider">Nilai Jual (Rp)</label>
                                        <input type="number"
                                               name="harga"
                                               class="form-control bg-light border-0 py-4 font-weight-bold text-primary"
                                               placeholder="Contoh: 250000000"
                                               required>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="text-xs font-weight-bold text-gray-700 text-uppercase tracking-wider">Deskripsi & Spesifikasi</label>
                                        <textarea name="deskripsi"
                                                  class="form-control bg-light border-0 text-gray-800"
                                                  rows="4"
                                                  placeholder="Jelaskan kondisi mesin, kelengkapan surat, maupun fitur tambahan..."
                                                  required></textarea>
                                    </div>

                                </div>

                                <div class="col-lg-5 pl-lg-4 mt-4 mt-lg-0">
                                    
                                    <div class="form-group mb-4">
                                        <label class="text-xs font-weight-bold text-gray-700 text-uppercase tracking-wider">Pratinjau Gambar</label>
                                        <div class="p-2 bg-light rounded text-center border d-flex align-items-center justify-content-center" style="min-height: 248px;">
                                            <div id="preview-placeholder" class="text-muted py-5">
                                                <i class="fas fa-image fa-3x text-gray-300 mb-2"></i>
                                                <p class="small mb-0">Belum ada foto yang dipilih</p>
                                            </div>
                                            <img id="image-preview" src="#" alt="Preview" class="img-fluid rounded shadow-sm d-none" style="max-height: 230px; width: 100%; object-fit: cover;">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="text-xs font-weight-bold text-gray-700 text-uppercase tracking-wider">Unggah Foto Utama</label>
                                        <div class="custom-file">
                                            <input type="file"
                                                   name="foto"
                                                   class="custom-file-input"
                                                   id="customFile"
                                                   accept="image/*"
                                                   required>
                                            <label class="custom-file-label bg-light border-0" for="customFile">Pilih berkas gambar...</label>
                                        </div>
                                        <small class="form-text text-muted small mt-2">
                                            <i class="fas fa-info-circle mr-1"></i> Gunakan gambar beresolusi tinggi dengan format JPG, JPEG, atau PNG.
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
                                    <span class="text font-weight-bold">Simpan Unit</span>
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

    // Live Preview Logic
    var reader = new FileReader();
    reader.onload = function(event) {
        var preview = document.getElementById('image-preview');
        var placeholder = document.getElementById('preview-placeholder');
        
        preview.src = event.target.result;
        preview.classList.remove('d-none');
        placeholder.classList.add('d-none');
    }
    if(e.target.files[0]){
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>

</body>
</html>
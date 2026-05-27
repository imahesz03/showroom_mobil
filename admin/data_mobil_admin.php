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
    
    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
    
    <style>
        .car-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .car-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }

        /* FIX: Tinggi seragam agar layout tidak berantakan */
        .card-img-container {
            position: relative;
            height: 210px; /* Samakan dengan lihat_mobil.php */
            background-color: #f8f9fa;
            overflow: hidden;
        }

        .card-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Kunci agar gambar tidak "melar" */
            cursor: pointer;
            transition: transform 0.3s;
        }

        .badge-status {
            position: absolute;
            top: 15px;
            left: 15px;
            z-index: 1;
        }
    </style>

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
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold tracking-tight">Data Mobil</h1>
                        <p class="mb-0 text-muted small">
                            Halaman untuk mengelola data mobil showroom, mulai dari melihat, menambah, hingga mengubah informasi kendaraan.
                        </p>
                    </div>

                    <a href="tambah_mobil.php" class="btn btn-primary btn-icon-split shadow-sm mt-3 mt-sm-0">
                        <span class="icon text-white-50">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span class="text font-weight-bold">Tambah Mobil</span>
                    </a>
                </div>

                <div class="card shadow border-0 mb-4 rounded-lg">
                    <div class="card-body py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                        <div class="d-flex align-items-center mb-3 mb-md-0">
                            <div class="bg-light p-2 rounded mr-3">
                                <i class="fas fa-th-large text-primary"></i>
                            </div>
                            <div>
                                <h6 class="m-0 font-weight-bold text-gray-800">Katalog Inventaris</h6>
                                <p class="m-0 text-muted small">Daftar koleksi mobil showroom yang ditampilkan dengan mudah dikelola.</p>
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
                                   placeholder="Cari nama, harga, status..."
                                   onkeyup="filterCards()">
                        </div>
                    </div>
                </div>

                <?php if (mysqli_num_rows($query) > 0) { ?>
                    
                    <div class="row" id="carGrid">
                        <?php while ($data = mysqli_fetch_assoc($query)) { ?>
                            
                            <div class="col-xl-4 col-md-6 mb-4 car-item-card">
                                <div class="card car-card shadow-sm h-100">
                                    
                                    <div class="card-img-container">
                                        <?php if (!empty($data['foto'])) { ?>
                                            <img src="../uploads/<?= htmlspecialchars($data['foto']); ?>" 
                                                 alt="Foto Mobil"
                                                 onclick="previewImage('../uploads/<?= htmlspecialchars($data['foto']); ?>', '<?= htmlspecialchars($data['nama_mobil']); ?>')">
                                        <?php } else { ?>
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light text-muted small">
                                                <i class="fas fa-image mr-2"></i> No Image
                                            </div>
                                        <?php } ?>

                                        <?php if ($data['status'] == "tersedia") { ?>
                                            <span class="badge badge-status bg-white text-success border border-success">Tersedia</span>
                                        <?php } else { ?>
                                            <span class="badge badge-status bg-white text-danger border border-danger">Tidak Tersedia</span>
                                        <?php } ?>
                                    </div>

                                    <div class="card-body d-flex flex-column pb-3">
                                        <h5 class="font-weight-bold text-gray-800 mb-1 text-truncate-title"><?= htmlspecialchars($data['nama_mobil']); ?></h5>
                                        
                                        <div class="text-primary font-weight-bold mb-2 text-lg">
                                            Rp <?= number_format($data['harga'], 0, ',', '.'); ?>
                                        </div>
                                        
                                        <div class="d-flex text-muted small mb-3">
                                            <div class="mr-3"><i class="fas fa-calendar-alt mr-1"></i> Tahun <?= htmlspecialchars($data['tahun'] ?? '-'); ?></div>
                                            <div><i class="fas fa-box mr-1"></i> Stok <?= htmlspecialchars($data['stok']); ?></div>
                                        </div>
                                        
                                        <p class="text-muted small text-truncate mb-4" style="max-height: 40px;">
                                            <?= htmlspecialchars($data['deskripsi'] ?? 'Tidak ada deskripsi kendaraan.'); ?>
                                        </p>
                                        
                                        <div class="row no-gutters mt-auto border-top pt-3">
                                            <div class="col-6 pr-1">
                                                <a href="edit_mobil.php?id=<?= $data['id_mobil']; ?>" 
                                                   class="btn btn-block btn-light text-warning border font-weight-bold btn-sm shadow-sm py-2">
                                                    <i class="fas fa-edit mr-1"></i> Edit
                                                </a>
                                            </div>
                                            <div class="col-6 pl-1">
                                                <a href="hapus_mobil_admin.php?id=<?= $data['id_mobil']; ?>" 
                                                   class="btn btn-block btn-light text-danger border font-weight-bold btn-sm shadow-sm py-2"
                                                   onclick="return confirm('Apakah Anda yakin ingin menghapus mobil ini?')">
                                                    <i class="fas fa-trash mr-1"></i> Hapus
                                                </a>
                                            </div>
                                        </div>

                                        <span class="d-none searchable-data"><?= strtolower($data['nama_mobil'] . ' ' . $data['harga'] . ' ' . $data['status']); ?></span>
                                    </div>

                                </div>
                            </div>

                        <?php } ?>
                    </div>

                    <div id="no-result" class="text-center text-muted py-5 mx-3" style="display:none;">
                        <div class="p-3 bg-light d-inline-block rounded-circle mb-3">
                            <i class="fas fa-search-minus fa-2x text-gray-400"></i>
                        </div>
                        <h6 class="font-weight-bold text-gray-800 mb-1">Data tidak cocok</h6>
                        <p class="small text-muted mb-0">Periksa kembali ejaan atau kata kunci pencarian Anda.</p>
                    </div>

                <?php } else { ?>

                    <div class="card shadow border-0 rounded-lg">
                        <div class="card-body text-center py-5 my-5">
                            <div class="p-4 bg-light d-inline-block rounded-circle mb-4">
                                <i class="fas fa-box-open fa-3x text-gray-300"></i>
                            </div>
                            <h5 class="text-gray-800 font-weight-bold mb-1">Gudang Kosong</h5>
                            <p class="text-muted small mb-0">Belum ada data unit kendaraan yang terdaftar di dalam database showroom.</p>
                        </div>
                    </div>

                <?php } ?>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-white border-0 py-3">
                <h5 class="modal-title font-weight-bold text-gray-800" id="modalTitle">Nama Mobil</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-2 text-center bg-light">
                <img id="modalImage" src="" alt="Preview Mobil" class="img-fluid rounded" style="max-height: 520px; width: 100%; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sb-admin-2.min.js"></script>

<script>
// Fungsi trigger klik foto untuk preview popup besar
function previewImage(imageSrc, carName) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('modalTitle').innerText = carName;
    $('#imageModal').modal('show');
}

// Live Search untuk sistem Grid Card
function filterCards() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const grid = document.getElementById('carGrid');
    if (!grid) return;
    
    const cards = grid.getElementsByClassName('car-item-card');
    const noResult = document.getElementById('no-result');
    let visibleCount = 0;

    for (let i = 0; i < cards.length; i++) {
        const searchData = cards[i].getElementsByClassName('searchable-data')[0].innerText;
        
        if (searchData.includes(filter)) {
            cards[i].style.display = '';
            visibleCount++;
        } else {
            cards[i].style.display = 'none';
        }
    }

    if (noResult) {
        noResult.style.display = visibleCount === 0 ? 'block' : 'none';
    }
}
</script>

</body>
</html>
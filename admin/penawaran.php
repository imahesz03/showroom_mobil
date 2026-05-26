<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit;
}

function rupiah($angka)
{
    return "Rp " . number_format((float)$angka, 0, ',', '.');
}

// Skema Migrasi Otomatis Kolom Tabel Penawaran
$columns = [
    "status"            => "ALTER TABLE penawaran ADD status ENUM('menunggu','diterima','ditolak') DEFAULT 'menunggu'",
    "metode_pembayaran" => "ALTER TABLE penawaran ADD metode_pembayaran ENUM('tunai','transfer') DEFAULT NULL",
    "bukti_pembayaran"  => "ALTER TABLE penawaran ADD bukti_pembayaran VARCHAR(255) DEFAULT NULL",
    "tanggal_keputusan" => "ALTER TABLE penawaran ADD tanggal_keputusan DATETIME DEFAULT NULL",
    "catatan_admin"     => "ALTER TABLE penawaran ADD catatan_admin TEXT DEFAULT NULL",
    "catatan"           => "ALTER TABLE penawaran ADD catatan TEXT DEFAULT NULL"
];

foreach ($columns as $col => $sql) {
    $cek = mysqli_query($koneksi, "SHOW COLUMNS FROM penawaran LIKE '$col'");
    if (mysqli_num_rows($cek) == 0) {
        mysqli_query($koneksi, $sql);
    }
}

// Retrieve Data Penawaran Mobil (Optimized Join)
$data = mysqli_query($koneksi, "
    SELECT 
        pn.*,
        p.nama AS nama_penjual,
        p.no_hp AS no_hp_penjual,
        m.nama_mobil,
        m.tahun,
        m.harga AS harga_asli,
        m.foto
    FROM penawaran pn
    LEFT JOIN penjual p ON pn.id_penjual = p.id_penjual
    LEFT JOIN mobil m ON pn.id_mobil = m.id_mobil
    ORDER BY pn.id_penawaran DESC
");

if (!$data) {
    die("Query penawaran error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penawaran Mobil Admin - Galaxy Showroom</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>

<body id="page-top" class="role-admin page-table">

<div id="wrapper">

    <?php include "../includes/sidebar_admin.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php 
            $pageTitle = "Penawaran Mobil";
            include "../includes/topbar.php"; 
            ?>

            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h1 class="h3 mb-1 text-gray-800 font-weight-bold tracking-tight">Penawaran Mobil</h1>
                        <p class="mb-0 text-muted small">
                            Ringkasan transaksi penawaran masuk, verifikasi berkas unit, dan riwayat beli dari penjual.
                        </p>
                    </div>
                </div>

                <?php if (isset($_SESSION['success'])) : ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-check-circle mr-2"></i><?= $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])) : ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i><?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <div class="card shadow border-0 mb-4 rounded-lg">

                    <div class="card-header bg-white border-bottom py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                        <div class="d-flex align-items-center mb-3 mb-md-0">
                            <div class="bg-light p-2 rounded mr-3">
                                <i class="fas fa-gavel text-primary"></i>
                            </div>
                            <div>
                                <h6 class="m-0 font-weight-bold text-gray-800">Data Penawaran Unit</h6>
                                <p class="m-0 text-muted small">Kelola data pengajuan masuk, validasi berkas, dan bukti pembayaran showroom</p>
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
                                   placeholder="Cari penjual, mobil, status..."
                                   onkeyup="filterTable()">
                        </div>
                    </div>

                    <div class="card-body p-4">

                        <?php if (mysqli_num_rows($data) > 0) : ?>

                            <div class="table-responsive">

                                <table class="table table-bordered table-hover mb-0" id="penawaranTable" width="100%" cellspacing="0">

                                    <thead class="bg-light text-dark text-uppercase small font-weight-bold">
                                        <tr>
                                            <th class="text-center py-3" style="width: 5%;">No</th>
                                            <th class="py-3" style="width: 23%;">Penjual</th>
                                            <th class="py-3" style="width: 25%;">Mobil</th>
                                            <th class="text-right py-3" style="width: 17%;">Nilai Tawar</th>
                                            <th class="text-center py-3" style="width: 15%;">Status</th>
                                            <th class="text-center py-3" style="width: 15%;">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody class="text-gray-700">

                                    <?php 
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($data)) : 

                                        $status = strtolower($row['status'] ?? 'menunggu');
                                        $badgeStatus = "secondary";
                                        $textStatus = ucfirst($status);

                                        if ($status == "menunggu") {
                                            $badgeStatus = "warning";
                                            $textStatus = "Menunggu";
                                        } elseif ($status == "diterima") {
                                            $badgeStatus = "success";
                                            $textStatus = "Diterima";
                                        } elseif ($status == "ditolak") {
                                            $badgeStatus = "danger";
                                            $textStatus = "Ditolak";
                                        }

                                        $hargaAsli = (float)$row['harga_asli'];
                                        $hargaTawar = (float)$row['harga_tawar'];
                                        $metode = $row['metode_pembayaran'] ?? '';
                                        
                                        $bolehCetak = ($status == 'diterima' && ($metode == 'tunai' || ($metode == 'transfer' && !empty($row['bukti_pembayaran']))));
                                    ?>

                                        <tr class="align-middle">
                                            <td class="text-center align-middle font-weight-bold text-muted">
                                                <?= $no++; ?>
                                            </td>

                                            <td class="align-middle">
                                                <div class="font-weight-bold text-gray-800 mb-0">
                                                    <?= htmlspecialchars($row['nama_penjual'] ?? '-'); ?>
                                                </div>
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-phone-alt mr-1" style="font-size: 85%;"></i><?= htmlspecialchars($row['no_hp_penjual'] ?? '-'); ?>
                                                </small>
                                            </td>

                                            <td class="align-middle">
                                                <div class="font-weight-bold text-gray-800 mb-0">
                                                    <?= htmlspecialchars($row['nama_mobil'] ?? '-'); ?>
                                                </div>
                                                <small class="text-muted d-block mb-1">
                                                    Tahun <?= htmlspecialchars($row['tahun'] ?? '-'); ?>
                                                </small>
                                                <div class="small text-muted font-italic" style="font-size: 85%;">
                                                    Harga Buka: <?= rupiah($hargaAsli); ?>
                                                </div>
                                            </td>

                                            <td class="align-middle text-right">
                                                <div class="font-weight-bold text-primary mb-0">
                                                    <?= rupiah($hargaTawar); ?>
                                                </div>
                                                <small class="text-muted d-block text-capitalize" style="font-size: 85%;">
                                                    Metode: <?= !empty($metode) ? htmlspecialchars($metode) : '-'; ?>
                                                </small>
                                            </td>

                                            <td class="text-center align-middle">
                                                <span class="badge badge-pill badge-<?= $badgeStatus; ?> px-3 py-2 font-weight-bold text-uppercase" style="font-size: 75%;">
                                                    <?= $textStatus; ?>
                                                </span>
                                            </td>

                                            <td class="text-center align-middle">
                                                <div class="dropdown">
                                                    <button class="btn btn-white border text-gray-800 btn-sm dropdown-toggle shadow-sm px-3 font-weight-bold"
                                                            type="button"
                                                            id="dropdownAksi<?= $row['id_penawaran']; ?>"
                                                            data-toggle="dropdown"
                                                            aria-haspopup="true"
                                                            aria-expanded="false">
                                                        Aksi
                                                    </button>

                                                    <div class="dropdown-menu dropdown-menu-right shadow border-0 rounded-lg"
                                                         aria-labelledby="dropdownAksi<?= $row['id_penawaran']; ?>">

                                                        <?php if ($status == 'menunggu') : ?>
                                                            <a href="#" class="dropdown-item py-2 text-sm text-primary font-weight-bold" data-toggle="modal" data-target="#modalAksi<?= $row['id_penawaran']; ?>">
                                                                <i class="fas fa-gavel fa-sm fa-fw mr-2"></i> Proses Unit
                                                            </a>
                                                        <?php endif; ?>

                                                        <?php if ($metode == 'transfer' && !empty($row['bukti_pembayaran'])) : ?>
                                                            <a href="../uploads/<?= htmlspecialchars($row['bukti_pembayaran']); ?>"
                                                               target="_blank"
                                                               class="dropdown-item py-2 text-sm">
                                                                <i class="fas fa-image fa-sm fa-fw mr-2 text-warning"></i> Bukti Bayar
                                                            </a>
                                                        <?php endif; ?>

                                                        <?php if ($bolehCetak) : ?>
                                                            <div class="dropdown-divider my-1"></div>
                                                            <a href="kwitansi_penawaran.php?id=<?= $row['id_penawaran']; ?>"
                                                               target="_blank"
                                                               class="dropdown-item py-2 text-sm text-success font-weight-bold">
                                                                <i class="fas fa-print fa-sm fa-fw mr-2"></i> Cetak Kwitansi
                                                            </a>
                                                        <?php endif; ?>

                                                        <?php if ($status == "ditolak") : ?>
                                                            <div class="dropdown-divider my-1"></div>
                                                            <span class="dropdown-item py-2 text-sm text-danger disabled font-weight-bold">
                                                                <i class="fas fa-times-circle fa-sm fa-fw mr-2"></i> Ajuan Ditolak
                                                            </span>
                                                        <?php endif; ?>

                                                    </div>
                                                </div>

                                                <div class="modal fade" id="modalAksi<?= $row['id_penawaran']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content border-0 shadow-lg rounded-lg">
                                                            <div class="modal-header bg-light border-bottom">
                                                                <h5 class="modal-title font-weight-bold text-gray-800" style="font-size: 1rem;">Validasi Penawaran Masuk</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body text-left p-4">
                                                                <div class="bg-light rounded p-3 text-sm text-gray-700 mb-3 border">
                                                                    <strong class="text-primary text-uppercase d-block mb-1" style="letter-spacing: 0.5px; font-size: 80%;">Ringkasan Unit</strong>
                                                                    <strong>Mobil:</strong> <?= htmlspecialchars($row['nama_mobil']); ?> <br>
                                                                    <strong>Harga Ajuan:</strong> <span class="font-weight-bold text-dark"><?= rupiah($row['harga_tawar']); ?></span>
                                                                </div>
                                                                
                                                                <form method="POST" action="proses_penawaran.php" enctype="multipart/form-data">
                                                                    <input type="hidden" name="id_penawaran" value="<?= $row['id_penawaran']; ?>">
                                                                    
                                                                    <div class="form-group mb-3">
                                                                        <label class="text-xs font-weight-bold text-uppercase text-gray-600 mb-1">Keputusan</label>
                                                                        <select name="keputusan_status" class="form-control form-control-sm keputusanSelect" required>
                                                                            <option value="diterima">Setujui & Terima Penawaran</option>
                                                                            <option value="tolak">Tolak Ajuan Penawaran</option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="wrapper-pembayaran">
                                                                        <div class="form-group mb-3">
                                                                            <label class="text-xs font-weight-bold text-uppercase text-gray-600 mb-1">Metode Pembayaran</label>
                                                                            <select name="metode_pembayaran" class="form-control form-control-sm modalMetodeBayar">
                                                                                <option value="tunai">Cash / Tunai</option>
                                                                                <option value="transfer">Transfer Bank</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group mb-3 modalBuktiWrapper" style="display:none;">
                                                                            <label class="text-xs font-weight-bold text-uppercase text-danger mb-1">Upload Bukti Transfer Showroom</label>
                                                                            <input type="file" name="bukti_pembayaran" class="form-control-file modalBuktiBayar" accept="image/jpeg,image/png,image/webp">
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group mb-4">
                                                                        <label class="text-xs font-weight-bold text-uppercase text-gray-600 mb-1">Catatan Review Admin</label>
                                                                        <textarea name="catatan_admin" class="form-control form-control-sm" rows="2" placeholder="Berikan catatan internal Showroom..."></textarea>
                                                                    </div>

                                                                    <div class="text-right border-top pt-3">
                                                                        <button type="button" class="btn btn-sm btn-light border px-3" data-dismiss="modal">Batal</button>
                                                                        <button type="submit" name="proses_verifikasi" class="btn btn-sm btn-primary px-4 shadow-sm font-weight-bold" onclick="return confirm('Simpan hasil keputusan transaksi penawaran ini?')">
                                                                            <i class="fas fa-save mr-1"></i> Simpan
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                    <?php endwhile; ?>

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

                        <?php else : ?>

                            <div class="text-center py-5 my-3">
                                <div class="p-4 bg-light d-inline-block rounded-circle mb-4">
                                    <i class="fas fa-gavel fa-3x text-gray-300"></i>
                                </div>
                                <h5 class="text-gray-800 font-weight-bold mb-1">Belum Ada Penawaran</h5>
                                <p class="text-muted small mb-0">
                                    Data rekaman penawaran mobil masuk dari pihak penjual belum tersedia.
                                </p>
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
// Fungsi live search tabel
function filterTable() {
    const input = document.getElementById("searchInput");
    const filter = input.value.toLowerCase();
    const table = document.getElementById("penawaranTable");
    const noResult = document.getElementById("no-result");

    if (!table) return;

    const tr = table.getElementsByTagName("tr");
    let visibleCount = 0;

    for (let i = 1; i < tr.length; i++) {
        const text = tr[i].innerText.toLowerCase();
        
        if (text.includes(filter)) {
            tr[i].style.display = "";
            visibleCount++;
        } else {
            tr[i].style.display = "none";
        }
    }

    if (noResult) {
        noResult.style.display = visibleCount === 0 ? "block" : "none";
    }
}

// Logika dinamis kondisional form input di dalam Modal Bootstrap
document.querySelectorAll(".keputusanSelect").forEach(function(select) {
    select.addEventListener("change", function() {
        let modal = this.closest(".modal-body");
        let wrapperPembayaran = modal.querySelector(".wrapper-pembayaran");
        let inputMetode = modal.querySelector(".modalMetodeBayar");
        let inputBukti = modal.querySelector(".modalBuktiBayar");

        if (this.value === "tolak") {
            wrapperPembayaran.style.display = "none";
            inputMetode.required = false;
            inputBukti.required = false;
        } else {
            wrapperPembayaran.style.display = "block";
            inputMetode.dispatchEvent(new Event('change'));
        }
    });
});

document.querySelectorAll(".modalMetodeBayar").forEach(function(select) {
    select.addEventListener("change", function() {
        let modal = this.closest(".modal-body");
        let keputusan = modal.querySelector(".keputusanSelect").value;
        let buktiWrapper = modal.querySelector(".modalBuktiWrapper");
        let inputBukti = modal.querySelector(".modalBuktiBayar");

        if (this.value === "transfer" && keputusan === "diterima") {
            buktiWrapper.style.display = "block";
            inputBukti.required = true;
        } else {
            buktiWrapper.style.display = "none";
            inputBukti.required = false;
            inputBukti.value = "";
        }
    });
});
</script>

</body>
</html>
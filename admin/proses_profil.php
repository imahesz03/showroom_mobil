<?php
// ============================================================
//  PROSES PROFIL — memproses update profil user
//  Path  : showroom_mobil/profil/proses_profil.php
//  Method: POST dari profil.php
// ============================================================

session_start();
include '../config/koneksi.php';

// ── Guard: wajib login & POST ─────────────────────────────────
if (empty($_SESSION['id_user']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../auth/login.php');
    exit;
}

$id_user = (int) $_SESSION['id_user'];
$role    = $_SESSION['role'] ?? '';

// ── Mapping tabel detail per role ────────────────────────────
$tabel_detail = [
    'admin'   => 'admin',
    'pembeli' => 'pembeli',
    'penjual' => 'penjual',
    'kurir'   => 'kurir',
];

// ── Helper: redirect dengan pesan flash ──────────────────────
function redirect_flash(string $key, string $msg): void
{
    $_SESSION[$key] = $msg;
    header('Location: profil.php');
    exit;
}


// ════════════════════════════════════════════════════════════
//  1. SANITASI INPUT
// ════════════════════════════════════════════════════════════

$username       = trim(mysqli_real_escape_string($koneksi, $_POST['username']       ?? ''));
$nama           = trim(mysqli_real_escape_string($koneksi, $_POST['nama']           ?? ''));
$no_hp          = trim(mysqli_real_escape_string($koneksi, $_POST['no_hp']          ?? ''));
$alamat         = trim(mysqli_real_escape_string($koneksi, $_POST['alamat']         ?? ''));
$password_lama  = $_POST['password_lama']  ?? '';
$password_baru  = $_POST['password_baru']  ?? '';
$password_konfirm = $_POST['password_konfirm'] ?? '';

// ── Validasi username tidak kosong ───────────────────────────
if ($username === '') {
    redirect_flash('flash_err', 'Username tidak boleh kosong.');
}

// ── Validasi username tidak duplikat (kecuali milik sendiri) ─
$cek_username = mysqli_query($koneksi,
    "SELECT id_user FROM users
     WHERE username = '$username' AND id_user != $id_user
     LIMIT 1"
);
if (mysqli_num_rows($cek_username) > 0) {
    redirect_flash('flash_err', 'Username sudah digunakan oleh user lain.');
}


// ════════════════════════════════════════════════════════════
//  2. UPLOAD FOTO PROFIL
// ════════════════════════════════════════════════════════════

$nama_foto_baru = null; // null = tidak ganti foto

if (!empty($_FILES['foto_profil']['name'])) {

    $file     = $_FILES['foto_profil'];
    $ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed  = ['jpg', 'jpeg', 'png', 'webp'];

    // Validasi tipe file
    if (!in_array($ekstensi, $allowed)) {
        redirect_flash('flash_err', 'Format foto tidak didukung. Gunakan JPG, PNG, atau WEBP.');
    }

    // Validasi ukuran (max 2 MB)
    if ($file['size'] > 2 * 1024 * 1024) {
        redirect_flash('flash_err', 'Ukuran foto maksimal 2 MB.');
    }

    // Validasi MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $allowed_mime = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($mime, $allowed_mime)) {
        redirect_flash('flash_err', 'File bukan gambar yang valid.');
    }

    // Nama file unik dengan timestamp + id_user
    $nama_foto_baru = time() . '_profil_' . $id_user . '.' . $ekstensi;
    $upload_dir     = '../uploads/';
    $upload_path    = $upload_dir . $nama_foto_baru;

    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        redirect_flash('flash_err', 'Gagal mengupload foto. Coba lagi.');
    }

    // Hapus foto lama jika ada (bukan foto bawaan / default)
    if (!empty($_SESSION['foto_profil'])) {
        $foto_lama = $upload_dir . $_SESSION['foto_profil'];
        if (file_exists($foto_lama)) {
            @unlink($foto_lama);
        }
    }

    // Simpan nama foto baru ke session (akan disimpan ke DB di step 4)
    $_SESSION['foto_profil'] = $nama_foto_baru;
}


// ════════════════════════════════════════════════════════════
//  3. UPDATE TABEL users (username)
// ════════════════════════════════════════════════════════════

$update_users = mysqli_query($koneksi,
    "UPDATE users SET username = '$username'
     WHERE id_user = $id_user"
);

if (!$update_users) {
    redirect_flash('flash_err', 'Gagal memperbarui username: ' . mysqli_error($koneksi));
}

// Update session username & nama
$_SESSION['username'] = $username;
if ($nama !== '') { $_SESSION['nama'] = $nama; }


// ════════════════════════════════════════════════════════════
//  4. UPDATE TABEL DETAIL (nama, no_hp, alamat)
// ════════════════════════════════════════════════════════════

if (isset($tabel_detail[$role]) && $nama !== '') {

    $tabel = $tabel_detail[$role];

    // Cek apakah baris detail sudah ada
    $cek = mysqli_query($koneksi,
        "SELECT id_user FROM `$tabel` WHERE id_user = $id_user LIMIT 1"
    );

    // Siapkan nilai foto: pakai yang baru kalau ada, atau tetap yang lama
    $foto_untuk_db = ($nama_foto_baru !== null)
        ? "'" . mysqli_real_escape_string($koneksi, $nama_foto_baru) . "'"
        : '`foto`'; // tidak berubah (self-reference = tetap nilai lama)

    if (mysqli_num_rows($cek) > 0) {
        // UPDATE — foto diupdate hanya jika ada foto baru
        if ($nama_foto_baru !== null) {
            $q_detail = mysqli_query($koneksi,
                "UPDATE `$tabel`
                 SET nama   = '$nama',
                     no_hp  = '$no_hp',
                     alamat = '$alamat',
                     foto   = '$nama_foto_baru'
                 WHERE id_user = $id_user"
            );
        } else {
            $q_detail = mysqli_query($koneksi,
                "UPDATE `$tabel`
                 SET nama   = '$nama',
                     no_hp  = '$no_hp',
                     alamat = '$alamat'
                 WHERE id_user = $id_user"
            );
        }
    } else {
        // INSERT (data detail belum pernah dibuat)
        $foto_insert = $nama_foto_baru ?? '';
        $q_detail = mysqli_query($koneksi,
            "INSERT INTO `$tabel` (id_user, nama, no_hp, alamat, foto)
             VALUES ($id_user, '$nama', '$no_hp', '$alamat', '$foto_insert')"
        );
    }

    if (!$q_detail) {
        redirect_flash('flash_err', 'Gagal memperbarui data diri: ' . mysqli_error($koneksi));
    }
}


// ════════════════════════════════════════════════════════════
//  5. GANTI PASSWORD (opsional)
// ════════════════════════════════════════════════════════════

if ($password_lama !== '' || $password_baru !== '' || $password_konfirm !== '') {

    // Semua kolom password wajib diisi
    if ($password_lama === '') {
        redirect_flash('flash_err', 'Masukkan password lama untuk mengganti password.');
    }
    if ($password_baru === '') {
        redirect_flash('flash_err', 'Password baru tidak boleh kosong.');
    }
    if (strlen($password_baru) < 6) {
        redirect_flash('flash_err', 'Password baru minimal 6 karakter.');
    }
    if ($password_baru !== $password_konfirm) {
        redirect_flash('flash_err', 'Konfirmasi password tidak cocok.');
    }

    // Ambil hash password lama dari database
    $q_pw = mysqli_query($koneksi,
        "SELECT password FROM users WHERE id_user = $id_user LIMIT 1"
    );
    $row_pw = mysqli_fetch_assoc($q_pw);

    if (!password_verify($password_lama, $row_pw['password'])) {
        redirect_flash('flash_err', 'Password lama tidak sesuai.');
    }

    // Hash password baru
    $hash_baru = password_hash($password_baru, PASSWORD_BCRYPT);
    $q_update_pw = mysqli_query($koneksi,
        "UPDATE users SET password = '$hash_baru'
         WHERE id_user = $id_user"
    );

    if (!$q_update_pw) {
        redirect_flash('flash_err', 'Gagal memperbarui password: ' . mysqli_error($koneksi));
    }
}


// ════════════════════════════════════════════════════════════
//  6. SELESAI — redirect dengan pesan sukses
// ════════════════════════════════════════════════════════════

redirect_flash('flash_ok', 'Profil berhasil diperbarui!');
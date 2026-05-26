<?php
session_start();
// Sesuaikan path koneksi jika perlu
include "../config/koneksi.php";

// Verifikasi akses admin
if ($_SESSION['role'] != "admin") {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Menangkap data dari form tambah_mobil.php
    $id_penjual = $_POST['id_penjual'];
    $nama_mobil = $_POST['nama_mobil'];
    $tahun      = $_POST['tahun'];
    $stok       = $_POST['stok'];
    $harga      = $_POST['harga'];
    $deskripsi  = $_POST['deskripsi'];
    
    // Proses Upload Gambar
    // Lokasi folder: showroom_mobil/uploads/
    // Jika file ini di dalam folder 'admin', maka path-nya adalah '../uploads/'
    $foto     = $_FILES['foto']['name'];
    $tmp_name = $_FILES['foto']['tmp_name'];
    $path     = "../uploads/" . $foto; 

    // Cek apakah file berhasil diupload ke server
    if (move_uploaded_file($tmp_name, $path)) {
        
        // Query INSERT ke database
        // Status diset 'tersedia' secara default sesuai enum di tabel mobil
        $query = "INSERT INTO mobil (id_penjual, nama_mobil, harga, stok, status, deskripsi, tahun, foto) 
                  VALUES (?, ?, ?, ?, 'tersedia', ?, ?, ?)";
        
        $stmt = mysqli_prepare($koneksi, $query);
        
        // Bind parameter:
        // i = integer, s = string, d = double (untuk harga)
        // Urutan: id_penjual, nama_mobil, harga, stok, deskripsi, tahun, foto
        mysqli_stmt_bind_param($stmt, "isdisis", 
            $id_penjual, 
            $nama_mobil, 
            $harga, 
            $stok, 
            $deskripsi, 
            $tahun, 
            $foto
        );
        
        // Eksekusi query
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
                    alert('Data mobil berhasil ditambahkan!'); 
                    window.location.href='data_mobil_admin.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal simpan ke database: " . mysqli_error($koneksi) . "'); 
                    window.history.back();
                  </script>";
        }
        
        mysqli_stmt_close($stmt);
        
    } else {
        echo "<script>
                alert('Gagal mengunggah foto. Pastikan folder uploads ada dan memiliki izin tulis.'); 
                window.history.back();
              </script>";
    }
} else {
    // Jika akses bukan via POST
    header("Location: tambah_mobil.php");
}
?>
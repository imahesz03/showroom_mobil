<?php

include "../config/koneksi.php";

$id = $_GET['id'];

$query = mysqli_query($koneksi,
"SELECT * FROM mobil
WHERE id_mobil='$id'");

$data = mysqli_fetch_assoc($query);

?>

<!DOCTYPE html>
<html>
<head>

    <title>Edit Mobil</title>

    <meta charset="UTF-8">

    <meta name="viewport"
    content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet"
    href="../assets/style.css">

</head>
<body>

<div class="container">

    <h2>Edit Mobil</h2>

    <form action="proses_edit.php"
    method="POST"
    enctype="multipart/form-data">

        <!-- ID MOBIL -->
        <input type="hidden"
        name="id_mobil"
        value="<?= $data['id_mobil']; ?>">


        <!-- FOTO LAMA -->
        <input type="hidden"
        name="foto_lama"
        value="<?= $data['foto']; ?>">


        <!-- NAMA MOBIL -->
        <div class="input-group">

            <label>Nama Mobil</label>

            <input type="text"
            name="nama_mobil"
            value="<?= $data['nama_mobil']; ?>"
            required>

        </div>


        <!-- DESKRIPSI MOBIL -->
        <div class="input-group">

            <label>Deskripsi Mobil</label>

            <textarea
            name="deskripsi"
            rows="4"
            required><?= $data['deskripsi']; ?></textarea>

        </div>


        <!-- TAHUN MOBIL -->
        <div class="input-group">

            <label>Tahun Mobil</label>

            <input type="number"
            name="tahun"
            value="<?= $data['tahun']; ?>"
            min="1900"
            max="2099"
            required>

        </div>


        <!-- HARGA -->
        <div class="input-group">

            <label>Harga</label>

            <input type="number"
            name="harga"
            value="<?= $data['harga']; ?>"
            required>

        </div>


        <!-- STOK -->
        <div class="input-group">

            <label>Stok</label>

            <input type="number"
            name="stok"
            value="<?= $data['stok']; ?>"
            required>

        </div>


        <!-- STATUS -->
        <div class="input-group">

            <label>Status</label>

            <select name="status" required>

                <option value="tersedia"
                <?= ($data['status']=="tersedia") ? 'selected' : ''; ?>>

                    Tersedia

                </option>

                <option value="tidak tersedia"
                <?= ($data['status']=="tidak tersedia") ? 'selected' : ''; ?>>

                    Tidak Tersedia

                </option>

            </select>

        </div>


        <!-- FOTO SAAT INI -->
        <div class="input-group">

            <label>Foto Saat Ini</label>

            <br><br>

            <img 
            src="../upload/<?= $data['foto']; ?>" 
            width="100%"
            style="
            border-radius:16px;
            border:1px solid rgba(99,102,241,0.2);
            ">

        </div>


        <!-- GANTI FOTO -->
        <div class="input-group">

            <label>Ganti Foto Mobil</label>

            <input type="file"
            name="foto"
            accept="image/*">

        </div>


        <!-- BUTTON -->
        <div style="
        display:flex;
        gap:15px;
        margin-top:20px;
        ">

            <!-- KEMBALI -->
            <a href="data_mobil.php"
            style="
            flex:1;
            text-align:center;
            text-decoration:none;
            padding:14px;
            border-radius:14px;
            background:#e5e7eb;
            color:#111827;
            font-weight:600;
            transition:0.3s;
            ">

                Kembali

            </a>


            <!-- UPDATE -->
            <button type="submit"
            class="btn"
            style="flex:1;">

                Update

            </button>

        </div>

    </form>

</div>

</body>
</html>
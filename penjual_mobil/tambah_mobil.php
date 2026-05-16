<!DOCTYPE html>
<html>
<head>

    <title>Tambah Mobil</title>

    <meta charset="UTF-8">

    <meta name="viewport"
    content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet"
    href="../assets/style.css">

</head>
<body>

<div class="container">

    <h2>Tambah Mobil</h2>

    <form action="proses_tambah.php"
    method="POST"
    enctype="multipart/form-data">

        <!-- NAMA MOBIL -->
        <div class="input-group">

            <label>Nama Mobil</label>

            <input type="text"
            name="nama_mobil"
            placeholder="Masukkan nama mobil"
            required>

        </div>


        <!-- DESKRIPSI MOBIL -->
        <div class="input-group">

            <label>Deskripsi Mobil</label>

            <textarea
            name="deskripsi"
            rows="4"
            placeholder="Masukkan deskripsi mobil"
            required></textarea>

        </div>


        <!-- TAHUN MOBIL -->
        <div class="input-group">

            <label>Tahun Mobil</label>

            <input type="number"
            name="tahun"
            placeholder="Contoh: 2022"
            min="1900"
            max="2099"
            required>

        </div>


        <!-- HARGA -->
        <div class="input-group">

            <label>Harga</label>

            <input type="number"
            name="harga"
            placeholder="Masukkan harga mobil"
            required>

        </div>


        <!-- STOK -->
        <div class="input-group">

            <label>Stok</label>

            <input type="number"
            name="stok"
            placeholder="Masukkan stok mobil"
            required>

        </div>


        <!-- FOTO MOBIL -->
        <div class="input-group">

            <label>Foto Mobil</label>

            <input type="file"
            name="foto"
            accept="image/*"
            required>

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


            <!-- SIMPAN -->
            <button type="submit"
            class="btn"
            style="flex:1;">

                Simpan

            </button>

        </div>

    </form>

</div>

</body>
</html>
<?php
session_start();
include "../config/koneksi.php";

$id = $_GET['id'];

$query = mysqli_query($koneksi,
    "SELECT * FROM pembeli WHERE id_pembeli='$id'"
);

$data = mysqli_fetch_assoc($query);

?>

<!DOCTYPE html>
<html lang="id">
<head>

    <title>Edit Pembeli</title>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/style.css">

    <!-- FONT AWESOME -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #0f172a;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 820px;
            background: rgba(17, 24, 39, 0.95);
            padding: 30px;
            border-radius: 20px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            box-shadow: 0 0 24px rgba(99, 102, 241, 0.12);
            margin: 30px auto;
        }

        .title-box {
            text-align: center;
            margin-bottom: 20px;
        }

        .title-box h2 {
            color: #fff;
            font-size: 18px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .title-box p {
            color: #64748b;
            font-size: 13px;
            margin-top: 5px;
        }

        form {
            display: grid;
            gap: 18px;
            margin-top: 20px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .input-group label {
            color: #94a3b8;
            font-size: 12px;
            font-weight: 600;
        }

        .input-group input,
        .input-group textarea {
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid rgba(99, 102, 241, 0.15);
            background: #1e293b;
            color: #f1f5f9;
            outline: none;
        }

        .input-group input:focus,
        .input-group textarea:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .button-group {
            display: flex;
            gap: 10px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            border: none;
        }

        .btn-back {
            background: #1e293b;
            color: #94a3b8;
            border: 1px solid rgba(99, 102, 241, 0.15);
        }

        .btn-back:hover {
            background: #273549;
            color: #fff;
        }

        .btn-save {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
        }

        .btn-save:hover {
            transform: translateY(-2px);
        }

    </style>

</head>
<body>

<div class="sidebar-layout">

    <?php include '../includes/sidebar_admin.php'; ?>

    <div class="main-content">

        <?php $pageTitle = 'Edit Pembeli'; include '../includes/topbar.php'; ?>

        <div class="container">

            <div class="title-box">
                <h2>
                    <i class="fa-solid fa-user-pen"></i>
                    Edit Pembeli
                </h2>
                <p>Update data pembeli dengan mudah</p>
            </div>

            <form action="proses_edit_pembeli.php" method="POST">

                <input type="hidden" name="id_pembeli" value="<?= $data['id_pembeli']; ?>">

                <div class="input-group">
                    <label>Nama Pembeli</label>
                    <input type="text"
                           name="nama"
                           value="<?= htmlspecialchars($data['nama']); ?>"
                           required>
                </div>

                <div class="input-group">
                    <label>No HP</label>
                    <input type="text"
                           name="no_hp"
                           value="<?= htmlspecialchars($data['no_hp']); ?>"
                           required>
                </div>

                <div class="input-group">
                    <label>Alamat</label>
                    <textarea name="alamat"
                              required><?= htmlspecialchars($data['alamat']); ?></textarea>
                </div>

                <div class="button-group">

                    <a href="data_pembeli_admin.php" class="btn btn-back">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </a>

                    <button type="submit" class="btn btn-save">
                        <i class="fa-solid fa-floppy-disk"></i> Update
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

</body>
</html>
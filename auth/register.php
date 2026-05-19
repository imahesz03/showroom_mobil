<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Galaxy Showroom</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .register-left{
            background: linear-gradient(135deg, #4e73df, #224abe);
            color: white;
            min-height: 620px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            text-align: center;
        }

        .register-left .icon{
            font-size: 70px;
            margin-bottom: 20px;
        }

        .register-left h2{
            font-weight: 800;
            margin-bottom: 15px;
        }

        .register-left p{
            font-size: 15px;
            opacity: .9;
            line-height: 1.7;
        }

        .feature-item{
            margin-top: 18px;
            font-size: 14px;
        }

        .feature-item i{
            margin-right: 8px;
        }
    </style>
</head>

<body class="bg-gradient-primary">

<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">

        <div class="card-body p-0">

            <div class="row no-gutters">

                <div class="col-lg-5 register-left">
                    <div>
                        <div class="icon">
                            <i class="fas fa-car-side"></i>
                        </div>

                        <h2>Galaxy Showroom</h2>

                        <p>
                            Daftar akun untuk melakukan pemesanan mobil,
                            pembayaran, monitoring transaksi, hingga pengiriman kendaraan.
                        </p>

                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            Booking mobil lebih mudah
                        </div>

                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            Pantau status transaksi
                        </div>

                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            Cek proses pengiriman mobil
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">

                    <div class="p-5">

                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-2">Buat Akun Baru</h1>
                            <p class="text-muted mb-4">Silakan lengkapi data registrasi</p>
                        </div>

                        <form class="user" action="proses_register.php" method="POST">

                            <div class="form-group">
                                <input type="text"
                                       name="nama"
                                       class="form-control form-control-user"
                                       placeholder="Nama Lengkap"
                                       required>
                            </div>

                            <div class="form-group">
                                <input type="text"
                                       name="username"
                                       class="form-control form-control-user"
                                       placeholder="Username"
                                       required>
                            </div>

                            <div class="form-group">
                                <input type="text"
                                       name="no_hp"
                                       class="form-control form-control-user"
                                       placeholder="No HP">
                            </div>

                            <div class="form-group">
                                <textarea name="alamat"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Alamat"></textarea>
                            </div>

                            <div class="form-group">
                                <select name="role" class="form-control" required>
                                    <option value="">-- Pilih Role --</option>
                                    <option value="pembeli">Pembeli</option>
                                    <option value="penjual">Penjual</option>
                                    <option value="kurir">Kurir</option>
                                </select>
                            </div>

                            <div class="form-group row">

                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <input type="password"
                                           name="password"
                                           class="form-control form-control-user"
                                           placeholder="Password"
                                           required>
                                </div>

                                <div class="col-sm-6">
                                    <input type="password"
                                           name="konfirmasi_password"
                                           class="form-control form-control-user"
                                           placeholder="Ulangi Password"
                                           required>
                                </div>

                            </div>

                            <button type="submit" name="register" class="btn btn-primary btn-user btn-block">
                                <i class="fas fa-user-plus"></i>
                                Daftar Akun
                            </button>

                        </form>

                        <hr>

                        <div class="text-center">
                            <a class="small" href="login.php">
                                Sudah punya akun? Login
                            </a>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../assets/js/sb-admin-2.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if(isset($_SESSION['error'])){ ?>

<script>

document.addEventListener("DOMContentLoaded", function(){

    Swal.fire({
        icon: 'error',
        title: 'Registrasi Gagal',
        text: '<?= $_SESSION['error']; ?>',
        showConfirmButton: false,
        timer: 1800,
        timerProgressBar: true
    });

});

</script>

<?php unset($_SESSION['error']); } ?>

</body>
</html>
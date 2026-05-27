<?php
session_start();

if(isset($_SESSION['role'])){
    if($_SESSION['role'] == 'admin'){
        header("Location: ../admin/dashboard.php");
    } elseif($_SESSION['role'] == 'pembeli'){
        header("Location: ../pembeli/dashboard.php");
    } elseif($_SESSION['role'] == 'penjual'){
        header("Location: ../penjual/dashboard.php");
    } elseif($_SESSION['role'] == 'kurir'){
        header("Location: ../kurir/dashboard.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Galaxy Showroom</title>

    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">

<div class="container d-flex align-items-center justify-content-center" style="min-height:100vh;">

    <div class="row justify-content-center w-100">

        <div class="col-xl-5 col-lg-6 col-md-8">

            <div class="card o-hidden border-0 shadow-lg">

                <div class="card-body p-3">

                    <div class="p-5">

                        <div class="text-center">
                            <h1 class="h1 text-gray-900 mb-2">
                                Galaxy Showroom
                            </h1>

                            <p class="text-muted mb-4">
                                Silakan login ke akun Anda
                            </p>
                        </div>

                        <form class="user" action="proses_login.php" method="POST">

                            <div class="form-group">
                                <input type="text"
                                       name="username"
                                       class="form-control form-control-user"
                                       placeholder="Username"
                                       required>
                            </div>

                            <div class="form-group">
                                <input type="password"
                                       name="password"
                                       class="form-control form-control-user"
                                       placeholder="Password"
                                       required>
                            </div>

                            <button type="submit"
                                    name="login"
                                    class="btn btn-primary btn-user btn-block">

                                Login

                            </button>

                        </form>

                        <hr>

                        <div class="text-center">
                            <a class="small" href="register.php">
                                Belum punya akun? Daftar sekarang!
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
        title: 'Login Gagal',
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
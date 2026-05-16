<?php
$page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">

    <h2>SHOWROOM</h2>

    <ul>

        <!-- DASHBOARD -->

        <li>

            <a href="../dashboard/pembeli.php"
            class="<?= ($page == 'pembeli.php') ? 'active' : ''; ?>">

                Dashboard

            </a>

        </li>


        <!-- LIHAT MOBIL -->

        <li>

            <a href="../pembeli_mobil/lihat_mobil.php"
            class="<?= ($page == 'lihat_mobil.php') ? 'active' : ''; ?>">

                Lihat Mobil

            </a>

        </li>


        <!-- PEMESANAN -->

        <li>

            <a href="../pembeli_mobil/pesanan_saya.php"
            class="<?= ($page == 'pesanan_saya.php') ? 'active' : ''; ?>">

                Pesanan Saya

            </a>

        </li>


        <!-- PEMBAYARAN -->

        <li>



        </li>


        <!-- PROFILE -->

        <li>

            <a href="#">

                Profile

            </a>

        </li>


        <!-- LOGOUT -->

        <li>

            <a href="../auth/logout.php"
            class="logout-btn">

                Logout

            </a>

        </li>

    </ul>

</div>
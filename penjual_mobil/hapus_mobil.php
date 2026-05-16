<?php

include "../config/koneksi.php";

$id = $_GET['id'];

mysqli_query($koneksi,
"DELETE FROM mobil
WHERE id_mobil='$id'");

header("Location: data_mobil.php");

?>
<?php
$koneksi = mysqli_connect("localhost", "root", "", "db_perpustakaan");

if(!$koneksi){
    die("Koneksi Gagal: " . mysqli_connect_error());
}

mysqli_set_charset($koneksi, "utf8mb4");
?>
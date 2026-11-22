<?php
session_start();
include '../config/koneksi.php';
include '../includes/auth_check.php';
include_once '../includes/functions.php';
wajib_role('anggota');

if (!isset($_POST['ajukan'])) {
    header("Location: ../index.php"); exit();
}

$user_id = $_SESSION['user_id'];
$buku_id = (int) $_POST['buku_id'];
$kembali = '../detail.php?id=' . $buku_id;

$q = mysqli_prepare($koneksi, "SELECT stok FROM buku WHERE id = ?");
mysqli_stmt_bind_param($q, 'i', $buku_id);
mysqli_stmt_execute($q);
$hasil = mysqli_stmt_get_result($q);

if (mysqli_num_rows($hasil) == 0) {
    set_pesan('danger', 'Buku tidak ditemukan');
    header("Location: ../index.php"); exit();
}

$buku = mysqli_fetch_assoc($hasil);

if ($buku['stok'] <= 0) {
    set_pesan('danger', 'Stok buku habis, belum bisa dipinjam');
    header("Location: " . $kembali); exit();
}

$q = mysqli_prepare(
    $koneksi,
    "SELECT COUNT(*) AS total FROM peminjaman
     WHERE user_id = ? AND status IN ('menunggu', 'dipinjam')"
);
mysqli_stmt_bind_param($q, 'i', $user_id);
mysqli_stmt_execute($q);
$hasil = mysqli_stmt_get_result($q);
$jumlah_aktif = mysqli_fetch_assoc($hasil)['total'];

if ($jumlah_aktif >= 3) {
    set_pesan('danger', 'Kamu sudah memiliki 3 pinjaman aktif (maksimal 3 buku)');
    header("Location: " . $kembali); exit();
}

$q = mysqli_prepare(
    $koneksi,
    "SELECT id FROM peminjaman
     WHERE user_id = ? AND buku_id = ? AND status IN ('menunggu', 'dipinjam')"
);
mysqli_stmt_bind_param($q, 'ii', $user_id, $buku_id);
mysqli_stmt_execute($q);
$hasil = mysqli_stmt_get_result($q);

if (mysqli_num_rows($hasil) > 0) {
    set_pesan('danger', 'Kamu sudah mengajukan atau sedang meminjam buku ini');
    header("Location: " . $kembali); exit();
}

$q = mysqli_prepare(
    $koneksi,
    "SELECT COUNT(*) AS total FROM peminjaman
     WHERE user_id = ? AND status_denda = 'belum_lunas'"
);
mysqli_stmt_bind_param($q, 'i', $user_id);
mysqli_stmt_execute($q);
$hasil = mysqli_stmt_get_result($q);
$denda_belum_lunas = mysqli_fetch_assoc($hasil)['total'];

if ($denda_belum_lunas > 0) {
    set_pesan('danger', 'Kamu masih punya denda yang belum lunas. Selesaikan pembayaran di menu Denda sebelum meminjam.');
    header("Location: denda.php"); 
    exit();
}

$query = mysqli_prepare(
    $koneksi,
    "INSERT INTO peminjaman (user_id, buku_id, tgl_pengajuan) VALUES (?, ?, CURDATE())"
);
mysqli_stmt_bind_param($query, 'ii', $user_id, $buku_id);
mysqli_stmt_execute($query);

set_pesan('success', 'Pengajuan peminjaman berhasil, menunggu persetujuan admin');
header("Location: " . $kembali); 
exit();
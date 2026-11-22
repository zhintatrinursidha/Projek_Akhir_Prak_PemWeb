<?php
session_start();
include '../../config/koneksi.php';
include '../../includes/auth_check.php';
include_once '../../includes/functions.php';
wajib_role('admin');

if (isset($_POST['hapus'])) {
    $id = (int) $_POST['id'];

    $q = mysqli_prepare($koneksi, "SELECT cover FROM buku WHERE id = ?");
    mysqli_stmt_bind_param($q, 'i', $id);
    mysqli_stmt_execute($q);
    $hasil = mysqli_stmt_get_result($q);

    if (mysqli_num_rows($hasil) == 0) {
        set_pesan('danger', 'Buku tidak ditemukan');
        header("Location: index.php"); exit();
    }

    $buku = mysqli_fetch_assoc($hasil);

    $q2 = mysqli_prepare($koneksi, "SELECT COUNT(*) AS total FROM peminjaman WHERE buku_id = ?");
    mysqli_stmt_bind_param($q2, 'i', $id);
    mysqli_stmt_execute($q2);
    $r2 = mysqli_stmt_get_result($q2);
    $jumlah_pinjam = mysqli_fetch_assoc($r2)['total'];

    if ($jumlah_pinjam > 0) {
        set_pesan('danger', 'Buku tidak bisa dihapus karena sudah punya riwayat peminjaman');
        header("Location: index.php"); exit();
    }

    $hapus = mysqli_prepare($koneksi, "DELETE FROM buku WHERE id = ?");
    mysqli_stmt_bind_param($hapus, 'i', $id);
    mysqli_stmt_execute($hapus);

    if ($buku['cover'] != 'no-cover.png') {
        $file = __DIR__ . '/../../uploads/cover/' . $buku['cover'];
        if (file_exists($file)) {
            unlink($file);
        }
    }

    set_pesan('success', 'Buku berhasil dihapus');
}

header("Location: index.php"); 
exit();
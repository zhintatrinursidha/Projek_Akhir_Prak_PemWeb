<?php
session_start();
include '../config/koneksi.php';
include '../includes/auth_check.php';
include_once '../includes/functions.php';
wajib_role('anggota');

$user_id = $_SESSION['user_id'];
$q = mysqli_prepare(
    $koneksi,
    "SELECT
        COALESCE(SUM(status IN ('menunggu', 'dipinjam')), 0) AS aktif,
        COALESCE(SUM(status = 'menunggu'), 0) AS menunggu,
        COALESCE(SUM(CASE WHEN status_denda = 'belum_lunas' THEN denda ELSE 0 END), 0) AS total_denda
     FROM peminjaman
     WHERE user_id = ?"
);
mysqli_stmt_bind_param($q, 'i', $user_id);
mysqli_stmt_execute($q);
$hasil = mysqli_stmt_get_result($q);
$ringkas = mysqli_fetch_assoc($hasil);

$q = mysqli_prepare(
    $koneksi,
    "SELECT p.tgl_jatuh_tempo, b.judul
     FROM peminjaman p
     JOIN buku b ON p.buku_id = b.id
     WHERE p.user_id = ? AND p.status = 'dipinjam'
     ORDER BY p.tgl_jatuh_tempo ASC
     LIMIT 1"
);
mysqli_stmt_bind_param($q, 'i', $user_id);
mysqli_stmt_execute($q);
$hasil = mysqli_stmt_get_result($q);
$terdekat = mysqli_fetch_assoc($hasil);

$judul = 'Dashboard Anggota';
include '../includes/header.php';
?>

<h3 class="mb-3">Halo, <?= htmlspecialchars($_SESSION['nama']) ?></h3>
<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card text-bg-primary h-100">
            <div class="card-body">
                <h2><?= $ringkas['aktif'] ?> / 3</h2>
                <p class="mb-0">Pinjaman aktif</p>
                <?php if ($ringkas['menunggu'] > 0) { ?>
                    <small><?= $ringkas['menunggu'] ?> menunggu persetujuan</small>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <?php
        $terlambat = $terdekat && $terdekat['tgl_jatuh_tempo'] < date('Y-m-d');
        ?>
        <div class="card <?= $terlambat ? 'text-bg-danger' : 'text-bg-warning' ?> h-100">
            <div class="card-body">
                <?php if ($terdekat) { ?>
                    <h2><?= date('d-m-Y', strtotime($terdekat['tgl_jatuh_tempo'])) ?></h2>
                    <p class="mb-0">Jatuh tempo terdekat<?= $terlambat ? ' (terlambat)' : '' ?></p>
                    <small><?= htmlspecialchars($terdekat['judul']) ?></small>
                <?php } else { ?>
                    <h2>-</h2>
                    <p class="mb-0">Tidak ada buku yang sedang dipinjam</p>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card <?= $ringkas['total_denda'] > 0 ? 'text-bg-danger' : 'text-bg-success' ?> h-100">
            <div class="card-body">
                <h2>Rp<?= number_format($ringkas['total_denda'], 0, ',', '.') ?></h2>
                <p class="mb-0">Denda belum dibayar</p>
                <?php if ($ringkas['total_denda'] > 0) { ?>
                    <a href="denda.php" class="btn btn-sm btn-light mt-2">Bayar Denda</a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<a href="../index.php" class="btn btn-primary">Cari Buku</a>
<a href="riwayat.php" class="btn btn-outline-primary">Lihat Riwayat</a>

<?php 
include '../includes/footer.php'; 
?>
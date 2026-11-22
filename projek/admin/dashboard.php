<?php
session_start();
include '../config/koneksi.php';
include '../includes/auth_check.php';
include_once '../includes/functions.php';
wajib_role('admin');


$q = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM buku");
$data = mysqli_fetch_assoc($q);
$total_buku = $data['total'];

$q = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM users WHERE role = 'anggota'");
$data = mysqli_fetch_assoc($q);
$total_anggota = $data['total'];

$q = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM peminjaman WHERE status = 'dipinjam'");
$data = mysqli_fetch_assoc($q);
$sedang_dipinjam = $data['total'];

$q = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM peminjaman WHERE status_denda = 'belum_lunas'");
$data = mysqli_fetch_assoc($q);
$denda_belum_lunas = $data['total'];

$q = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pembayaran_denda WHERE status = 'menunggu'");
$data = mysqli_fetch_assoc($q);
$menunggu_verifikasi = $data['total'];

$judul = 'Dashboard Admin';
include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-3 mb-3">
        <?php 
        include '../includes/sidebar_admin.php';
        ?>
    </div>
    <div class="col-md-9">
        <h3 class="mb-3">Dashboard</h3>
        <div class="row">
            <div class="col-6 col-lg-3 mb-3">
                <div class="card text-bg-primary h-100">
                    <div class="card-body">
                        <h2><?= $total_buku ?></h2>
                        <p class="mb-0">Total Buku</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 mb-3">
                <div class="card text-bg-success h-100">
                    <div class="card-body">
                        <h2><?= $total_anggota ?></h2>
                        <p class="mb-0">Total Anggota</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 mb-3">
                <div class="card text-bg-warning h-100">
                    <div class="card-body">
                        <h2><?= $sedang_dipinjam ?></h2>
                        <p class="mb-0">Sedang Dipinjam</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 mb-3">
                <div class="card text-bg-danger h-100">
                    <div class="card-body">
                        <h2><?= $denda_belum_lunas ?></h2>
                        <p class="mb-0">Denda Belum Lunas</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-warning mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Pembayaran menunggu verifikasi</h5>
                    <p class="mb-0 text-muted"><?= $menunggu_verifikasi ?> bukti transfer perlu diperiksa</p>
                </div>
               <a href="<?= $base_url ?>/admin/verifikasi_pembayaran.php" class="btn btn-warning">Periksa</a>
            </div>
        </div>
        <p id="pesanGrafik" class="text-danger"></p>
        <div class="row">
            <div class="col-lg-8 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h6>Kunjungan 7 Hari Terakhir</h6>
                        <canvas id="grafikKunjungan"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h6>Status Peminjaman</h6>
                        <canvas id="grafikStatus"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-body">
                <h6>5 Buku Terlaris</h6>
                <canvas id="grafikTerlaris"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="<?= $base_url ?>/assets/grafik.js"></script>

<?php 
include '../includes/footer.php'; 
?>
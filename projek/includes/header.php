<?php
require_once __DIR__ . '/functions.php';
$base_url = '/projek';
if (!isset($judul)) {
    $judul = 'PustakaKu';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= bersihkan($judul) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $base_url ?>/assets/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="<?= $base_url ?>/index.php">PustakaKu</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuUtama">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuUtama">
            <ul class="navbar-nav ms-auto">
                <?php if (!isset($_SESSION['role'])) { ?>
                    <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/auth/login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/auth/register.php">Register</a></li>
                <?php } elseif ($_SESSION['role'] == 'anggota') { ?>
                    <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/anggota/dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/index.php">Katalog</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/anggota/riwayat.php">Riwayat</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/anggota/denda.php">Denda</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/anggota/profil.php">Profil</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/auth/logout.php">Logout</a></li>
                <?php } else { ?>
                    <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/admin/dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/auth/logout.php">Logout</a></li>
                <?php } ?>
            </ul>
            <?php if (isset($_SESSION['login'])) { ?>
                <span class="navbar-text ms-lg-3 d-flex align-items-center text-white">
                <img src="<?= url_foto($_SESSION['foto']) ?>" class="foto-nav me-2" alt="foto">
                <?= htmlspecialchars($_SESSION['nama']) ?>
                </span>
            <?php } ?>
        </div>
    </div>
</nav>
<div class="container mt-4">
    <?php tampil_pesan(); ?>
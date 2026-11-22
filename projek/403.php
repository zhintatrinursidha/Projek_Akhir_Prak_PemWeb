<?php
$judul = 'Akses Ditolak';
include __DIR__ . '/includes/header.php';
?>

<div class="text-center mt-5">
    <h1 class="display-1">403</h1>
    <h4>Akses ditolak</h4>
    <p>Kamu tidak punya izin untuk membuka halaman ini.</p>
    <a href="<?= $base_url ?>/index.php" class="btn btn-primary">Kembali ke Beranda</a>
</div>

<?php 
include __DIR__ . '/includes/footer.php'; 
?>
<?php
session_start();
include 'config/koneksi.php';
include 'includes/auth_check.php';
include_once 'includes/functions.php';

$kategori_id = isset($_GET['kategori']) ? (int) $_GET['kategori'] : 0;
$query = mysqli_prepare(
    $koneksi,
    "SELECT b.*, k.nama_kategori
     FROM buku b
     LEFT JOIN kategori k ON b.kategori_id = k.id
     WHERE (? = 0 OR b.kategori_id = ?)
     ORDER BY b.judul"
);
mysqli_stmt_bind_param($query, 'ii', $kategori_id, $kategori_id);
mysqli_stmt_execute($query);
$data_buku = mysqli_stmt_get_result($query);

$data_kategori = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori");

$judul = 'PustakaKu - Perpustakaan Online';
include 'includes/header.php';
?>

<div class="hero text-center">
    <h1 class="fw-bold">PustakaKu</h1>
    <p class="lead mb-3">Cari, ajukan, dan pinjam buku perpustakaan dengan mudah.</p>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <input type="text" id="cariBuku" class="form-control form-control-lg" placeholder="Cari judul atau penulis...">
        </div>
    </div>
</div>
<div class="mb-4">
    <a href="index.php" class="btn btn-sm <?= $kategori_id == 0 ? 'btn-primary' : 'btn-outline-primary' ?> mb-1">Semua</a>
    <?php while ($k = mysqli_fetch_assoc($data_kategori)) { ?>
        <a href="index.php?kategori=<?= $k['id'] ?>"
           class="btn btn-sm <?= $kategori_id == $k['id'] ? 'btn-primary' : 'btn-outline-primary' ?> mb-1">
            <?= htmlspecialchars($k['nama_kategori']) ?>
        </a>
    <?php } ?>
</div>
<div class="row">
    <?php while ($b = mysqli_fetch_assoc($data_buku)) { ?>
        <div class="col-6 col-md-4 col-lg-3 mb-4 item-buku"
             data-cari="<?= htmlspecialchars(mb_strtolower($b['judul'] . ' ' . $b['penulis'])) ?>">
            <div class="card card-buku">
                <img src="<?= url_cover($b['cover']) ?>" class="card-img-top" alt="cover">
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title"><?= htmlspecialchars($b['judul']) ?></h6>
                    <p class="card-text text-muted small mb-1"><?= htmlspecialchars((string) $b['penulis']) ?></p>
                    <p class="mb-2">
                        <span class="badge text-bg-info"><?= htmlspecialchars((string) $b['nama_kategori']) ?></span>
                        <?php if ($b['stok'] > 0) { ?>
                            <span class="badge text-bg-success">Stok <?= $b['stok'] ?></span>
                        <?php } else { ?>
                            <span class="badge text-bg-danger">Habis</span>
                        <?php } ?>
                    </p>
                    <a href="detail.php?id=<?= $b['id'] ?>" class="btn btn-primary btn-sm mt-auto">Detail</a>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<p id="kosongBuku" class="text-center text-muted" style="display: none;">Buku tidak ditemukan.</p>

<?php 
include 'includes/footer.php'; 
?>
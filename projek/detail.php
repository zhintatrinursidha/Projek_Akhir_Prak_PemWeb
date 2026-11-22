<?php
session_start();
include 'config/koneksi.php';
include 'includes/auth_check.php';
include_once 'includes/functions.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$query = mysqli_prepare(
    $koneksi,
    "SELECT b.*, k.nama_kategori
     FROM buku b
     LEFT JOIN kategori k ON b.kategori_id = k.id
     WHERE b.id = ?"
);
mysqli_stmt_bind_param($query, 'i', $id);
mysqli_stmt_execute($query);
$hasil = mysqli_stmt_get_result($query);

$ditemukan = mysqli_num_rows($hasil) == 1;

if ($ditemukan) {
    $b = mysqli_fetch_assoc($hasil);
    $judul = $b['judul'];
} else {
    http_response_code(404);
    $judul = 'Buku Tidak Ditemukan';
}

include 'includes/header.php';
?>

<?php if (!$ditemukan) { ?>
    <div class="alert alert-warning">Buku tidak ditemukan.</div>
    <a href="index.php" class="btn btn-primary">Kembali ke Katalog</a>
<?php } else { ?>
    <div class="row">
        <div class="col-md-4 mb-3">
            <img src="<?= url_cover($b['cover']) ?>" class="img-fluid rounded shadow-sm" alt="cover">
        </div>
        <div class="col-md-8">
            <h3><?= htmlspecialchars($b['judul']) ?></h3>
            <table class="table table-borderless w-auto">
                <tr>
                    <td class="text-muted">Kode</td>
                    <td><?= htmlspecialchars($b['kode_buku']) ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Penulis</td>
                    <td><?= htmlspecialchars((string) $b['penulis']) ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Penerbit</td>
                    <td><?= htmlspecialchars((string) $b['penerbit']) ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Tahun</td>
                    <td><?= htmlspecialchars((string) $b['tahun_terbit']) ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Kategori</td>
                    <td><?= htmlspecialchars((string) $b['nama_kategori']) ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Ketersediaan</td>
                    <td>
                        <?php if ($b['stok'] > 0) { ?>
                            <span class="badge text-bg-success">Tersedia (<?= $b['stok'] ?>)</span>
                        <?php } else { ?>
                            <span class="badge text-bg-danger">Stok habis</span>
                        <?php } ?>
                    </td>
                </tr>
            </table>
            <h6>Sinopsis</h6>
            <p><?= nl2br(htmlspecialchars((string) $b['sinopsis'])) ?></p>
            <?php if (!isset($_SESSION['login'])) { ?>
                <a href="auth/login.php" class="btn btn-primary">Login untuk Meminjam</a>
            <?php } else if ($_SESSION['role'] == 'anggota') { ?>
                <form method="POST" action="anggota/pinjam.php" class="d-inline">
                    <input type="hidden" name="buku_id" value="<?= $b['id'] ?>">
                    <button type="submit" name="ajukan" class="btn btn-primary"
                            <?= $b['stok'] <= 0 ? 'disabled' : '' ?>>
                        Ajukan Pinjam
                    </button>
                </form>
            <?php } else { ?>
                <p class="text-muted">Admin tidak dapat mengajukan peminjaman.</p>
            <?php } ?>
            <a href="index.php" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>
<?php } ?>

<?php 
include 'includes/footer.php'; 
?>
<?php
session_start();
include '../../config/koneksi.php';
include '../../includes/auth_check.php';
include_once '../../includes/functions.php';
wajib_role('admin');

$cari = isset($_GET['cari']) ? trim($_GET['cari']) : '';
$halaman = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
if ($halaman < 1) {
    $halaman = 1;
}

$per_halaman = 5;
$offset = ($halaman - 1) * $per_halaman;
$like = "%" . $cari . "%";

$q_total = mysqli_prepare(
    $koneksi,
    "SELECT COUNT(*) AS total FROM buku
     WHERE judul LIKE ? OR penulis LIKE ? OR kode_buku LIKE ?"
);
mysqli_stmt_bind_param($q_total, 'sss', $like, $like, $like);
mysqli_stmt_execute($q_total);
$r_total = mysqli_stmt_get_result($q_total);
$total_data = mysqli_fetch_assoc($r_total)['total'];
$total_halaman = ceil($total_data / $per_halaman);

$query = mysqli_prepare(
    $koneksi,
    "SELECT b.*, k.nama_kategori
     FROM buku b
     LEFT JOIN kategori k ON b.kategori_id = k.id
     WHERE b.judul LIKE ? OR b.penulis LIKE ? OR b.kode_buku LIKE ?
     ORDER BY b.id DESC
     LIMIT ? OFFSET ?"
);
mysqli_stmt_bind_param($query, 'sssii', $like, $like, $like, $per_halaman, $offset);
mysqli_stmt_execute($query);
$data_buku = mysqli_stmt_get_result($query);

$judul = 'Data Buku';

include '../../includes/header.php';
?>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <a href="<?= $base_url ?>/admin/dashboard.php" class="btn btn-outline-secondary btn-sm mb-3 no-print">
            &larr; Kembali ke Dashboard
        </a>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Data Buku</h3>
            <a href="tambah.php" class="btn btn-primary">+ Tambah Buku</a>
        </div>
        <form method="GET" class="input-group mb-3">
            <input type="text" name="cari" class="form-control"
                   placeholder="Cari judul, penulis, atau kode..."
                   value="<?= htmlspecialchars($cari) ?>">
            <button class="btn btn-outline-primary" type="submit">Cari</button>
        </form>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Cover</th>
                            <th>Kode</th>
                            <th>Judul</th>
                            <th>Penulis</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($data_buku) == 0) { ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Data buku tidak ditemukan</td>
                            </tr>
                        <?php } ?>

                        <?php while ($b = mysqli_fetch_assoc($data_buku)) { ?>
                            <tr>
                                <td>
                                    <img src="<?= url_cover($b['cover']) ?>" alt="cover" style="width: 45px; height: 60px; object-fit: cover;">
                                </td>
                                <td><?= htmlspecialchars($b['kode_buku']) ?></td>
                                <td><?= htmlspecialchars($b['judul']) ?></td>
                                <td><?= htmlspecialchars((string) $b['penulis']) ?></td>
                                <td><?= htmlspecialchars((string) $b['nama_kategori']) ?></td>
                                <td><?= $b['stok'] ?></td>
                                <td>
                                    <a href="edit.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-warning">Ubah</a>
                                    <form method="POST" action="hapus.php" class="d-inline form-konfirmasi" data-pesan="Hapus buku &quot;<?= htmlspecialchars($b['judul']) ?>&quot;?">
                                        <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                        <button type="submit" name="hapus" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if ($total_halaman > 1) { ?>
            <nav class="mt-3">
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $total_halaman; $i++) { ?>
                        <li class="page-item <?= $i == $halaman ? 'active' : '' ?>">
                            <a class="page-link" href="?cari=<?= urlencode($cari) ?>&halaman=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php } ?>
                </ul>
            </nav>
        <?php } ?>
    </div>
</div>

<?php 
include '../../includes/footer.php'; 
?>
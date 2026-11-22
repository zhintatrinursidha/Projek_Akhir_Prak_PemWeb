<?php
session_start();
include '../config/koneksi.php';
include '../includes/auth_check.php';
include_once '../includes/functions.php';
wajib_role('admin');

if (isset($_POST['kembalikan'])) {
    $id = (int) $_POST['id'];
    $q = mysqli_prepare(
        $koneksi,
        "SELECT buku_id, tgl_jatuh_tempo FROM peminjaman WHERE id = ? AND status = 'dipinjam'"
    );
    mysqli_stmt_bind_param($q, 'i', $id);
    mysqli_stmt_execute($q);
    $hasil = mysqli_stmt_get_result($q);

    if (mysqli_num_rows($hasil) == 0) {
        set_pesan('danger', 'Data peminjaman tidak ditemukan atau sudah dikembalikan');
        header("Location: pengembalian.php"); exit();
    }

    $pinjam = mysqli_fetch_assoc($hasil);

    $tgl_kembali = date('Y-m-d');
    $denda = hitung_denda($pinjam['tgl_jatuh_tempo'], $tgl_kembali);

    if ($denda > 0) {
        $status_denda = 'belum_lunas';
    } else {
        $status_denda = 'tidak_ada';
    }

    mysqli_begin_transaction($koneksi);

    $q1 = mysqli_prepare(
        $koneksi,
        "UPDATE peminjaman
         SET tgl_kembali = ?, denda = ?, status = 'dikembalikan', status_denda = ?
         WHERE id = ? AND status = 'dipinjam'"
    );
    mysqli_stmt_bind_param($q1, 'sisi', $tgl_kembali, $denda, $status_denda, $id);
    mysqli_stmt_execute($q1);

    if (mysqli_stmt_affected_rows($q1) == 1) {
        $q2 = mysqli_prepare($koneksi, "UPDATE buku SET stok = stok + 1 WHERE id = ?");
        mysqli_stmt_bind_param($q2, 'i', $pinjam['buku_id']);
        mysqli_stmt_execute($q2);

        mysqli_commit($koneksi);

        if ($denda > 0) {
            set_pesan('warning', 'Buku dikembalikan. Denda keterlambatan: Rp' . number_format($denda, 0, ',', '.'));
        } else {
            set_pesan('success', 'Buku dikembalikan tanpa denda');
        }
    } else {
        mysqli_rollback($koneksi);
        set_pesan('danger', 'Gagal memproses pengembalian');
    }

    header("Location: pengembalian.php"); exit();
}

$data_pinjam = mysqli_query(
    $koneksi,
    "SELECT p.id, p.tgl_pinjam, p.tgl_jatuh_tempo,
            DATEDIFF(CURDATE(), p.tgl_jatuh_tempo) AS selisih,
            u.nama, b.judul
     FROM peminjaman p
     JOIN users u ON p.user_id = u.id
     JOIN buku b ON p.buku_id = b.id
     WHERE p.status = 'dipinjam'
     ORDER BY p.tgl_jatuh_tempo ASC"
);

$judul = 'Pengembalian Buku';

include '../includes/header.php';
?>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <a href="<?= $base_url ?>/admin/dashboard.php" class="btn btn-outline-secondary btn-sm mb-3 no-print">
            &larr; Kembali ke Dashboard
        </a>
        <h3 class="mb-3">Pengembalian Buku</h3>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Anggota</th>
                            <th>Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Status</th>
                            <th>Estimasi Denda</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($data_pinjam) == 0) { ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Tidak ada buku yang sedang dipinjam</td>
                            </tr>
                        <?php } ?>

                        <?php while ($p = mysqli_fetch_assoc($data_pinjam)) { ?>
                            <tr>
                                <td><?= htmlspecialchars($p['nama']) ?></td>
                                <td><?= htmlspecialchars($p['judul']) ?></td>
                                <td><?= date('d-m-Y', strtotime($p['tgl_pinjam'])) ?></td>
                                <td><?= date('d-m-Y', strtotime($p['tgl_jatuh_tempo'])) ?></td>
                                <td>
                                    <?php if ($p['selisih'] > 0) { ?>
                                        <span class="badge text-bg-danger">Terlambat <?= $p['selisih'] ?> hari</span>
                                    <?php } else { ?>
                                        <span class="badge text-bg-success">Dipinjam</span>
                                    <?php } ?>
                                </td>
                                <td>Rp<?= number_format(hitung_denda($p['tgl_jatuh_tempo'], date('Y-m-d')), 0, ',', '.') ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <button type="submit" name="kembalikan" class="btn btn-sm btn-primary">Kembalikan</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php'; 
?>
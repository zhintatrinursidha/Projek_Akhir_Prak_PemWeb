<?php
session_start();
include '../config/koneksi.php';
include '../includes/auth_check.php';
include_once '../includes/functions.php';
wajib_role('admin');

if (isset($_POST['setujui'])) {
    $id = (int) $_POST['id'];
    $q = mysqli_prepare(
        $koneksi,
        "SELECT buku_id FROM peminjaman WHERE id = ? AND status = 'menunggu'"
    );
    mysqli_stmt_bind_param($q, 'i', $id);
    mysqli_stmt_execute($q);
    $hasil = mysqli_stmt_get_result($q);

    if (mysqli_num_rows($hasil) == 0) {
        set_pesan('danger', 'Pengajuan tidak ditemukan atau sudah diproses');
        header("Location: peminjaman.php"); exit();
    }

    $pinjam = mysqli_fetch_assoc($hasil);
    $buku_id = $pinjam['buku_id'];

    mysqli_begin_transaction($koneksi);

    $q1 = mysqli_prepare($koneksi, "UPDATE buku SET stok = stok - 1 WHERE id = ? AND stok > 0");
    mysqli_stmt_bind_param($q1, 'i', $buku_id);
    mysqli_stmt_execute($q1);

    if (mysqli_stmt_affected_rows($q1) == 1) {
        $q2 = mysqli_prepare(
            $koneksi,
            "UPDATE peminjaman
             SET status = 'dipinjam',
                 tgl_pinjam = CURDATE(),
                 tgl_jatuh_tempo = DATE_ADD(CURDATE(), INTERVAL 7 DAY)
             WHERE id = ? AND status = 'menunggu'"
        );
        mysqli_stmt_bind_param($q2, 'i', $id);
        mysqli_stmt_execute($q2);

        if (mysqli_stmt_affected_rows($q2) == 1) {
            mysqli_commit($koneksi);
            set_pesan('success', 'Pengajuan disetujui, stok buku berkurang 1');
        } else {
            mysqli_rollback($koneksi);
            set_pesan('danger', 'Gagal menyetujui pengajuan');
        }
    } else {
        mysqli_rollback($koneksi);
        set_pesan('danger', 'Stok buku habis, pengajuan tidak bisa disetujui');
    }

    header("Location: peminjaman.php"); exit();
}

if (isset($_POST['tolak'])) {
    $id = (int) $_POST['id'];

    $query = mysqli_prepare(
        $koneksi,
        "UPDATE peminjaman SET status = 'ditolak' WHERE id = ? AND status = 'menunggu'"
    );
    mysqli_stmt_bind_param($query, 'i', $id);
    mysqli_stmt_execute($query);

    set_pesan('success', 'Pengajuan ditolak');
    header("Location: peminjaman.php"); exit();
}

$data_pengajuan = mysqli_query(
    $koneksi,
    "SELECT p.id, p.tgl_pengajuan, u.nama, u.email, b.judul, b.stok
     FROM peminjaman p
     JOIN users u ON p.user_id = u.id
     JOIN buku b ON p.buku_id = b.id
     WHERE p.status = 'menunggu'
     ORDER BY p.tgl_pengajuan ASC, p.id ASC"
);

$judul = 'Verifikasi Peminjaman';

include '../includes/header.php';
?>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <a href="<?= $base_url ?>/admin/dashboard.php" class="btn btn-outline-secondary btn-sm mb-3 no-print">
            &larr; Kembali ke Dashboard
        </a>
        <h3 class="mb-3">Pengajuan Peminjaman</h3>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tgl Pengajuan</th>
                            <th>Anggota</th>
                            <th>Buku</th>
                            <th>Stok</th>
                            <th style="width: 190px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($data_pengajuan) == 0) { ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada pengajuan yang menunggu</td>
                            </tr>
                        <?php } ?>

                        <?php while ($p = mysqli_fetch_assoc($data_pengajuan)) { ?>
                            <tr>
                                <td><?= date('d-m-Y', strtotime($p['tgl_pengajuan'])) ?></td>
                                <td>
                                    <?= htmlspecialchars($p['nama']) ?><br>
                                    <small class="text-muted"><?= htmlspecialchars($p['email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($p['judul']) ?></td>
                                <td>
                                    <?php if ($p['stok'] > 0) { ?>
                                        <?= $p['stok'] ?>
                                    <?php } else { ?>
                                        <span class="badge text-bg-danger">Habis</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <button type="submit" name="setujui" class="btn btn-sm btn-success"
                                                <?= $p['stok'] <= 0 ? 'disabled' : '' ?>>Setujui</button>
                                    </form>

                                    <form method="POST" class="d-inline form-konfirmasi"
                                          data-pesan="Tolak pengajuan ini?">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <button type="submit" name="tolak" class="btn btn-sm btn-danger">Tolak</button>
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
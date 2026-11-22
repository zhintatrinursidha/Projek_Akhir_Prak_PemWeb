<?php
session_start();
include '../config/koneksi.php';
include '../includes/auth_check.php';
include_once '../includes/functions.php';
wajib_role('admin');

if (isset($_POST['bayar_tunai'])) {
    $id = (int) $_POST['id'];
    $admin_id = $_SESSION['user_id'];

    $q = mysqli_prepare(
        $koneksi,
        "SELECT denda FROM peminjaman WHERE id = ? AND status_denda = 'belum_lunas'"
    );
    mysqli_stmt_bind_param($q, 'i', $id);
    mysqli_stmt_execute($q);
    $hasil = mysqli_stmt_get_result($q);

    if (mysqli_num_rows($hasil) == 0) {
        set_pesan('danger', 'Denda tidak ditemukan atau sudah lunas');
        header("Location: denda.php"); exit();
    }

    $pinjam = mysqli_fetch_assoc($hasil);

    $q = mysqli_prepare(
        $koneksi,
        "SELECT COUNT(*) AS total FROM pembayaran_denda
         WHERE peminjaman_id = ? AND status = 'menunggu'"
    );
    mysqli_stmt_bind_param($q, 'i', $id);
    mysqli_stmt_execute($q);
    $hasil = mysqli_stmt_get_result($q);

    if (mysqli_fetch_assoc($hasil)['total'] > 0) {
        set_pesan('danger', 'Ada pembayaran transfer yang menunggu verifikasi untuk denda ini');
        header("Location: denda.php"); exit();
    }

    mysqli_begin_transaction($koneksi);

    $q1 = mysqli_prepare(
        $koneksi,
        "INSERT INTO pembayaran_denda
            (peminjaman_id, jumlah, metode, status, diverifikasi_oleh, tgl_verifikasi)
         VALUES (?, ?, 'tunai', 'lunas', ?, NOW())"
    );
    mysqli_stmt_bind_param($q1, 'iii', $id, $pinjam['denda'], $admin_id);
    mysqli_stmt_execute($q1);

    $q2 = mysqli_prepare(
        $koneksi,
        "UPDATE peminjaman SET status_denda = 'lunas' WHERE id = ? AND status_denda = 'belum_lunas'"
    );
    mysqli_stmt_bind_param($q2, 'i', $id);
    mysqli_stmt_execute($q2);

    if (mysqli_stmt_affected_rows($q2) == 1) {
        mysqli_commit($koneksi);
        set_pesan('success', 'Pembayaran tunai Rp' . number_format($pinjam['denda'], 0, ',', '.') . ' berhasil dicatat');
    } else {
        mysqli_rollback($koneksi);
        set_pesan('danger', 'Gagal mencatat pembayaran');
    }

    header("Location: denda.php"); 
    exit();
}

$data_denda = mysqli_query(
    $koneksi,
    "SELECT p.id, p.denda, p.tgl_kembali, u.nama, u.email, b.judul,
            (SELECT COUNT(*) FROM pembayaran_denda pd
             WHERE pd.peminjaman_id = p.id AND pd.status = 'menunggu') AS transfer_menunggu
     FROM peminjaman p
     JOIN users u ON p.user_id = u.id
     JOIN buku b ON p.buku_id = b.id
     WHERE p.status_denda = 'belum_lunas'
     ORDER BY p.tgl_kembali ASC"
);

$judul = 'Denda';

include '../includes/header.php';
?>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <a href="<?= $base_url ?>/admin/dashboard.php" class="btn btn-outline-secondary btn-sm mb-3 no-print">
            &larr; Kembali ke Dashboard
        </a>
        <h3 class="mb-3">Denda Belum Lunas</h3>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Anggota</th>
                            <th>Buku</th>
                            <th>Tgl Kembali</th>
                            <th>Denda</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($data_denda) == 0) { ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Tidak ada denda yang belum lunas</td>
                            </tr>
                        <?php } ?>

                        <?php while ($d = mysqli_fetch_assoc($data_denda)) { ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($d['nama']) ?><br>
                                    <small class="text-muted"><?= htmlspecialchars($d['email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($d['judul']) ?></td>
                                <td><?= date('d-m-Y', strtotime($d['tgl_kembali'])) ?></td>
                                <td>Rp<?= number_format($d['denda'], 0, ',', '.') ?></td>
                                <td>
                                    <?php if ($d['transfer_menunggu'] > 0) { ?>
                                        <span class="badge text-bg-warning">Transfer menunggu verifikasi</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <form method="POST" class="form-konfirmasi"
                                          data-pesan="Terima pembayaran tunai Rp<?= number_format($d['denda'], 0, ',', '.') ?> dari <?= htmlspecialchars($d['nama']) ?>?">
                                        <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                        <button type="submit" name="bayar_tunai" class="btn btn-sm btn-success"
                                                <?= $d['transfer_menunggu'] > 0 ? 'disabled' : '' ?>>
                                            Terima Tunai
                                        </button>
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
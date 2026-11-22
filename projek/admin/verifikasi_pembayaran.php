<?php
session_start();
include '../config/koneksi.php';
include '../includes/auth_check.php';
include_once '../includes/functions.php';
wajib_role('admin');

if (isset($_POST['setujui'])) {
    $id = (int) $_POST['id'];
    $admin_id = $_SESSION['user_id'];

    $q = mysqli_prepare(
        $koneksi,
        "SELECT peminjaman_id FROM pembayaran_denda WHERE id = ? AND status = 'menunggu'"
    );
    mysqli_stmt_bind_param($q, 'i', $id);
    mysqli_stmt_execute($q);
    $hasil = mysqli_stmt_get_result($q);

    if (mysqli_num_rows($hasil) == 0) {
        set_pesan('danger', 'Pembayaran tidak ditemukan atau sudah diproses');
        header("Location: verifikasi_pembayaran.php"); exit();
    }

    $bayar = mysqli_fetch_assoc($hasil);
    mysqli_begin_transaction($koneksi);

    $q1 = mysqli_prepare(
        $koneksi,
        "UPDATE pembayaran_denda
         SET status = 'lunas', diverifikasi_oleh = ?, tgl_verifikasi = NOW()
         WHERE id = ? AND status = 'menunggu'"
    );
    mysqli_stmt_bind_param($q1, 'ii', $admin_id, $id);
    mysqli_stmt_execute($q1);

    if (mysqli_stmt_affected_rows($q1) == 1) {
        $q2 = mysqli_prepare(
            $koneksi,
            "UPDATE peminjaman SET status_denda = 'lunas'
             WHERE id = ? AND status_denda = 'belum_lunas'"
        );
        mysqli_stmt_bind_param($q2, 'i', $bayar['peminjaman_id']);
        mysqli_stmt_execute($q2);

        if (mysqli_stmt_affected_rows($q2) == 1) {
            mysqli_commit($koneksi);
            set_pesan('success', 'Pembayaran disetujui, denda dinyatakan lunas');
        } else {
            mysqli_rollback($koneksi);
            set_pesan('danger', 'Denda ini sudah lunas atau tidak ditemukan');
        }
    } else {
        mysqli_rollback($koneksi);
        set_pesan('danger', 'Gagal memproses pembayaran');
    }

    header("Location: verifikasi_pembayaran.php"); exit();
}

if (isset($_POST['tolak'])) {
    $id = (int) $_POST['id'];
    $admin_id = $_SESSION['user_id'];
    $alasan = mb_substr(trim($_POST['alasan']), 0, 255);

    if ($alasan == "") {
        set_pesan('danger', 'Alasan penolakan wajib diisi');
        header("Location: verifikasi_pembayaran.php"); exit();
    }

    $query = mysqli_prepare(
        $koneksi,
        "UPDATE pembayaran_denda
         SET status = 'ditolak', catatan = ?, diverifikasi_oleh = ?, tgl_verifikasi = NOW()
         WHERE id = ? AND status = 'menunggu'"
    );
    mysqli_stmt_bind_param($query, 'sii', $alasan, $admin_id, $id);
    mysqli_stmt_execute($query);

    set_pesan('success', 'Pembayaran ditolak, anggota bisa mengunggah bukti baru');
    header("Location: verifikasi_pembayaran.php"); 
    exit();
}

$data_bayar = mysqli_query(
    $koneksi,
    "SELECT pd.id, pd.jumlah, pd.bukti, pd.tgl_bayar, u.nama, u.email, b.judul
     FROM pembayaran_denda pd
     JOIN peminjaman p ON pd.peminjaman_id = p.id
     JOIN users u ON p.user_id = u.id
     JOIN buku b ON p.buku_id = b.id
     WHERE pd.status = 'menunggu' AND pd.metode = 'transfer'
     ORDER BY pd.tgl_bayar ASC"
);

$judul = 'Verifikasi Pembayaran';

include '../includes/header.php';
?>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <a href="<?= $base_url ?>/admin/dashboard.php" class="btn btn-outline-secondary btn-sm mb-3 no-print">
            &larr; Kembali ke Dashboard
        </a>
        <h3 class="mb-3">Verifikasi Pembayaran Denda</h3>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Bukti</th>
                            <th>Anggota</th>
                            <th>Buku</th>
                            <th>Nominal</th>
                            <th>Tgl Bayar</th>
                            <th style="width: 170px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($data_bayar) == 0) { ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Tidak ada pembayaran yang menunggu verifikasi</td>
                            </tr>
                        <?php } ?>
                        <?php while ($r = mysqli_fetch_assoc($data_bayar)) { ?>
                            <tr>
                                <td>
                                    <a href="<?= $base_url ?>/uploads/bukti/<?= htmlspecialchars($r['bukti']) ?>" target="_blank">
                                        <img src="<?= $base_url ?>/uploads/bukti/<?= htmlspecialchars($r['bukti']) ?>" alt="bukti" style="width: 70px; height: 70px; object-fit: cover;" class="rounded border">
                                    </a>
                                </td>
                                <td>
                                    <?= htmlspecialchars($r['nama']) ?><br>
                                    <small class="text-muted"><?= htmlspecialchars($r['email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($r['judul']) ?></td>
                                <td>Rp<?= number_format($r['jumlah'], 0, ',', '.') ?></td>
                                <td><?= date('d-m-Y H:i', strtotime($r['tgl_bayar'])) ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                        <button type="submit" name="setujui" class="btn btn-sm btn-success">Setujui</button>
                                    </form>
                                    <button class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalTolak"
                                            data-id="<?= $r['id'] ?>">
                                        Tolak
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTolak" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tolak Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="idPembayaran">
                <label class="form-label">Alasan penolakan</label>
                <textarea name="alasan" class="form-control" rows="3" maxlength="255" required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" name="tolak" class="btn btn-danger">Tolak</button>
            </div>
        </form>
    </div>
</div>

<?php 
include '../includes/footer.php'; 
?>
<?php
session_start();
include '../config/koneksi.php';
include '../includes/auth_check.php';
include_once '../includes/functions.php';
wajib_role('anggota');

$user_id = $_SESSION['user_id'];
$query = mysqli_prepare(
    $koneksi,
    "SELECT p.*, b.judul
     FROM peminjaman p
     JOIN buku b ON p.buku_id = b.id
     WHERE p.user_id = ?
     ORDER BY p.id DESC"
);
mysqli_stmt_bind_param($query, 'i', $user_id);
mysqli_stmt_execute($query);
$data_riwayat = mysqli_stmt_get_result($query);

$hari_ini = date('Y-m-d');

$judul = 'Riwayat Peminjaman';
include '../includes/header.php';
?>

<h3 class="mb-3">Riwayat Peminjaman</h3>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Buku</th>
                    <th>Pengajuan</th>
                    <th>Pinjam</th>
                    <th>Jatuh Tempo</th>
                    <th>Kembali</th>
                    <th>Status</th>
                    <th>Denda</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($data_riwayat) == 0) { ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">Belum ada riwayat peminjaman</td>
                    </tr>
                <?php } ?>
                <?php while ($r = mysqli_fetch_assoc($data_riwayat)) { ?>
                    <tr>
                        <td><?= htmlspecialchars($r['judul']) ?></td>
                        <td><?= date('d-m-Y', strtotime($r['tgl_pengajuan'])) ?></td>
                        <td><?= $r['tgl_pinjam'] ? date('d-m-Y', strtotime($r['tgl_pinjam'])) : '-' ?></td>
                        <td><?= $r['tgl_jatuh_tempo'] ? date('d-m-Y', strtotime($r['tgl_jatuh_tempo'])) : '-' ?></td>
                        <td><?= $r['tgl_kembali'] ? date('d-m-Y', strtotime($r['tgl_kembali'])) : '-' ?></td>
                        <td>
                            <?= badge_status($r['status']) ?>
                            <?php if ($r['status'] == 'dipinjam' && $r['tgl_jatuh_tempo'] < $hari_ini) { ?>
                                <span class="badge text-bg-danger">Terlambat</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($r['denda'] > 0) { ?>
                                Rp<?= number_format($r['denda'], 0, ',', '.') ?>
                                <?php if ($r['status_denda'] == 'lunas') { ?>
                                    <span class="badge text-bg-success">Lunas</span>
                                <?php } else { ?>
                                    <span class="badge text-bg-danger">Belum lunas</span>
                                <?php } ?>
                            <?php } else { ?>
                                -
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
include '../includes/footer.php'; 
?>

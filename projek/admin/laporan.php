<?php
session_start();
include '../config/koneksi.php';
include '../includes/auth_check.php';
include_once '../includes/functions.php';
wajib_role('admin');

$dari = isset($_GET['dari']) ? $_GET['dari'] : '';
$sampai = isset($_GET['sampai']) ? $_GET['sampai'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

if ($dari != '' && !strtotime($dari)) {
    $dari = '';
}
if ($sampai != '' && !strtotime($sampai)) {
    $sampai = '';
}
$status_valid = ['menunggu', 'dipinjam', 'dikembalikan', 'ditolak'];
if (!in_array($status, $status_valid)) {
    $status = '';
}

$query = mysqli_prepare(
    $koneksi,
    "SELECT p.*, u.nama, b.judul,
            (SELECT COALESCE(SUM(pd.jumlah), 0) FROM pembayaran_denda pd
             WHERE pd.peminjaman_id = p.id AND pd.status = 'lunas') AS terkumpul
     FROM peminjaman p
     JOIN users u ON p.user_id = u.id
     JOIN buku b ON p.buku_id = b.id
     WHERE (? = '' OR p.tgl_pengajuan >= ?)
       AND (? = '' OR p.tgl_pengajuan <= ?)
       AND (? = '' OR p.status = ?)
     ORDER BY p.tgl_pengajuan DESC, p.id DESC"
);
mysqli_stmt_bind_param($query, 'ssssss', $dari, $dari, $sampai, $sampai, $status, $status);
mysqli_stmt_execute($query);
$hasil = mysqli_stmt_get_result($query);

$data_laporan = [];
$denda_terbentuk = 0;
$denda_terkumpul = 0;

while ($baris = mysqli_fetch_assoc($hasil)) {
    $data_laporan[] = $baris;
    $denda_terbentuk += $baris['denda'];
    $denda_terkumpul += $baris['terkumpul'];
}

$total_data = count($data_laporan);

$judul = 'Laporan Peminjaman';

include '../includes/header.php';
?>

<div class="row">
    <div class="col-lg-10 mx-auto area-laporan">
        <a href="<?= $base_url ?>/admin/dashboard.php" class="btn btn-outline-secondary btn-sm mb-3 no-print">
            &larr; Kembali ke Dashboard
        </a>
        <div class="hanya-cetak text-center mb-3">
            <h4 class="mb-0">Laporan Peminjaman PustakaKu</h4>
            <p class="mb-0">
                Periode:
                <?= $dari != '' ? date('d-m-Y', strtotime($dari)) : 'awal' ?>
                s/d
                <?= $sampai != '' ? date('d-m-Y', strtotime($sampai)) : 'sekarang' ?>
                | Status: <?= $status != '' ? ucfirst($status) : 'Semua' ?>
            </p>
            <small>Dicetak: <?= date('d-m-Y H:i') ?></small>
        </div>
        <h3 class="mb-3 no-print">Laporan Peminjaman</h3>
        <form method="GET" class="card card-body mb-3 no-print">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Dari tanggal</label>
                    <input type="date" name="dari" class="form-control" value="<?= htmlspecialchars($dari) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sampai tanggal</label>
                    <input type="date" name="sampai" class="form-control" value="<?= htmlspecialchars($sampai) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        <?php foreach ($status_valid as $s) { ?>
                            <option value="<?= $s ?>" <?= $status == $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                    <a href="laporan.php" class="btn btn-outline-secondary">Reset</a>
                    <button type="button" id="tombolCetak" class="btn btn-secondary">Cetak</button>
                </div>
            </div>
        </form>

        <div class="row mb-3">
            <div class="col-6 col-lg-3 mb-2">
                <div class="card h-100">
                    <div class="card-body">
                        <h5><?= $total_data ?></h5>
                        <small class="text-muted">Total transaksi</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 mb-2">
                <div class="card h-100">
                    <div class="card-body">
                        <h5>Rp<?= number_format($denda_terbentuk, 0, ',', '.') ?></h5>
                        <small class="text-muted">Denda terbentuk</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 mb-2">
                <div class="card h-100">
                    <div class="card-body">
                        <h5>Rp<?= number_format($denda_terkumpul, 0, ',', '.') ?></h5>
                        <small class="text-muted">Denda terkumpul</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 mb-2">
                <div class="card h-100">
                    <div class="card-body">
                        <h5>Rp<?= number_format($denda_terbentuk - $denda_terkumpul, 0, ',', '.') ?></h5>
                        <small class="text-muted">Belum terkumpul</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Pengajuan</th>
                            <th>Anggota</th>
                            <th>Buku</th>
                            <th>Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Kembali</th>
                            <th>Status</th>
                            <th>Denda</th>
                            <th>Terkumpul</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_data == 0) { ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted">Tidak ada data untuk filter ini</td>
                            </tr>
                        <?php } ?>

                        <?php foreach ($data_laporan as $r) { ?>
                            <tr>
                                <td><?= date('d-m-Y', strtotime($r['tgl_pengajuan'])) ?></td>
                                <td><?= htmlspecialchars($r['nama']) ?></td>
                                <td><?= htmlspecialchars($r['judul']) ?></td>
                                <td><?= $r['tgl_pinjam'] ? date('d-m-Y', strtotime($r['tgl_pinjam'])) : '-' ?></td>
                                <td><?= $r['tgl_jatuh_tempo'] ? date('d-m-Y', strtotime($r['tgl_jatuh_tempo'])) : '-' ?></td>
                                <td><?= $r['tgl_kembali'] ? date('d-m-Y', strtotime($r['tgl_kembali'])) : '-' ?></td>
                                <td><?= badge_status($r['status']) ?></td>
                                <td><?= $r['denda'] > 0 ? 'Rp' . number_format($r['denda'], 0, ',', '.') : '-' ?></td>
                                <td><?= $r['terkumpul'] > 0 ? 'Rp' . number_format($r['terkumpul'], 0, ',', '.') : '-' ?></td>
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
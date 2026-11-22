<?php
session_start();
include '../config/koneksi.php';
include '../includes/auth_check.php';
include_once '../includes/functions.php';
wajib_role('anggota');

$user_id = $_SESSION['user_id'];
if (isset($_POST['bayar'])) {
    $peminjaman_id = (int) $_POST['peminjaman_id'];
    $q = mysqli_prepare(
        $koneksi,
        "SELECT denda FROM peminjaman
         WHERE id = ? AND user_id = ? AND status_denda = 'belum_lunas'"
    );
    mysqli_stmt_bind_param($q, 'ii', $peminjaman_id, $user_id);
    mysqli_stmt_execute($q);
    $hasil = mysqli_stmt_get_result($q);

    if (mysqli_num_rows($hasil) == 0) {
        set_pesan('danger', 'Data denda tidak ditemukan');
        header("Location: denda.php"); exit();
    }

    $pinjam = mysqli_fetch_assoc($hasil);
    $q = mysqli_prepare(
        $koneksi,
        "SELECT COUNT(*) AS total FROM pembayaran_denda
         WHERE peminjaman_id = ? AND status = 'menunggu'"
    );
    mysqli_stmt_bind_param($q, 'i', $peminjaman_id);
    mysqli_stmt_execute($q);
    $hasil = mysqli_stmt_get_result($q);

    if (mysqli_fetch_assoc($hasil)['total'] > 0) {
        set_pesan('warning', 'Bukti pembayaran kamu masih menunggu verifikasi');
        header("Location: denda.php"); exit();
    }

    $hasil_upload = upload_gambar($_FILES['bukti'], 'bukti');

    if ($hasil_upload['sukses']) {
        $query = mysqli_prepare(
            $koneksi,
            "INSERT INTO pembayaran_denda (peminjaman_id, jumlah, metode, bukti, status)
             VALUES (?, ?, 'transfer', ?, 'menunggu')"
        );
        mysqli_stmt_bind_param($query, 'iis', $peminjaman_id, $pinjam['denda'], $hasil_upload['nama_file']);
        mysqli_stmt_execute($query);

        set_pesan('success', 'Bukti transfer berhasil dikirim, menunggu verifikasi admin');
    } else {
        set_pesan('danger', $hasil_upload['pesan']);
    }

    header("Location: denda.php"); 
    exit();
}

$query = mysqli_prepare(
    $koneksi,
    "SELECT p.id, p.denda, p.tgl_kembali, b.judul,
            (SELECT pd.status FROM pembayaran_denda pd
             WHERE pd.peminjaman_id = p.id AND pd.metode = 'transfer'
             ORDER BY pd.id DESC LIMIT 1) AS status_bayar,
            (SELECT pd.catatan FROM pembayaran_denda pd
             WHERE pd.peminjaman_id = p.id AND pd.metode = 'transfer'
             ORDER BY pd.id DESC LIMIT 1) AS catatan_bayar
     FROM peminjaman p
     JOIN buku b ON p.buku_id = b.id
     WHERE p.user_id = ? AND p.status_denda = 'belum_lunas'
     ORDER BY p.tgl_kembali ASC"
);
mysqli_stmt_bind_param($query, 'i', $user_id);
mysqli_stmt_execute($query);
$data_denda = mysqli_stmt_get_result($query);

$judul = 'Denda Saya';
include '../includes/header.php';
?>

<h3 class="mb-3">Denda Saya</h3>
<div class="alert alert-info">
    <strong>Rekening perpustakaan</strong><br>
    Bank BRI 1234-5678-9012 a.n. Perpustakaan PustakaKu<br>
    <small>Transfer sesuai nominal denda, lalu unggah bukti transfer (JPG/PNG, maks 2 MB).
    Pembayaran tunai bisa dilakukan langsung ke admin perpustakaan.</small>
</div>
<?php if (mysqli_num_rows($data_denda) == 0) { ?>
    <div class="alert alert-success">Kamu tidak memiliki denda yang belum lunas.</div>
<?php } ?>
<?php while ($d = mysqli_fetch_assoc($data_denda)) { ?>
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between flex-wrap">
                <div>
                    <h5 class="mb-1"><?= htmlspecialchars($d['judul']) ?></h5>
                    <p class="text-muted mb-2">Dikembalikan: <?= date('d-m-Y', strtotime($d['tgl_kembali'])) ?></p>
                </div>
                <h4 class="text-danger">Rp<?= number_format($d['denda'], 0, ',', '.') ?></h4>
            </div>
            <?php if ($d['status_bayar'] == 'menunggu') { ?>
                <span class="badge text-bg-warning">Menunggu verifikasi admin</span>
            <?php } else { ?>
                <?php if ($d['status_bayar'] == 'ditolak') { ?>
                    <div class="alert alert-danger py-2">
                        Pembayaran sebelumnya ditolak.
                        Alasan: <?= htmlspecialchars((string) $d['catatan_bayar']) ?><br>
                        Silakan unggah bukti yang baru.
                    </div>
                <?php } ?>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="peminjaman_id" value="<?= $d['id'] ?>">
                    <div class="mb-2">
                        <label class="form-label">Bukti transfer</label>
                        <input type="file" name="bukti" class="form-control input-bukti" data-target="preview<?= $d['id'] ?>" accept=".jpg,.jpeg,.png" required>
                    </div>
                    <img src="" id="preview<?= $d['id'] ?>" alt="preview bukti" style="display: none; max-width: 200px;" class="mb-2 rounded border">
                    <div>
                        <button type="submit" name="bayar" class="btn btn-primary">Kirim Bukti Transfer</button>
                    </div>
                </form>
            <?php } ?>
        </div>
    </div>
<?php } ?>

<?php 
include '../includes/footer.php'; 
?>
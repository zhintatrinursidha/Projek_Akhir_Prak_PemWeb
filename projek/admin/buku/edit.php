<?php
session_start();
include '../../config/koneksi.php';
include '../../includes/auth_check.php';
include_once '../../includes/functions.php';
wajib_role('admin');

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$q = mysqli_prepare($koneksi, "SELECT * FROM buku WHERE id = ?");
mysqli_stmt_bind_param($q, 'i', $id);
mysqli_stmt_execute($q);
$hasil_buku = mysqli_stmt_get_result($q);

if (mysqli_num_rows($hasil_buku) == 0) {
    set_pesan('danger', 'Buku tidak ditemukan');
    header("Location: index.php"); exit();
}

$buku = mysqli_fetch_assoc($hasil_buku);

$kode_buku = $buku['kode_buku'];
$judul_buku = $buku['judul'];
$penulis = $buku['penulis'];
$penerbit = $buku['penerbit'];
$tahun = $buku['tahun_terbit'];
$kategori_id = $buku['kategori_id'];
$stok = $buku['stok'];
$sinopsis = $buku['sinopsis'];
$cover = $buku['cover'];

if (isset($_POST['simpan'])) {
    $kode_buku = trim($_POST['kode_buku']);
    $judul_buku = trim($_POST['judul_buku']);
    $penulis = trim($_POST['penulis']);
    $penerbit = trim($_POST['penerbit']);
    $tahun = $_POST['tahun_terbit'] == "" ? null : (int) $_POST['tahun_terbit'];
    $kategori_id = $_POST['kategori_id'] == "" ? null : (int) $_POST['kategori_id'];
    $stok = (int) $_POST['stok'];
    $sinopsis = trim($_POST['sinopsis']);

    if ($kode_buku == "" || $judul_buku == "") {
        $error = "Kode buku dan judul wajib diisi";
    } else if ($stok < 0) {
        $error = "Stok tidak boleh negatif";
    } else {
        $cek = mysqli_prepare($koneksi, "SELECT id FROM buku WHERE kode_buku = ? AND id != ?");
        mysqli_stmt_bind_param($cek, 'si', $kode_buku, $id);
        mysqli_stmt_execute($cek);
        $hasil_cek = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($hasil_cek) > 0) {
            $error = "Kode buku sudah dipakai";
        } else {
            $cover_baru = $buku['cover'];
            if ($_FILES['cover']['error'] != UPLOAD_ERR_NO_FILE) {
                $hasil_upload = upload_gambar($_FILES['cover'], 'cover');
                if ($hasil_upload['sukses']) {
                    $cover_baru = $hasil_upload['nama_file'];
                } else {
                    $error = $hasil_upload['pesan'];
                }
            }

            if (!isset($error)) {
                $query = mysqli_prepare(
                    $koneksi,
                    "UPDATE buku SET kode_buku = ?, judul = ?, penulis = ?, penerbit = ?,
                     tahun_terbit = ?, kategori_id = ?, stok = ?, sinopsis = ?, cover = ?
                     WHERE id = ?"
                );
                mysqli_stmt_bind_param( $query, 'ssssiiissi', $kode_buku, $judul_buku, $penulis, $penerbit, $tahun, $kategori_id, $stok, $sinopsis, $cover_baru, $id
                );
                mysqli_stmt_execute($query);

                if ($cover_baru != $buku['cover'] && $buku['cover'] != 'no-cover.png') {
                    $file_lama = __DIR__ . '/../../uploads/cover/' . $buku['cover'];
                    if (file_exists($file_lama)) {
                        unlink($file_lama);
                    }
                }

                set_pesan('success', 'Buku berhasil diubah');
                header("Location: index.php"); 
                exit();
            }
        }
    }
}

$judul = 'Ubah Buku';

include '../../includes/header.php';
?>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <h3 class="mb-3">Ubah Buku</h3>
        <?php include '_form.php'; ?>
    </div>
</div>

<?php 
include '../../includes/footer.php';
?>

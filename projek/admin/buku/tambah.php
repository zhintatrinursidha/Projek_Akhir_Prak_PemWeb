<?php
session_start();
include '../../config/koneksi.php';
include '../../includes/auth_check.php';
include_once '../../includes/functions.php';
wajib_role('admin');

$kode_buku = "";
$judul_buku = "";
$penulis = "";
$penerbit = "";
$tahun = null;
$kategori_id = null;
$stok = 0;
$sinopsis = "";
$cover = 'no-cover.png';

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
        $cek = mysqli_prepare($koneksi, "SELECT id FROM buku WHERE kode_buku = ?");
        mysqli_stmt_bind_param($cek, 's', $kode_buku);
        mysqli_stmt_execute($cek);
        $hasil_cek = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($hasil_cek) > 0) {
            $error = "Kode buku sudah dipakai";
        } else {
            $nama_cover = 'no-cover.png';
            if ($_FILES['cover']['error'] != UPLOAD_ERR_NO_FILE) {
                $hasil_upload = upload_gambar($_FILES['cover'], 'cover');
                if ($hasil_upload['sukses']) {
                    $nama_cover = $hasil_upload['nama_file'];
                } else {
                    $error = $hasil_upload['pesan'];
                }
            }

            if (!isset($error)) {
                $query = mysqli_prepare(
                    $koneksi,
                    "INSERT INTO buku (kode_buku, judul, penulis, penerbit, tahun_terbit, kategori_id, stok, sinopsis, cover)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );
                mysqli_stmt_bind_param(
                    $query,
                    'ssssiiiss',
                    $kode_buku, $judul_buku, $penulis, $penerbit,
                    $tahun, $kategori_id, $stok, $sinopsis, $nama_cover
                );
                mysqli_stmt_execute($query);

                set_pesan('success', 'Buku berhasil ditambahkan');
                header("Location: index.php"); 
                exit();
            }
        }
    }
}

$judul = 'Tambah Buku';

include '../../includes/header.php';
?>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <h3 class="mb-3">Tambah Buku</h3>
        <?php include '_form.php'; ?>
    </div>
</div>

<?php 
include '../../includes/footer.php';
?>

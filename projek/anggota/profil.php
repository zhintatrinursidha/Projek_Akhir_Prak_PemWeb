<?php
session_start();
include '../config/koneksi.php';
include '../includes/auth_check.php';
include_once '../includes/functions.php';
wajib_role('anggota');

$user_id = $_SESSION['user_id'];
$q = mysqli_prepare($koneksi, "SELECT * FROM users WHERE id = ?");
mysqli_stmt_bind_param($q, 'i', $user_id);
mysqli_stmt_execute($q);
$hasil = mysqli_stmt_get_result($q);
$user = mysqli_fetch_assoc($hasil);

if (isset($_POST['simpan_profil'])) {
    $nama = trim($_POST['nama']);
    $nim = trim($_POST['nim']);
    $no_hp = trim($_POST['no_hp']);

    if ($nama == "") {
        set_pesan('danger', 'Nama wajib diisi');
        header("Location: profil.php"); exit();
    }

    $foto_baru = $user['foto'];
    if ($_FILES['foto']['error'] != UPLOAD_ERR_NO_FILE) {
        $hasil_upload = upload_gambar($_FILES['foto'], 'foto');
        if ($hasil_upload['sukses']) {
            $foto_baru = $hasil_upload['nama_file'];
        } else {
            set_pesan('danger', $hasil_upload['pesan']);
            header("Location: profil.php"); exit();
        }
    }

    $query = mysqli_prepare(
        $koneksi,
        "UPDATE users SET nama = ?, nim = ?, no_hp = ?, foto = ? WHERE id = ?"
    );
    mysqli_stmt_bind_param($query, 'ssssi', $nama, $nim, $no_hp, $foto_baru, $user_id);
    mysqli_stmt_execute($query);

    if ($foto_baru != $user['foto'] && $user['foto'] != 'default.png') {
        $file_lama = __DIR__ . '/../uploads/foto/' . $user['foto'];
        if (file_exists($file_lama)) {
            unlink($file_lama);
        }
    }

    $_SESSION['nama'] = $nama;
    $_SESSION['foto'] = $foto_baru;

    set_pesan('success', 'Profil berhasil diperbarui');
    header("Location: profil.php"); 
    exit();
}

if (isset($_POST['ganti_password'])) {
    $lama = $_POST['password_lama'];
    $baru = $_POST['password_baru'];
    $konfirmasi = $_POST['konfirmasi'];

    if (!password_verify($lama, $user['password'])) {
        set_pesan('danger', 'Password lama salah');
    } else if (strlen($baru) < 8) {
        set_pesan('danger', 'Password baru minimal 8 karakter');
    } else if ($baru != $konfirmasi) {
        set_pesan('danger', 'Konfirmasi password baru tidak cocok');
    } else {
        $hash = password_hash($baru, PASSWORD_DEFAULT);
        $query = mysqli_prepare(
            $koneksi,
            "UPDATE users SET password = ?, remember_token = NULL WHERE id = ?"
        );
        mysqli_stmt_bind_param($query, 'si', $hash, $user_id);
        mysqli_stmt_execute($query);

        set_pesan('success', 'Password berhasil diubah');
    }

    header("Location: profil.php"); 
    exit();
}

$judul = 'Profil Saya';
include '../includes/header.php';
?>

<h3 class="mb-3">Profil Saya</h3>
<div class="row">
    <div class="col-md-7 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Data Diri</h5>
                <form method="POST" enctype="multipart/form-data">
                    <div class="text-center mb-3">
                        <img src="<?= url_foto($user['foto']) ?>" id="previewFoto" class="foto-profil" alt="foto profil">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto (JPG/PNG, maks 2 MB)</label>
                        <input type="file" name="foto" class="form-control input-bukti" data-target="previewFoto" accept=".jpg,.jpeg,.png">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($user['nama']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">NIM</label>
                        <input type="text" name="nim" class="form-control" value="<?= htmlspecialchars((string) $user['nim']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars((string) $user['no_hp']) ?>">
                    </div>
                    <button type="submit" name="simpan_profil" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-5 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Ubah Password</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Password Lama</label>
                        <input type="password" name="password_lama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password_baru" class="form-control" minlength="8" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="konfirmasi" class="form-control" minlength="8" required>
                    </div>
                    <button type="submit" name="ganti_password" class="btn btn-warning">Ubah Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
include '../includes/footer.php'; 
?>
<?php
session_start();
include '../config/koneksi.php';
include '../includes/auth_check.php';
include_once '../includes/functions.php';
wajib_role('admin');

if (isset($_POST['ubah_status'])) {
    $id = (int) $_POST['id'];
    $status_baru = $_POST['status_baru'] == 'aktif' ? 'aktif' : 'nonaktif';

    $query = mysqli_prepare(
        $koneksi,
        "UPDATE users SET status = ?, remember_token = NULL WHERE id = ? AND role = 'anggota'"
    );
    mysqli_stmt_bind_param($query, 'si', $status_baru, $id);
    mysqli_stmt_execute($query);

    set_pesan('success', 'Status anggota berhasil diubah menjadi ' . $status_baru);
    header("Location: anggota.php"); 
    exit();
}

if (isset($_POST['reset_password'])) {
    $id = (int) $_POST['id'];

    $q = mysqli_prepare($koneksi, "SELECT nama FROM users WHERE id = ? AND role = 'anggota'");
    mysqli_stmt_bind_param($q, 'i', $id);
    mysqli_stmt_execute($q);
    $hasil = mysqli_stmt_get_result($q);

    if (mysqli_num_rows($hasil) == 1) {
        $user = mysqli_fetch_assoc($hasil);

        $password_baru = bin2hex(random_bytes(4));
        $hash = password_hash($password_baru, PASSWORD_DEFAULT);

        $query = mysqli_prepare(
            $koneksi,
            "UPDATE users SET password = ?, remember_token = NULL WHERE id = ?"
        );
        mysqli_stmt_bind_param($query, 'si', $hash, $id);
        mysqli_stmt_execute($query);

        set_pesan('success', 'Password ' . $user['nama'] . ' direset menjadi: ' . $password_baru . ' (catat dan berikan ke anggota)');
    } else {
        set_pesan('danger', 'Anggota tidak ditemukan');
    }

    header("Location: anggota.php");
    exit();
}

$data_anggota = mysqli_query(
    $koneksi,
    "SELECT id, nama, email, nim, no_hp, status FROM users WHERE role = 'anggota' ORDER BY nama"
);

$judul = 'Data Anggota';
include '../includes/header.php';
?>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <a href="<?= $base_url ?>/admin/dashboard.php" class="btn btn-outline-secondary btn-sm mb-3 no-print">
            &larr; Kembali ke Dashboard
        </a>
        <h3 class="mb-3">Data Anggota</h3>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>NIM</th>
                            <th>No. HP</th>
                            <th>Status</th>
                            <th style="width: 220px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($a = mysqli_fetch_assoc($data_anggota)) { ?>
                            <tr>
                                <td><?= htmlspecialchars($a['nama']) ?></td>
                                <td><?= htmlspecialchars($a['email']) ?></td>
                                <td><?= htmlspecialchars((string) $a['nim']) ?></td>
                                <td><?= htmlspecialchars((string) $a['no_hp']) ?></td>
                                <td>
                                    <?php if ($a['status'] == 'aktif') { ?>
                                        <span class="badge text-bg-success">Aktif</span>
                                    <?php } else { ?>
                                        <span class="badge text-bg-secondary">Nonaktif</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <form method="POST" class="d-inline>
                                        <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                        <?php if ($a['status'] == 'aktif') { ?>
                                            <input type="hidden" name="status_baru" value="nonaktif">
                                            <button type="submit" name="ubah_status" class="btn btn-sm btn-outline-secondary">Nonaktifkan</button>
                                        <?php } else { ?>
                                            <input type="hidden" name="status_baru" value="aktif">
                                            <button type="submit" name="ubah_status" class="btn btn-sm btn-outline-success">Aktifkan</button>
                                        <?php } ?>
                                    </form>
                                    <form method="POST" class="d-inline form-konfirmasi"
                                          data-pesan="Reset password <?= htmlspecialchars($a['nama']) ?>?">
                                        <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                        <button type="submit" name="reset_password" class="btn btn-sm btn-warning">Reset Password</button>
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
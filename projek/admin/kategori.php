<?php
session_start();
include '../config/koneksi.php';
include '../includes/auth_check.php';
include_once '../includes/functions.php';
wajib_role('admin');

if (isset($_POST['simpan'])) {
    $id = (int) $_POST['id'];          
    $nama = trim($_POST['nama_kategori']);

    if ($nama == "") {
        set_pesan('danger', 'Nama kategori wajib diisi');
    } else {
        $cek = mysqli_prepare(
            $koneksi,
            "SELECT id FROM kategori WHERE nama_kategori = ? AND id != ?"
        );
        mysqli_stmt_bind_param($cek, 'si', $nama, $id);
        mysqli_stmt_execute($cek);
        $hasil = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($hasil) > 0) {
            set_pesan('danger', 'Nama kategori sudah ada');
        } else if ($id == 0) {
            $query = mysqli_prepare(
                $koneksi,
                "INSERT INTO kategori (nama_kategori) VALUES (?)"
            );
            mysqli_stmt_bind_param($query, 's', $nama);
            mysqli_stmt_execute($query);
            set_pesan('success', 'Kategori berhasil ditambahkan');
        } else {
            $query = mysqli_prepare(
                $koneksi,
                "UPDATE kategori SET nama_kategori = ? WHERE id = ?"
            );
            mysqli_stmt_bind_param($query, 'si', $nama, $id);
            mysqli_stmt_execute($query);
            set_pesan('success', 'Kategori berhasil diubah');
        }
    }

    header("Location: kategori.php"); 
    exit();
}

if (isset($_POST['hapus'])) {
    $id = (int) $_POST['id'];
    $query = mysqli_prepare($koneksi, "DELETE FROM kategori WHERE id = ?");
    mysqli_stmt_bind_param($query, 'i', $id);

    if (mysqli_stmt_execute($query)) {
        set_pesan('success', 'Kategori berhasil dihapus');
    } else {
        set_pesan('danger', 'Kategori gagal dihapus');
    }

    header("Location: kategori.php"); 
    exit();
}

$data_kategori = mysqli_query(
    $koneksi,
    "SELECT k.id, k.nama_kategori, COUNT(b.id) AS jumlah_buku
     FROM kategori k
     LEFT JOIN buku b ON b.kategori_id = k.id
     GROUP BY k.id, k.nama_kategori
     ORDER BY k.nama_kategori"
);

$judul = 'Kategori';

include '../includes/header.php';
?>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <a href="<?= $base_url ?>/admin/dashboard.php" class="btn btn-outline-secondary btn-sm mb-3 no-print">
            &larr; Kembali ke Dashboard
        </a>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Kategori</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalKategori">
                + Tambah Kategori
            </button>
        </div>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Nama Kategori</th>
                            <th>Jumlah Buku</th>
                            <th style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php while ($baris = mysqli_fetch_assoc($data_kategori)) { ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($baris['nama_kategori']) ?></td>
                                <td><?= $baris['jumlah_buku'] ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalKategori"
                                            data-id="<?= $baris['id'] ?>"
                                            data-nama="<?= htmlspecialchars($baris['nama_kategori']) ?>">
                                        Ubah
                                    </button>

                                    <form method="POST" class="d-inline form-hapus"
                                          data-nama="<?= htmlspecialchars($baris['nama_kategori']) ?>">
                                        <input type="hidden" name="id" value="<?= $baris['id'] ?>">
                                        <button type="submit" name="hapus" class="btn btn-sm btn-danger">Hapus</button>
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

<div class="modal fade" id="modalKategori" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="judulModal">Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="idKategori">
                <label class="form-label">Nama Kategori</label>
                <input type="text" name="nama_kategori" id="namaKategori"
                       class="form-control" maxlength="50" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php 
include '../includes/footer.php'; 
?>
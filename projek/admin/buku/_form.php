<?php
$data_kategori = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori");
?>

<?php if (isset($error)) { ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php } ?>

<form method="POST" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Kode Buku</label>
                    <input type="text" name="kode_buku" class="form-control" maxlength="20" required value="<?= htmlspecialchars((string) $kode_buku) ?>">
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label">Judul</label>
                    <input type="text" name="judul_buku" class="form-control" maxlength="200" required  value="<?= htmlspecialchars((string) $judul_buku) ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Penulis</label>
                    <input type="text" name="penulis" class="form-control" maxlength="100" value="<?= htmlspecialchars((string) $penulis) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Penerbit</label>
                    <input type="text" name="penerbit" class="form-control" maxlength="100" value="<?= htmlspecialchars((string) $penerbit) ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tahun Terbit</label>
                    <input type="number" name="tahun_terbit" class="form-control" min="1900" max="2100" value="<?= htmlspecialchars((string) $tahun) ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="kategori_id" class="form-select">
                        <option value="">- Tanpa kategori -</option>
                        <?php while ($k = mysqli_fetch_assoc($data_kategori)) { ?>
                            <option value="<?= $k['id'] ?>" <?= $kategori_id == $k['id'] ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kategori']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stok" class="form-control" min="0" required value="<?= (int) $stok ?>">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Sinopsis</label>
                <textarea name="sinopsis" class="form-control" rows="4"><?= htmlspecialchars((string) $sinopsis) ?></textarea>
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Cover (JPG/PNG, maks 2 MB)</label>
            <div class="mb-3">
                <img src="<?= url_cover($cover) ?>" id="previewCover" class="preview-cover" alt="preview cover">
            </div>
            <input type="file" name="cover" id="inputCover" class="form-control" accept=".jpg,.jpeg,.png">
        </div>
    </div>
    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
    <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<div class="list-group sidebar-admin">
    <a href="<?= $base_url ?>/admin/buku/index.php"
       class="list-group-item list-group-item-action <?= $menu_aktif == 'buku' ? 'active' : '' ?>">Buku</a>
    <a href="<?= $base_url ?>/admin/kategori.php"
       class="list-group-item list-group-item-action <?= $menu_aktif == 'kategori' ? 'active' : '' ?>">Kategori</a>
    <a href="<?= $base_url ?>/admin/anggota.php"
       class="list-group-item list-group-item-action <?= $menu_aktif == 'anggota' ? 'active' : '' ?>">Anggota</a>
    <a href="<?= $base_url ?>/admin/peminjaman.php"
       class="list-group-item list-group-item-action <?= $menu_aktif == 'peminjaman' ? 'active' : '' ?>">Peminjaman</a>
    <a href="<?= $base_url ?>/admin/pengembalian.php"
       class="list-group-item list-group-item-action <?= $menu_aktif == 'pengembalian' ? 'active' : '' ?>">Pengembalian</a>
    <a href="<?= $base_url ?>/admin/denda.php"
       class="list-group-item list-group-item-action <?= $menu_aktif == 'denda' ? 'active' : '' ?>">Denda</a>
    <a href="<?= $base_url ?>/admin/verifikasi_pembayaran.php"
       class="list-group-item list-group-item-action <?= $menu_aktif == 'verifikasi' ? 'active' : '' ?>">Verifikasi Pembayaran</a>
    <a href="<?= $base_url ?>/admin/laporan.php"
       class="list-group-item list-group-item-action <?= $menu_aktif == 'laporan' ? 'active' : '' ?>">Laporan</a>
</div>
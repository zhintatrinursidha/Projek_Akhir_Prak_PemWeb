PustakaKu

Sistem informasi perpustakaan berbasis web. Anggota bisa mencari buku, mengajukan peminjaman, dan membayar denda. Admin mengelola buku, memverifikasi peminjaman, dan melihat laporan.

Teknologi
1. PHP (prosedural, mysqli)
2. MySQL / MariaDB
3. HTML, CSS, JavaScript
4. Bootstrap 5
5. Chart.js (grafik dashboard)
6. XAMPP (Apache + MySQL)

Pengunjung
1. Melihat katalog buku, filter kategori, dan pencarian langsung (live search)
2. Melihat detail buku
3. Register dan login

Anggota
1. Mengajukan peminjaman
2. Dashboard ringkasan dan riwayat peminjaman
3. Membayar denda lewat transfer dengan upload bukti
4. Edit profil, ganti foto, dan ganti password

Admin
1. Dashboard dengan kartu ringkasan dan 3 grafik
2. CRUD kategori dan buku (upload cover, pencarian, pagination)
3. Kelola anggota (aktif/nonaktif, reset password)
4. Verifikasi pengajuan peminjaman dan proses pengembalian
5. Denda otomatis: Rp1.000 per hari keterlambatan
6. Terima pembayaran denda tunai dan verifikasi bukti transfer
7. Laporan peminjaman dengan filter dan tombol cetak

Lainnya
1. Login dengan session, plus cookie "Ingat Saya"
2. Proteksi halaman berdasarkan role (admin dan anggota)
3. Password disimpan dalam bentuk hash (password_hash)
4. Query memakai prepared statement
5. Pencatatan kunjungan, satu catatan per sesi

Akun demo
   Role          Email	Password
1. Admin	    admin@pustakaku.com	admin123
2. Anggota	  andi@mail.com	andi1234
3. Anggota	  budi@mail.com	budi1234
4. Anggota	  chan@mail.com	chan1234

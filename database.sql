-- ============================================================
--  PustakaKu - Sistem Informasi Pengelolaan dan Peminjaman Buku
--  File    : database.sql (versi final)
--  Cara pakai: phpMyAdmin > Import, atau
--              mysql -u root < database.sql
--
--  Akun demo (hanya untuk pengembangan, ganti sebelum dipakai nyata):
--    admin@pustakaku.com   / admin123   (admin)
--    andi@mail.com         / andi1234   (anggota - Andi Surandi)
--    budi@mail.com         / budi1234   (anggota - Budi Manbus)
--    chan@mail.com         / chan1234   (anggota - Chan Sincan)
-- ============================================================

DROP DATABASE IF EXISTS db_perpustakaan;
CREATE DATABASE db_perpustakaan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_perpustakaan;

-- ------------------------------------------------------------
-- 1. users
-- ------------------------------------------------------------
CREATE TABLE users (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  nama           VARCHAR(100) NOT NULL,
  email          VARCHAR(100) NOT NULL UNIQUE,
  password       VARCHAR(255) NOT NULL,               -- hasil password_hash()
  role           ENUM('admin','anggota') NOT NULL DEFAULT 'anggota',
  nim            VARCHAR(20)  NULL,
  no_hp          VARCHAR(15)  NULL,
  foto           VARCHAR(255) NOT NULL DEFAULT 'default.png',
  status         ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  remember_token VARCHAR(64)  NULL,                   -- hash token cookie "Ingat Saya"
  created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 2. kategori
-- ------------------------------------------------------------
CREATE TABLE kategori (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori  VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 3. buku
-- ------------------------------------------------------------
CREATE TABLE buku (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  kode_buku      VARCHAR(20)  NOT NULL UNIQUE,
  judul          VARCHAR(200) NOT NULL,
  penulis        VARCHAR(100) NULL,
  penerbit       VARCHAR(100) NULL,
  tahun_terbit   YEAR NULL,
  kategori_id    INT NULL,
  stok           INT NOT NULL DEFAULT 0,
  sinopsis       TEXT NULL,
  cover          VARCHAR(255) NOT NULL DEFAULT 'no-cover.png',
  created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_buku_kategori FOREIGN KEY (kategori_id)
    REFERENCES kategori(id) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT chk_stok CHECK (stok >= 0)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 4. peminjaman
-- ------------------------------------------------------------
CREATE TABLE peminjaman (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  user_id          INT NOT NULL,
  buku_id          INT NOT NULL,
  tgl_pengajuan    DATE NOT NULL,
  tgl_pinjam       DATE NULL,
  tgl_jatuh_tempo  DATE NULL,
  tgl_kembali      DATE NULL,
  status           ENUM('menunggu','dipinjam','dikembalikan','ditolak') NOT NULL DEFAULT 'menunggu',
  denda            INT NOT NULL DEFAULT 0,
  status_denda     ENUM('tidak_ada','belum_lunas','lunas') NOT NULL DEFAULT 'tidak_ada',
  CONSTRAINT fk_pinjam_user FOREIGN KEY (user_id)
    REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_pinjam_buku FOREIGN KEY (buku_id)
    REFERENCES buku(id) ON DELETE CASCADE,
  INDEX idx_pinjam_status (status),
  INDEX idx_pinjam_user_status (user_id, status),
  INDEX idx_pinjam_denda (status_denda)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 5. pembayaran_denda
-- ------------------------------------------------------------
CREATE TABLE pembayaran_denda (
  id                 INT AUTO_INCREMENT PRIMARY KEY,
  peminjaman_id      INT NOT NULL,
  jumlah             INT NOT NULL,
  metode             ENUM('tunai','transfer') NOT NULL,
  bukti              VARCHAR(255) NULL,               -- nama file, khusus transfer
  status             ENUM('menunggu','lunas','ditolak') NOT NULL DEFAULT 'menunggu',
  catatan            VARCHAR(255) NULL,               -- alasan jika ditolak
  tgl_bayar          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  diverifikasi_oleh  INT NULL,                        -- id admin pemverifikasi
  tgl_verifikasi     DATETIME NULL,
  CONSTRAINT fk_bayar_pinjam FOREIGN KEY (peminjaman_id)
    REFERENCES peminjaman(id) ON DELETE CASCADE,
  CONSTRAINT fk_bayar_admin FOREIGN KEY (diverifikasi_oleh)
    REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_bayar_status (status)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 6. kunjungan
-- ------------------------------------------------------------
CREATE TABLE kunjungan (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NULL,                               -- NULL = pengunjung belum login
  ip_address  VARCHAR(45) NULL,
  halaman     VARCHAR(100) NULL,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_kunjungan_user FOREIGN KEY (user_id)
    REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_kunjungan_tgl (created_at)
) ENGINE=InnoDB;

-- ============================================================
--  DATA AWAL (seed) - tanggal relatif terhadap hari import,
--  sehingga grafik dan denda terlambat selalu terlihat saat demo
-- ============================================================

INSERT INTO users (nama, email, password, role, nim, no_hp) VALUES
('Admin Perpustakaan', 'admin@pustakaku.com', '$2y$10$GS1R7YcehcVCdrx7yyfXxOAjXLJduY/MOrTEhYjAfz4ZJF7bbNZeO', 'admin',   NULL,       '081200000001'),
('Andi Surandi', 'andi@mail.com', '$2y$10$U3PsTBN2Xr7Q3hMjQJNM2.fxDeEDePDHs3eRQSO0NLyOSPKCSww72', 'anggota', '23010001', '081200000002'),
('Budi Manbus', 'budi@mail.com', '$2y$10$XLtVADVFCYZkJ6KTXNQQReVfwCGOaz86CFfLsgMb69ODGm63YeNjG', 'anggota', '23010002', '081200000003'),
('Chan Sincan', 'chan@mail.com', '$2y$10$12JW91L98Tf4oP85hTqDzOVsRzJLUvbxZxrTuGh8x4oMK6acq1l.i', 'anggota', '23010003', '081200000004');

INSERT INTO kategori (nama_kategori) VALUES
('Pemrograman'), ('Jaringan'), ('Basis Data'), ('Novel'), ('Umum');

INSERT INTO buku (kode_buku, judul, penulis, penerbit, tahun_terbit, kategori_id, stok, sinopsis) VALUES
('BK-001', 'Dasar-Dasar Basis Data',        'Rina Kartika',   'Informatika', 2020, 3, 4, 'Pengantar perancangan dan pengelolaan basis data relasional.'),
('BK-002', 'Pemrograman Web dengan PHP',    'Agus Setiawan',  'Andi Offset', 2021, 1, 3, 'Panduan membangun aplikasi web dinamis menggunakan PHP dan MySQL.'),
('BK-003', 'Jaringan Komputer',             'Dedi Hermawan',  'Graha Ilmu',  2019, 2, 2, 'Konsep dasar jaringan, protokol, dan topologi.'),
('BK-004', 'Algoritma dan Struktur Data',   'Maya Lestari',   'Informatika', 2022, 1, 5, 'Algoritma pencarian, pengurutan, dan struktur data dasar.'),
('BK-005', 'Laskar Pelangi',                'Andrea Hirata',  'Bentang',     2005, 4, 1, 'Novel tentang perjuangan anak-anak Belitung meraih pendidikan.'),
('BK-006', 'HTML, CSS, dan Bootstrap',      'Tono Wijaya',    'Elex Media',  2023, 1, 5, 'Membuat tampilan web responsif dengan Bootstrap 5.'),
('BK-007', 'JavaScript untuk Pemula',       'Lina Marlina',   'Informatika', 2022, 1, 4, 'Dasar JavaScript dan manipulasi DOM.'),
('BK-008', 'Keamanan Sistem Informasi',     'Hendra Gunawan', 'Andi Offset', 2021, 2, 3, 'Dasar keamanan aplikasi dan jaringan.'),
('BK-009', 'Bumi Manusia',                  'Pramoedya A. T.','Hasta Mitra', 1980, 4, 2, 'Novel sejarah pada masa kolonial.'),
('BK-010', 'Metodologi Penelitian',         'Siti Aminah',    'Alfabeta',    2018, 5, 3, 'Panduan menyusun penelitian dan karya ilmiah.');

-- Peminjaman (tgl pakai CURDATE agar selalu relevan)
INSERT INTO peminjaman
 (user_id, buku_id, tgl_pengajuan, tgl_pinjam, tgl_jatuh_tempo, tgl_kembali, status, denda, status_denda) VALUES
-- dikembalikan tepat waktu
(2, 1, DATE_SUB(CURDATE(), INTERVAL 41 DAY), DATE_SUB(CURDATE(), INTERVAL 40 DAY), DATE_SUB(CURDATE(), INTERVAL 33 DAY), DATE_SUB(CURDATE(), INTERVAL 34 DAY), 'dikembalikan', 0,    'tidak_ada'),
(3, 1, DATE_SUB(CURDATE(), INTERVAL 26 DAY), DATE_SUB(CURDATE(), INTERVAL 25 DAY), DATE_SUB(CURDATE(), INTERVAL 18 DAY), DATE_SUB(CURDATE(), INTERVAL 19 DAY), 'dikembalikan', 0,    'tidak_ada'),
(4, 4, DATE_SUB(CURDATE(), INTERVAL 16 DAY), DATE_SUB(CURDATE(), INTERVAL 15 DAY), DATE_SUB(CURDATE(), INTERVAL 8 DAY),  DATE_SUB(CURDATE(), INTERVAL 10 DAY), 'dikembalikan', 0,    'tidak_ada'),
(2, 2, DATE_SUB(CURDATE(), INTERVAL 12 DAY), DATE_SUB(CURDATE(), INTERVAL 11 DAY), DATE_SUB(CURDATE(), INTERVAL 4 DAY),  DATE_SUB(CURDATE(), INTERVAL 5 DAY),  'dikembalikan', 0,    'tidak_ada'),
-- dikembalikan terlambat, denda sudah lunas (tunai)
(2, 2, DATE_SUB(CURDATE(), INTERVAL 31 DAY), DATE_SUB(CURDATE(), INTERVAL 30 DAY), DATE_SUB(CURDATE(), INTERVAL 23 DAY), DATE_SUB(CURDATE(), INTERVAL 20 DAY), 'dikembalikan', 3000, 'lunas'),
-- dikembalikan terlambat, denda belum lunas (bukti transfer menunggu verifikasi)
(3, 3, DATE_SUB(CURDATE(), INTERVAL 21 DAY), DATE_SUB(CURDATE(), INTERVAL 20 DAY), DATE_SUB(CURDATE(), INTERVAL 13 DAY), DATE_SUB(CURDATE(), INTERVAL 8 DAY),  'dikembalikan', 5000, 'belum_lunas'),
-- sedang dipinjam (normal)
(2, 1, DATE_SUB(CURDATE(), INTERVAL 4 DAY),  DATE_SUB(CURDATE(), INTERVAL 3 DAY),  DATE_ADD(CURDATE(), INTERVAL 4 DAY),  NULL, 'dipinjam', 0, 'tidak_ada'),
-- sedang dipinjam (terlambat 3 hari)
(3, 5, DATE_SUB(CURDATE(), INTERVAL 11 DAY), DATE_SUB(CURDATE(), INTERVAL 10 DAY), DATE_SUB(CURDATE(), INTERVAL 3 DAY),  NULL, 'dipinjam', 0, 'tidak_ada'),
-- menunggu verifikasi
(4, 2, CURDATE(), NULL, NULL, NULL, 'menunggu', 0, 'tidak_ada'),
-- ditolak
(4, 6, DATE_SUB(CURDATE(), INTERVAL 2 DAY), NULL, NULL, NULL, 'ditolak', 0, 'tidak_ada');

-- Pembayaran denda
INSERT INTO pembayaran_denda
 (peminjaman_id, jumlah, metode, bukti, status, catatan, tgl_bayar, diverifikasi_oleh, tgl_verifikasi) VALUES
(5, 3000, 'tunai',    NULL,                'lunas',    NULL, DATE_SUB(NOW(), INTERVAL 19 DAY), 1,    DATE_SUB(NOW(), INTERVAL 19 DAY)),
(6, 5000, 'transfer', 'bukti_contoh.jpg',  'menunggu', NULL, DATE_SUB(NOW(), INTERVAL 1 DAY),  NULL, NULL);

-- Kunjungan 7 hari terakhir (data contoh untuk grafik)
INSERT INTO kunjungan (user_id, ip_address, halaman, created_at)
SELECT NULL, '127.0.0.1', 'index.php', DATE_SUB(NOW(), INTERVAL d.hari DAY)
FROM (SELECT 0 AS hari UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3
      UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6) d
JOIN (SELECT 1 AS n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
      UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8) k
  ON k.n <= 2 + MOD(d.hari * 3, 7);

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 22 Nov 2026 pada 08.00
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_perpustakaan`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `buku`
--

CREATE TABLE `buku` (
  `id` int(11) NOT NULL,
  `kode_buku` varchar(20) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `penulis` varchar(100) DEFAULT NULL,
  `penerbit` varchar(100) DEFAULT NULL,
  `tahun_terbit` year(4) DEFAULT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `sinopsis` text DEFAULT NULL,
  `cover` varchar(255) NOT NULL DEFAULT 'no-cover.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data untuk tabel `buku`
--

INSERT INTO `buku` (`id`, `kode_buku`, `judul`, `penulis`, `penerbit`, `tahun_terbit`, `kategori_id`, `stok`, `sinopsis`, `cover`, `created_at`) VALUES
(1, 'BK-001', 'Strange Buildings', 'Uketsu', 'Pushkin Vertigo', '2020', 4, 4, 'Novel misteri gelap yang menghimpun 11 kasus bangunan dengan keanehan arsitektur', 'no-cover.png', '2026-11-14 20:11:26'),
(2, 'BK-002', 'Pemrograman Web dengan PHP', 'Andre Pratama', 'DuniaIlkom', '2021', 1, 3, 'Panduan membangun aplikasi web dinamis menggunakan PHP untuk pemula', '6b02740dda83a.jpg', '2026-11-14 20:11:26'),
(3, 'BK-003', 'Jaringan Komputer', 'Dedi Hermawan', 'Graha Ilmu', '2019', 2, 2, 'Konsep dasar jaringan, protokol, dan topologi.', '6b0273c412a8d.jpg', '2026-11-14 20:11:26'),
(4, 'BK-004', 'Dasar-Dasar Algoritma, Struktur Data, dan Pemrograman', 'M. A. Saelon', 'Informatika', '2022', 1, 5, 'Algoritma pencarian, pengurutan, dan struktur data dasar.', '6b026496c91a1.jpg', '2026-11-14 20:11:26'),
(5, 'BK-005', 'Laskar Pelangi', 'Andrea Hirata', 'Bentang', '2005', 4, 0, 'Novel tentang perjuangan anak-anak Belitung meraih pendidikan.', '6b027332c22e9.jpg', '2026-11-14 20:11:26'),
(6, 'BK-006', 'ATLAS Indonesia & Dunia', 'Tono Wijaya', 'Elex Media', '2026', 5, 5, 'Panduan referensi geografis visual yang menyajikan pemetaan wilayah 38 provinsi di Indonesia, peta benua serta negara-negara di dunia, hingga pengetahuan dasar astronomi', '6b0276ade78cf.jpg', '2026-11-14 20:11:26'),
(7, 'BK-007', 'HTML5 & JavaScript untuk Pemula', 'Lina Marlina', 'Informatika', '2022', 1, 4, 'Dasar HTML dan JavaScript sampai bisa', '6b0263f7ed904.png', '2026-11-14 20:11:26'),
(8, 'BK-008', 'Keamanan Informasi dan Jaringan', 'Hendra Gunawan', 'Andi Offset', '2021', 2, 3, 'Dasar keamanan aplikasi dan jaringan.', '6b0263ed109c0.png', '2026-11-14 20:11:26'),
(9, 'BK-009', 'Bumi Manusia', 'Pramoedya A. T.', 'Hasta Mitra', '1980', 4, 2, 'Novel sejarah pada masa kolonial.', '6b0263dd514f1.png', '2026-11-14 20:11:26'),
(10, 'BK-010', 'Metode Penelitian', 'Siti Aminah', 'Alfabeta', '2018', 5, 3, 'Panduan menyusun penelitian dan karya ilmiah.', '6b0263c93c014.png', '2026-11-14 20:11:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`) VALUES
(3, 'Basis Data'),
(2, 'Jaringan'),
(4, 'Novel'),
(1, 'Pemrograman'),
(5, 'Umum');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kunjungan`
--

CREATE TABLE `kunjungan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `halaman` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kunjungan`
--

INSERT INTO `kunjungan` (`id`, `user_id`, `ip_address`, `halaman`, `created_at`) VALUES
(1, NULL, '127.0.0.1', 'index.php', '2026-11-14 20:11:26'),
(2, NULL, '127.0.0.1', 'index.php', '2026-11-13 20:11:26'),
(3, NULL, '127.0.0.1', 'index.php', '2026-11-12 20:11:26'),
(4, NULL, '127.0.0.1', 'index.php', '2026-11-11 20:11:26'),
(5, NULL, '127.0.0.1', 'index.php', '2026-11-10 20:11:26'),
(6, NULL, '127.0.0.1', 'index.php', '2026-11-09 20:11:26'),
(7, NULL, '127.0.0.1', 'index.php', '2026-11-08 20:11:26'),
(8, NULL, '127.0.0.1', 'index.php', '2026-11-14 20:11:26'),
(9, NULL, '127.0.0.1', 'index.php', '2026-11-13 20:11:26'),
(10, NULL, '127.0.0.1', 'index.php', '2026-11-12 20:11:26'),
(11, NULL, '127.0.0.1', 'index.php', '2026-11-11 20:11:26'),
(12, NULL, '127.0.0.1', 'index.php', '2026-11-10 20:11:26'),
(13, NULL, '127.0.0.1', 'index.php', '2026-11-09 20:11:26'),
(14, NULL, '127.0.0.1', 'index.php', '2026-11-08 20:11:26'),
(15, NULL, '127.0.0.1', 'index.php', '2026-11-13 20:11:26'),
(16, NULL, '127.0.0.1', 'index.php', '2026-11-12 20:11:26'),
(17, NULL, '127.0.0.1', 'index.php', '2026-11-11 20:11:26'),
(18, NULL, '127.0.0.1', 'index.php', '2026-11-10 20:11:26'),
(19, NULL, '127.0.0.1', 'index.php', '2026-11-09 20:11:26'),
(20, NULL, '127.0.0.1', 'index.php', '2026-11-08 20:11:26'),
(21, NULL, '127.0.0.1', 'index.php', '2026-11-13 20:11:26'),
(22, NULL, '127.0.0.1', 'index.php', '2026-11-12 20:11:26'),
(23, NULL, '127.0.0.1', 'index.php', '2026-11-11 20:11:26'),
(24, NULL, '127.0.0.1', 'index.php', '2026-11-10 20:11:26'),
(25, NULL, '127.0.0.1', 'index.php', '2026-11-08 20:11:26'),
(26, NULL, '127.0.0.1', 'index.php', '2026-11-13 20:11:26'),
(27, NULL, '127.0.0.1', 'index.php', '2026-11-12 20:11:26'),
(28, NULL, '127.0.0.1', 'index.php', '2026-11-10 20:11:26'),
(29, NULL, '127.0.0.1', 'index.php', '2026-11-08 20:11:26'),
(30, NULL, '127.0.0.1', 'index.php', '2026-11-12 20:11:26'),
(31, NULL, '127.0.0.1', 'index.php', '2026-11-10 20:11:26'),
(32, NULL, '127.0.0.1', 'index.php', '2026-11-08 20:11:26'),
(33, NULL, '127.0.0.1', 'index.php', '2026-11-12 20:11:26'),
(34, NULL, '127.0.0.1', 'index.php', '2026-11-10 20:11:26'),
(35, NULL, '127.0.0.1', 'index.php', '2026-11-12 20:11:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran_denda`
--

CREATE TABLE `pembayaran_denda` (
  `id` int(11) NOT NULL,
  `peminjaman_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `metode` enum('tunai','transfer') NOT NULL,
  `bukti` varchar(255) DEFAULT NULL,
  `status` enum('menunggu','lunas','ditolak') NOT NULL DEFAULT 'menunggu',
  `catatan` varchar(255) DEFAULT NULL,
  `tgl_bayar` datetime NOT NULL DEFAULT current_timestamp(),
  `diverifikasi_oleh` int(11) DEFAULT NULL,
  `tgl_verifikasi` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pembayaran_denda`
--

INSERT INTO `pembayaran_denda` (`id`, `peminjaman_id`, `jumlah`, `metode`, `bukti`, `status`, `catatan`, `tgl_bayar`, `diverifikasi_oleh`, `tgl_verifikasi`) VALUES
(1, 5, 3000, 'tunai', NULL, 'lunas', NULL, '2026-10-27 03:11:26', 1, '2026-10-27 03:11:26'),
(2, 6, 5000, 'transfer', 'bukti_contoh.jpg', 'menunggu', NULL, '2026-11-14 03:11:26', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `buku_id` int(11) NOT NULL,
  `tgl_pengajuan` date NOT NULL,
  `tgl_pinjam` date DEFAULT NULL,
  `tgl_jatuh_tempo` date DEFAULT NULL,
  `tgl_kembali` date DEFAULT NULL,
  `status` enum('menunggu','dipinjam','dikembalikan','ditolak') NOT NULL DEFAULT 'menunggu',
  `denda` int(11) NOT NULL DEFAULT 0,
  `status_denda` enum('tidak_ada','belum_lunas','lunas') NOT NULL DEFAULT 'tidak_ada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `peminjaman`
--

INSERT INTO `peminjaman` (`id`, `user_id`, `buku_id`, `tgl_pengajuan`, `tgl_pinjam`, `tgl_jatuh_tempo`, `tgl_kembali`, `status`, `denda`, `status_denda`) VALUES
(1, 2, 1, '2026-10-05', '2026-10-06', '2026-10-13', '2026-10-12', 'dikembalikan', 0, 'tidak_ada'),
(2, 3, 1, '2026-10-20', '2026-10-21', '2026-10-28', '2026-10-27', 'dikembalikan', 0, 'tidak_ada'),
(3, 4, 4, '2026-10-30', '2026-10-31', '2026-11-07', '2026-11-05', 'dikembalikan', 0, 'tidak_ada'),
(4, 2, 2, '2026-11-03', '2026-11-04', '2026-11-11', '2026-11-10', 'dikembalikan', 0, 'tidak_ada'),
(5, 2, 2, '2026-10-15', '2026-10-16', '2026-10-23', '2026-10-26', 'dikembalikan', 3000, 'lunas'),
(6, 3, 3, '2026-10-25', '2026-10-26', '2026-11-02', '2026-11-07', 'dikembalikan', 5000, 'belum_lunas'),
(7, 2, 1, '2026-11-11', '2026-11-12', '2026-11-19', NULL, 'dipinjam', 0, 'tidak_ada'),
(8, 3, 5, '2026-11-04', '2026-11-05', '2026-11-12', NULL, 'dipinjam', 0, 'tidak_ada'),
(9, 4, 2, '2026-11-15', NULL, NULL, NULL, 'menunggu', 0, 'tidak_ada'),
(10, 4, 6, '2026-11-13', NULL, NULL, NULL, 'ditolak', 0, 'tidak_ada'),
(11, 5, 5, '2026-11-22', '2026-11-22', '2026-11-29', NULL, 'dipinjam', 0, 'tidak_ada');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','anggota') NOT NULL DEFAULT 'anggota',
  `nim` varchar(20) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `foto` varchar(255) NOT NULL DEFAULT 'default.png',
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `remember_token` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `nim`, `no_hp`, `foto`, `status`, `remember_token`, `created_at`) VALUES
(1, 'Admin Perpustakaan', 'admin@pustakaku.com', '$2y$10$GS1R7YcehcVCdrx7yyfXxOAjXLJduY/MOrTEhYjAfz4ZJF7bbNZeO', 'admin', NULL, '081200000001', 'default.png', 'aktif', NULL, '2026-11-14 20:11:26'),
(2, 'Andi Surandi', 'andi@mail.com', '$2y$10$U3PsTBN2Xr7Q3hMjQJNM2.fxDeEDePDHs3eRQSO0NLyOSPKCSww72', 'anggota', '23010001', '081200000002', 'default.png', 'aktif', NULL, '2026-11-14 20:11:26'),
(3, 'Budi Manbus', 'budi@mail.com', '$2y$10$XLtVADVFCYZkJ6KTXNQQReVfwCGOaz86CFfLsgMb69ODGm63YeNjG', 'anggota', '23010002', '081200000003', 'default.png', 'aktif', NULL, '2026-11-14 20:11:26'),
(4, 'Chan Sincan', 'chan@mail.com', '$2y$10$12JW91L98Tf4oP85hTqDzOVsRzJLUvbxZxrTuGh8x4oMK6acq1l.i', 'anggota', '23010003', '081200000004', 'default.png', 'aktif', NULL, '2026-11-14 20:11:26'),
(5, 'Zhinta Trinursidha', '123250050@student.upnyk.ac.id', '$2y$10$p5pWMOr9Gv5kiD1racicVOWtdZFTxHnOZoHjCL/jwJrGOy/pfilge', 'anggota', '123250050', '081578237821', '6b02721188edc.jpg', 'aktif', NULL, '2026-11-21 13:54:06');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_buku` (`kode_buku`),
  ADD KEY `fk_buku_kategori` (`kategori_id`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indeks untuk tabel `kunjungan`
--
ALTER TABLE `kunjungan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_kunjungan_user` (`user_id`),
  ADD KEY `idx_kunjungan_tgl` (`created_at`);

--
-- Indeks untuk tabel `pembayaran_denda`
--
ALTER TABLE `pembayaran_denda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bayar_pinjam` (`peminjaman_id`),
  ADD KEY `fk_bayar_admin` (`diverifikasi_oleh`),
  ADD KEY `idx_bayar_status` (`status`);

--
-- Indeks untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pinjam_buku` (`buku_id`),
  ADD KEY `idx_pinjam_status` (`status`),
  ADD KEY `idx_pinjam_user_status` (`user_id`,`status`),
  ADD KEY `idx_pinjam_denda` (`status_denda`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `buku`
--
ALTER TABLE `buku`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `kunjungan`
--
ALTER TABLE `kunjungan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT untuk tabel `pembayaran_denda`
--
ALTER TABLE `pembayaran_denda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `buku`
--
ALTER TABLE `buku`
  ADD CONSTRAINT `fk_buku_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kunjungan`
--
ALTER TABLE `kunjungan`
  ADD CONSTRAINT `fk_kunjungan_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `pembayaran_denda`
--
ALTER TABLE `pembayaran_denda`
  ADD CONSTRAINT `fk_bayar_admin` FOREIGN KEY (`diverifikasi_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_bayar_pinjam` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjaman` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `fk_pinjam_buku` FOREIGN KEY (`buku_id`) REFERENCES `buku` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pinjam_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

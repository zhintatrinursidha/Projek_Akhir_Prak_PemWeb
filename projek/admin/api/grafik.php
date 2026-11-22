<?php
session_start();
include '../../config/koneksi.php';
include '../../includes/auth_check.php';

header('Content-Type: application/json');

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Akses ditolak']);
    exit();
}

$q = mysqli_query(
    $koneksi,
    "SELECT b.judul, COUNT(p.id) AS jumlah
     FROM peminjaman p
     JOIN buku b ON p.buku_id = b.id
     WHERE p.status IN ('dipinjam', 'dikembalikan')
     GROUP BY b.id, b.judul
     ORDER BY jumlah DESC
     LIMIT 5"
);

$terlaris_label = [];
$terlaris_data = [];
while ($baris = mysqli_fetch_assoc($q)) {
    $terlaris_label[] = $baris['judul'];
    $terlaris_data[] = (int) $baris['jumlah'];
}

$q = mysqli_query(
    $koneksi,
    "SELECT DATE(created_at) AS tanggal, COUNT(*) AS jumlah
     FROM kunjungan
     WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
     GROUP BY DATE(created_at)"
);

$per_tanggal = [];
while ($baris = mysqli_fetch_assoc($q)) {
    $per_tanggal[$baris['tanggal']] = (int) $baris['jumlah'];
}

$kunjungan_label = [];
$kunjungan_data = [];
for ($i = 6; $i >= 0; $i--) {
    $tanggal = date('Y-m-d', strtotime("-$i day"));
    $kunjungan_label[] = date('d/m', strtotime($tanggal));
    $kunjungan_data[] = isset($per_tanggal[$tanggal]) ? $per_tanggal[$tanggal] : 0;
}

$q = mysqli_query(
    $koneksi,
    "SELECT status, COUNT(*) AS jumlah FROM peminjaman GROUP BY status"
);

$status_label = [];
$status_data = [];
while ($baris = mysqli_fetch_assoc($q)) {
    $status_label[] = $baris['status'];
    $status_data[] = (int) $baris['jumlah'];
}

echo json_encode([
    'terlaris' => ['label' => $terlaris_label, 'data' => $terlaris_data],
    'kunjungan' => ['label' => $kunjungan_label, 'data' => $kunjungan_data],
    'status' => ['label' => $status_label, 'data' => $status_data]
]);
<?php
if (isset($koneksi) && !isset($_SESSION['kunjungan_tercatat'])) {
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $ip = $_SERVER['REMOTE_ADDR'];
    $halaman = basename($_SERVER['SCRIPT_NAME']);

    $q = mysqli_prepare(
        $koneksi,
        "INSERT INTO kunjungan (user_id, ip_address, halaman) VALUES (?, ?, ?)"
    );
    mysqli_stmt_bind_param($q, 'iss', $user_id, $ip, $halaman);
    mysqli_stmt_execute($q);

    $_SESSION['kunjungan_tercatat'] = true;
}
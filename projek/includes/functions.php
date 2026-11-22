<?php
date_default_timezone_set('Asia/Jakarta');

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

function bersihkan($teks){
    return htmlspecialchars(trim($teks), ENT_QUOTES, 'UTF-8');
}

function hitung_denda($jatuh_tempo, $tanggal_kembali){
    $selisih_detik = strtotime($tanggal_kembali) - strtotime($jatuh_tempo);
    $hari_terlambat = floor($selisih_detik / (60 * 60 * 24));
    return max(0, $hari_terlambat) * 1000;
}

function set_pesan($tipe, $teks){
    $_SESSION['pesan'] = ['tipe' => $tipe, 'teks' => $teks];
}

function tampil_pesan(){
    if(isset($_SESSION['pesan'])){
        $tipe = $_SESSION['pesan']['tipe'];
        $teks = bersihkan($_SESSION['pesan']['teks']);
        echo "
        <div class='alert alert-$tipe' alert-dismissible fade show' role='alert'>$teks
            <button type = 'button' class = 'btn-close' data-bs-dismiss = 'alert'></button>
        </div>";
        unset($_SESSION['pesan']);
    }
}

function Upload_gambar($file, $folder){
    if($file['error'] !== UPLOAD_ERR_OK){
        return ['sukses' => false, 'pesan' => 'Upload Gagal atau tidak ada file yang dipilih.'];
    }

    if($file['size'] > 2 * 1024 * 1024){
        return ['sukses' => false, 'pesan' => 'Ukuran file Maksimal 2MB.'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $diizinkan = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

    if(!array_key_exists($mime, $diizinkan)){
        return ['sukses' => false, 'pesan' => 'File hanya gambar JPG dan PNG.'];
    }

    $nama_baru = uniqid() . '.' . $diizinkan[$mime];
    $tujuan = __DIR__ . '/../uploads/' . $folder . '/' . $nama_baru;

    if(!move_uploaded_file($file['tmp_name'], $tujuan)){
        return ['sukses' => false, 'pesan' => 'Gagal menyimpan file.'];
    }
    return ['sukses' => true, 'nama_file' => $nama_baru];
}

function url_cover($cover) {
    global $base_url;

    if ($cover == 'no-cover.png') {
        return $base_url . '/uploads/no-cover.png';
    }

    return $base_url . '/uploads/cover/' . $cover;
}

function badge_status($status) {
    $warna = [
        'menunggu' => 'warning',
        'dipinjam' => 'primary',
        'dikembalikan' => 'success',
        'ditolak' => 'danger'
    ];
    return "<span class='badge text-bg-" . $warna[$status] . "'>" . ucfirst($status) . "</span>";
}

function url_foto($foto) {
    if ($foto == 'default.png') {
        return '/projek/uploads/default.png';
    }
    return '/projek/uploads/foto/' . $foto;
}
?>
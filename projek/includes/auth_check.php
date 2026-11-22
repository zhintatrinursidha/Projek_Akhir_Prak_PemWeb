<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['login']) && isset($_COOKIE['remember'])) {
    $token_hash = hash('sha256', $_COOKIE['remember']);

    $query = mysqli_prepare ($koneksi, "SELECT * FROM users WHERE remember_token = ?  AND status = 'aktif'");

    mysqli_stmt_bind_param($query, 's', $token_hash);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);

    if(mysqli_num_roes($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        session_regenerate_id(true);

        $_SESSION['login'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['foto'] = $user['foto'];
    } else {
        setcookie("remember", "", time() - 3600, '/');
    }

}

function wajib_login() {
    if (!isset($_SESSION['login'])) {
        header("Location: /projek/auth/login.php");
        exit();
    }
}

function wajib_role($role) {
    wajib_login();

    if ($_SESSION['role'] != $role) {
        http_response_code(403);
        include __DIR__ . '/../403.php';
        exit();
    }
}
?>
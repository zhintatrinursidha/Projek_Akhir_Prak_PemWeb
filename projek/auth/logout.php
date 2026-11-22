<?php
session_start();
include '../config/koneksi.php';

if (isset($_SESSION['user_id'])) {
    $query = mysqli_prepare(
        $koneksi,
        "UPDATE users SET remember_token = NULL WHERE id = ?"
    );

    mysqli_stmt_bind_param($query, 'i', $_SESSION['user_id']);
    mysqli_stmt_execute($query);
}

setcookie("remember", "", time() - 3600, '/');

session_unset();
session_destroy();

header("Location: login.php");
exit();
?>
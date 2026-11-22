<?php
session_start();
include '../config/koneksi.php';
include '../includes/auth_check.php';

if(isset($_SESSION['login'])) {
    if($_SESSION(['role'] == 'admin')) {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../anggota/dashboard.php");
    }
    exit();
}

if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = mysqli_prepare($koneksi, "SELECT * FROM users WHERE email = ?");

    mysqli_stmt_bind_param($query, 's', $email);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);

    if(mysqli_num_rows($result) == 1) {
        $user =  mysqli_fetch_assoc($result);

        if(password_verify($password, $user['password'])) {
            if($user['status'] == 'aktif') {
                session_regenerate_id(true);

                $_SESSION['login'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['foto'] = $user['foto'];

                if (isset($_POST['ingat'])) {
                    $token = bin2hex(random_bytes(32));
                    $token_hash = hash('sha256', $token);

                    $update = mysqli_prepare(
                        $koneksi,
                        "UPDATE users SET remember_token = ? WHERE id = ?"
                    );
                    mysqli_stmt_bind_param($update, 'si', $token_hash, $user['id']);
                    mysqli_stmt_execute($update);

                    setcookie("remember", $token, time() + (7 * 24 * 60 * 60), '/', '', false, true);
                }

                if($user['role'] == 'admin') {
                    header("Location: ../admin/dashboard.php");
                } else {
                    header("Location: ../anggota/dashboard.php");
                }
                exit();
            } else {
                $error = "Akun kamu nonaktif, silakan hubungi admin";
            }
        } else {
            $error = "Email atau password salah";
        }
    } else {
        $error = "Email atau password salah";
    }
}

$judul = 'login';
include '../includes/header.php';
?>

<div class = "kotak-auth">
    <h4 class = "mb-3">Login</h4>
    <?php if(isset($error)) {?>
        <div class = "alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php } ?>
    <div id="pesanValidasi"></div>
    <form method = "POST" id="formLogin">
        <div class mb-3>
            <label class = "form-label">Email</label>
            <input type = "text" name = "email" class = "form-control" required>
        </div>
        <div class mb-3>
            <label class = "form-label">Password</label>
            <div class = "input-group">
                <input type = "password" name = "password" id = "password" class = "form-control" required>
                <button type = "button" class = "btn btn-outline-secondary" id = "togglePassword">Lihat</button>
            </div>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="ingat" id="ingat" class="form-check-input">
            <label for="ingat" class="form-check-label">Ingat Saya</label>
        </div>
        <button type = "submit" name = "login" class = "btn btn-primary w-100">Login</button>
    </form>
    <p class = "mt-3 mb-0 text-center">Belum Punya Akun? <a href = "register.php">REGISTER</a></p>
</div>

<?php
include '../includes/footer.php';
?>
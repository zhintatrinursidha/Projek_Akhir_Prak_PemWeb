<?php
include '../config/koneksi.php';

if(isset($_POST['register'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $nim = $_POST['nim'];
    $no_hp = $_POST['no_hp'];
    $password = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi'];

    if($nama == "" && $email == "" && $nim == "" && $no_hp == "" && $password == "" && $konfirmasi == "") {
        $error = "Semua kolom wajib diisi";
    } else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format Email Tidak Valid";
    } else if(strlen($password) < 8) {
        $error = "Password Minimal 8 Karakter";
    } else if($password != $konfirmasi) {
        $error = "Konfirmasi Password Tidak Cocok";
    } else {
        $check = mysqli_prepare($koneksi, "SELECT id FROM users WHERE email = ?");

        mysqli_stmt_bind_param($check, 's', $email);
        mysqli_stmt_execute($check);
        $result = mysqli_stmt_get_result($check);

        if(mysqli_num_rows($result) > 0) {
            $error = "Email Sudah Terdaftar";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $query = mysqli_prepare($koneksi, "INSERT INTO users (nama, email, password, nim, no_hp) VALUES (?, ?, ?, ?, ?)");

            mysqli_stmt_bind_param($query, 'sssss', $nama, $email, $hashedPassword, $nim, $no_hp);
            if(mysqli_stmt_execute($query)) {
                $sukses = "Register berhasil, Silakan login";
                $_POST = [];
            } else {
                $error = "Register gagal";
            }
        }
    }
}

$judul = 'Register';
include '../includes/header.php';
?>

<div class = "kotak-auth">
    <h4 class = "mb-3">Daftar Akun</h4>
    <?php if(isset($error)) {?>
        <div class = "alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php } ?>
    <?php if(isset($sukses)) {?>
        <div class = "alert alert-success"><?= htmlspecialchars($sukses) ?></div>
    <?php } ?>
    <div id="pesanValidasi"></div>
    <form method = "POST" id = "formRegister" novalidate>
        <div class = "mb-3">
            <label class = "form-label">Nama Lengkap</label>
            <input type = "text" name = "nama" class = "form-control" value "<?=htmlspecialchars($_POST['nama'] ?? '')?>">
        </div>
        <div class mb-3>
            <label class = "form-label">Email</label>
            <input type = "text" name = "email" class = "form-control" value "<?=htmlspecialchars($_POST['email'] ?? '')?>">
        </div>
        <div class mb-3>
            <label class = "form-label">NIM</label>
            <input type = "text" name = "nim" class = "form-control" value "<?=htmlspecialchars($_POST['nim'] ?? '')?>">
        </div>
        <div class mb-3>
            <label class = "form-label">No. HP</label>
            <input type = "text" name = "no_hp" class = "form-control" value "<?=htmlspecialchars($_POST['no_hp'] ?? '')?>">
        </div>
        <div class mb-3>
            <label class = "form-label">Password</label>
            <input type = "password" name = "password" class = "form-control">
        </div>
        <div class mb-3>
            <label class = "form-label">Konfirmasi Password</label>
            <input type = "password" name = "konfirmasi" class = "form-control mb-3">
        </div>
        <div >
            <button type = "submit" name = "register" class = "btn btn-primary w-100">Daftar</button>
        </div>
    </form>
    <p class = "mt-3 mb-0 text-center">Sudah Punya Akun? <a href = "login.php">LOGIN</a></p>
</div>

<?php 
include '../includes/footer.php';
?>
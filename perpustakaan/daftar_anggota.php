<?php
session_start();
require_once 'config.php';

if(isset($_POST['daftar'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $nama = $_POST['nama_lengkap'];
    $nis = $_POST['nis'];
    $kelas = $_POST['kelas'];
    $no_telp = $_POST['no_telp'];
    
    // Cek username unik
    $cek = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
    if(mysqli_num_rows($cek) > 0) {
        $error = "Username sudah digunakan!";
    } else {
        $query = "INSERT INTO users (username, password, role, nama_lengkap, nis, kelas, no_telp) 
                  VALUES ('$username', '$password', 'siswa', '$nama', '$nis', '$kelas', '$no_telp')";
        if(mysqli_query($conn, $query)) {
            header("Location: login.php?register=success");
            exit();
        } else {
            $error = "Gagal mendaftar: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Anggota Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white text-center py-3">
                    <h4>📝 Pendaftaran Anggota Baru</h4>
                    <small>Siswa / Siswi</small>
                </div>
                <div class="card-body p-4">
                    <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                    <form method="POST">
                        <div class="mb-3"><label>Username</label><input type="text" name="username" class="form-control" required></div>
                        <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                        <div class="mb-3"><label>Nama Lengkap</label><input type="text" name="nama_lengkap" class="form-control" required></div>
                        <div class="mb-3"><label>NIS</label><input type="text" name="nis" class="form-control"></div>
                        <div class="mb-3"><label>Kelas</label><input type="text" name="kelas" class="form-control"></div>
                        <div class="mb-3"><label>No Telepon</label><input type="text" name="no_telp" class="form-control"></div>
                        <button type="submit" name="daftar" class="btn btn-success w-100">Daftar</button>
                        <a href="index.php" class="btn btn-link w-100 mt-2">Kembali ke Login</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
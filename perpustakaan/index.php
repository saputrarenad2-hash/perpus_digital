<?php
session_start();
if(isset($_SESSION['user_id'])) {
    if($_SESSION['role'] == 'admin') header("Location: admin/dashboard.php");
    else header("Location: siswa/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Perpustakaan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0">📚 Perpustakaan Digital Sekolah</h4>
                    <small>Silakan login</small>
                </div>
                <div class="card-body p-4">
                    <?php if(isset($_GET['error'])): ?>
                        <div class="alert alert-danger">Username atau password salah!</div>
                    <?php endif; ?>
                    <?php if(isset($_GET['register'])): ?>
                        <div class="alert alert-success">Pendaftaran berhasil! Silakan login.</div>
                    <?php endif; ?>
                    <form action="proses_login.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2">Login</button>
                    </form>
                    <hr>
                    <p class="text-center mb-0">Belum punya akun? <a href="daftar_anggota.php">Daftar sebagai siswa</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
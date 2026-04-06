<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../login.php");
    exit();
}
require_once '../config.php';

$id = $_SESSION['user_id'];
$success = $error = '';

// Ambil data siswa
$query = "SELECT * FROM users WHERE id = $id AND role = 'siswa'";
$result = mysqli_query($conn, $query);
$siswa = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nis = mysqli_real_escape_string($conn, $_POST['nis']);
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas']);
    $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $password_baru = $_POST['password_baru'];
    $konfirmasi = $_POST['konfirmasi_password'];

    // Cek username unik
    $cek = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username' AND id != $id");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Username sudah digunakan.";
    } else {
        $update = "UPDATE users SET nama_lengkap = '$nama', username = '$username', nis = '$nis', kelas = '$kelas', no_telp = '$no_telp'";
        if (!empty($password_baru)) {
            if ($password_baru !== $konfirmasi) {
                $error = "Password baru tidak cocok.";
            } else {
                $hashed = md5($password_baru);
                $update .= ", password = '$hashed'";
            }
        }
        if (empty($error)) {
            $update .= " WHERE id = $id";
            if (mysqli_query($conn, $update)) {
                $_SESSION['nama'] = $nama;
                $success = "Profil berhasil diperbarui.";
                $siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $id"));
            } else {
                $error = "Gagal update: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">📚 Perpustakaan Digital</a>
        <div class="d-flex gap-3">
            <a href="profil.php" class="btn btn-outline-light btn-sm rounded-pill">👤 Profil</a>
            <a href="../logout.php" class="btn btn-outline-light btn-sm rounded-pill">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="fade-up">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="fw-bold">👤 Profil Saya</h2>
            <a href="dashboard.php" class="btn btn-secondary rounded-pill">← Kembali</a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-pill"><?= $success ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-pill"><?= $error ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">Informasi Akun Siswa</div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($siswa['nama_lengkap']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($siswa['username']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIS</label>
                            <input type="text" name="nis" class="form-control" value="<?= htmlspecialchars($siswa['nis']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kelas</label>
                            <input type="text" name="kelas" class="form-control" value="<?= htmlspecialchars($siswa['kelas']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No Telepon</label>
                            <input type="text" name="no_telp" class="form-control" value="<?= htmlspecialchars($siswa['no_telp']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" value="Siswa" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password Baru (kosongkan jika tidak diubah)</label>
                            <input type="password" name="password_baru" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="konfirmasi_password" class="form-control">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
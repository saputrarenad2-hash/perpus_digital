<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
require_once '../config.php';

$id = $_SESSION['user_id'];
$success = $error = '';

// Ambil data admin saat ini
$query = "SELECT * FROM users WHERE id = $id AND role = 'admin'";
$result = mysqli_query($conn, $query);
$admin = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $password_baru = $_POST['password_baru'];
    $konfirmasi = $_POST['konfirmasi_password'];

    // Cek username unik (kecuali username sendiri)
    $cek_username = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username' AND id != $id");
    if (mysqli_num_rows($cek_username) > 0) {
        $error = "Username sudah digunakan oleh pengguna lain.";
    } else {
        // Update data dasar
        $update = "UPDATE users SET nama_lengkap = '$nama', username = '$username', no_telp = '$no_telp'";
        if (!empty($password_baru)) {
            if ($password_baru !== $konfirmasi) {
                $error = "Password baru dan konfirmasi tidak cocok.";
            } else {
                $hashed = md5($password_baru); // gunakan MD5 sesuai sistem
                $update .= ", password = '$hashed'";
            }
        }
        if (empty($error)) {
            $update .= " WHERE id = $id";
            if (mysqli_query($conn, $update)) {
                $_SESSION['nama'] = $nama; // update session
                $success = "Profil berhasil diperbarui.";
                // Refresh data
                $admin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $id"));
            } else {
                $error = "Gagal memperbarui profil: " . mysqli_error($conn);
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
    <title>Profil Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 p-0 sidebar" style="min-height: 100vh;">
            <div class="text-center py-4">
                <h4 class="text-white mb-0">📚 Perpustakaan</h4>
                <small class="text-white-50">Admin Panel</small>
            </div>
            <nav class="nav flex-column">
                <a href="dashboard.php" class="nav-link"><span>🏠</span> Dashboard</a>
                <a href="kelola_buku.php" class="nav-link"><span>📖</span> Kelola Buku</a>
                <a href="kelola_anggota.php" class="nav-link"><span>👥</span> Kelola Anggota</a>
                <a href="kelola_transaksi.php" class="nav-link"><span>🔄</span> Kelola Transaksi</a>
                <a href="profil.php" class="nav-link active"><span>👤</span> Profil</a>
                <a href="../logout.php" class="nav-link text-danger"><span>🚪</span> Logout</a>
            </nav>
        </div>

        <!-- Konten -->
        <div class="col-md-10 p-4 main-content fade-up">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">👤 Profil Administrator</h2>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-pill" role="alert"><?= $success ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show rounded-pill" role="alert"><?= $error ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Informasi Akun</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($admin['nama_lengkap']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($admin['username']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No Telepon</label>
                                <input type="text" name="no_telp" class="form-control" value="<?= htmlspecialchars($admin['no_telp']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role</label>
                                <input type="text" class="form-control" value="Administrator" disabled>
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
                        <a href="dashboard.php" class="btn btn-secondary rounded-pill px-4">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
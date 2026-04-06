<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .sidebar { background-color: #2c3e50; min-height: 100vh; }
        .sidebar a { color: white; text-decoration: none; display: block; padding: 12px 20px; transition: 0.3s; }
        .sidebar a:hover { background-color: #1abc9c; }
        .content { padding: 20px; }
        .card-menu { transition: transform 0.2s; cursor: pointer; }
        .card-menu:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0 sidebar">
            <h4 class="text-white text-center py-3">📚 Admin Panel</h4>
            <a href="dashboard.php">🏠 Dashboard</a>
            <a href="kelola_buku.php">📖 Kelola Buku</a>
            <a href="kelola_anggota.php">👥 Kelola Anggota</a>
            <a href="kelola_transaksi.php">🔄 Kelola Transaksi</a>
            <!-- <a href="profil.php" class="nav-link"><span>👤</span> Profil</a> -->
            <a href="../logout.php" class="text-danger">🚪 Logout</a>
        </div>
        <div class="col-md-10 content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Selamat datang, <?= $_SESSION['nama'] ?></h2>
                <span class="badge bg-primary">Admin</span>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card card-menu text-center bg-info text-white">
                        <div class="card-body">
                            <h3>📖</h3>
                            <h5>Kelola Buku</h5>
                            <p>Tambah, edit, hapus data buku</p>
                            <a href="kelola_buku.php" class="btn btn-light">Masuk</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card card-menu text-center bg-success text-white">
                        <div class="card-body">
                            <h3>👥</h3>
                            <h5>Kelola Anggota</h5>
                            <p>Data siswa pengguna perpustakaan</p>
                            <a href="kelola_anggota.php" class="btn btn-light">Masuk</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card card-menu text-center bg-warning text-dark">
                        <div class="card-body">
                            <h3>🔄</h3>
                            <h5>Kelola Transaksi</h5>
                            <p>Peminjaman & pengembalian buku</p>
                            <a href="kelola_transaksi.php" class="btn btn-light">Masuk</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
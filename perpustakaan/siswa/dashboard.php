<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../login.php");
    exit();
}
require_once '../config.php';
$id_siswa = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Dashboard Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Tambahan untuk sentuhan interaktif */
        .card-menu:active {
            transform: scale(0.98);
        }
        .btn:active {
            transform: scale(0.96);
        }
    </style>
</head>
<body>

<!-- Navbar dengan toggler untuk mobile -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">📚 Perpus Digital</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSiswa">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSiswa">
            <div class="ms-auto d-flex flex-column flex-lg-row gap-2">
                <span class="text-white">Halo, <?= htmlspecialchars($_SESSION['nama']) ?></span>
                <a href="profil.php" class="btn btn-outline-light btn-sm rounded-pill">👤 Profil</a>
                <a href="../logout.php" class="btn btn-outline-light btn-sm rounded-pill">Logout</a>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <!-- Menu utama 2 tombol besar -->
    <div class="row fade-up g-3">
        <div class="col-6 mb-3">
            <div class="card h-100 text-center shadow-sm card-menu">
                <div class="card-body">
                    <div class="display-1">📖</div>
                    <h5 class="card-title mt-2">Pinjam</h5>
                    <p class="card-text text-muted small">Pinjam buku tersedia</p>
                    <a href="peminjaman_buku.php" class="btn btn-primary rounded-pill w-100">Pinjam</a>
                </div>
            </div>
        </div>
        <div class="col-6 mb-3">
            <div class="card h-100 text-center shadow-sm card-menu">
                <div class="card-body">
                    <div class="display-1">📙</div>
                    <h5 class="card-title mt-2">Kembali</h5>
                    <p class="card-text text-muted small">Kembalikan buku</p>
                    <a href="pengembalian_buku.php" class="btn btn-warning rounded-pill w-100">Kembali</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Buku terbaru (grid 2 kolom di mobile) -->
    <div class="card mt-4 fade-up">
        <div class="card-header bg-primary text-white">📚 Buku Terbaru</div>
        <div class="card-body">
            <div class="row g-2">
                <?php
                $buku_baru = mysqli_query($conn, "SELECT * FROM buku ORDER BY id DESC LIMIT 4");
                while($b = mysqli_fetch_assoc($buku_baru)):
                ?>
                <div class="col-6 col-md-3 mb-2">
                    <a href="detail_buku.php?id=<?= $b['id'] ?>" class="text-decoration-none">
                        <div class="text-center">
                            <?php if($b['sampul'] && file_exists("../uploads/".$b['sampul'])): ?>
                                <img src="../uploads/<?= $b['sampul'] ?>" class="img-fluid rounded shadow-sm" style="height:120px; object-fit:cover; width:100%;">
                            <?php else: ?>
                                <div style="height:120px; background:#e9ecef; display:flex; align-items:center; justify-content:center; border-radius:10px;">📘</div>
                            <?php endif; ?>
                            <p class="mt-1 mb-0 small fw-bold"><?= htmlspecialchars($b['judul']) ?></p>
                            <small class="text-muted">Stok: <?= $b['stok'] ?></small>
                        </div>
                    </a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- Buku dipinjam saat ini -->
    <div class="card mt-4 fade-up">
        <div class="card-header bg-primary text-white">📋 Sedang Dipinjam</div>
        <div class="card-body p-0">
            <?php
            $aktif = mysqli_query($conn, "SELECT t.*, b.judul, b.sampul FROM transaksi t JOIN buku b ON t.id_buku=b.id WHERE t.id_siswa=$id_siswa AND t.status='dipinjam'");
            if(mysqli_num_rows($aktif) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Sampul</th><th>Judul & Tgl Pinjam</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            <?php while($a = mysqli_fetch_assoc($aktif)): ?>
                            <tr>
                                <td style="width:60px">
                                    <?php if($a['sampul'] && file_exists("../uploads/".$a['sampul'])): ?>
                                        <img src="../uploads/<?= $a['sampul'] ?>" width="40" height="50" style="object-fit:cover; border-radius:6px;">
                                    <?php else: ?>📘<?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($a['judul']) ?></strong><br>
                                    <small class="text-muted"><?= $a['tanggal_pinjam'] ?></small>
                                </td>
                                <td>
                                    <a href="pengembalian_buku.php?kembali=<?= $a['id'] ?>" class="btn btn-sm btn-info rounded-pill" onclick="return confirm('Kembalikan?')">Kembali</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-light text-center m-3">Tidak ada buku dipinjam.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
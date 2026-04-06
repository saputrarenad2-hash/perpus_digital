<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'siswa') header("Location: ../login.php");
require_once '../config.php';
$id_siswa = $_SESSION['user_id'];

if(isset($_GET['pinjam'])) {
    $id_buku = $_GET['pinjam'];
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE id_siswa=$id_siswa AND status='dipinjam'"));
    if($cek['total'] >= 3) {
        $error = "Maksimal 3 buku.";
    } else {
        $stok = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stok FROM buku WHERE id=$id_buku"));
        if($stok['stok'] > 0) {
            mysqli_query($conn, "INSERT INTO transaksi (id_buku, id_siswa, tanggal_pinjam, status) VALUES ($id_buku, $id_siswa, CURDATE(), 'dipinjam')");
            mysqli_query($conn, "UPDATE buku SET stok = stok-1 WHERE id=$id_buku");
            $success = "Berhasil dipinjam.";
        } else {
            $error = "Stok habis.";
        }
    }
    header("Location: peminjaman_buku.php?msg=".urlencode($success??$error));
}

$buku = mysqli_query($conn, "SELECT * FROM buku WHERE stok > 0 ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Peminjaman Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">📚 Perpus Digital</a>
        <a href="dashboard.php" class="btn btn-outline-light btn-sm rounded-pill">← Kembali</a>
    </div>
</nav>

<div class="container mt-3">
    <h2 class="fw-bold fs-4 mb-3">📖 Daftar Buku Tersedia</h2>
    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-info alert-dismissible fade show rounded-pill small"><?= htmlspecialchars($_GET['msg']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <div class="row g-3">
        <?php if(mysqli_num_rows($buku) == 0): ?>
            <div class="col-12"><div class="alert alert-warning">Belum ada buku tersedia.</div></div>
        <?php else: ?>
            <?php while($b = mysqli_fetch_assoc($buku)): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <?php if($b['sampul'] && file_exists("../uploads/".$b['sampul'])): ?>
                        <img src="../uploads/<?= $b['sampul'] ?>" class="card-img-top" style="height:160px; object-fit:cover;">
                    <?php else: ?>
                        <div class="card-img-top bg-light text-center py-4">📘</div>
                    <?php endif; ?>
                    <div class="card-body p-2">
                        <h6 class="card-title fw-bold"><?= htmlspecialchars($b['judul']) ?></h6>
                        <p class="card-text small text-muted"><?= htmlspecialchars($b['penulis']) ?></p>
                        <p class="mb-1"><span class="badge bg-success">Stok: <?= $b['stok'] ?></span></p>
                        <div class="d-flex gap-1 mt-2">
                            <a href="detail_buku.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-info rounded-pill flex-grow-1">Detail</a>
                            <a href="?pinjam=<?= $b['id'] ?>" class="btn btn-sm btn-primary rounded-pill flex-grow-1" onclick="return confirm('Pinjam?')">Pinjam</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
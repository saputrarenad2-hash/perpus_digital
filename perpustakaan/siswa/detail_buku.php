<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'siswa') header("Location: ../login.php");
require_once '../config.php';
$id_buku = intval($_GET['id'] ?? 0);
if($id_buku <= 0) header("Location: peminjaman_buku.php");
$query = mysqli_query($conn, "SELECT * FROM buku WHERE id=$id_buku");
$buku = mysqli_fetch_assoc($query);
if(!$buku) header("Location: peminjaman_buku.php");

if(isset($_GET['pinjam'])) {
    $id_siswa = $_SESSION['user_id'];
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE id_siswa=$id_siswa AND status='dipinjam'"));
    if($cek['total'] >= 3) $error = "Maksimal 3 buku.";
    elseif($buku['stok'] <= 0) $error = "Stok habis.";
    else {
        mysqli_query($conn, "INSERT INTO transaksi (id_buku, id_siswa, tanggal_pinjam, status) VALUES ($id_buku, $id_siswa, CURDATE(), 'dipinjam')");
        mysqli_query($conn, "UPDATE buku SET stok = stok-1 WHERE id=$id_buku");
        $success = "Berhasil dipinjam.";
        header("Location: detail_buku.php?id=$id_buku&msg=".urlencode($success));
        exit();
    }
    header("Location: detail_buku.php?id=$id_buku&error=".urlencode($error));
    exit();
}
$msg = $_GET['msg'] ?? '';
$err = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title><?= htmlspecialchars($buku['judul']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">📚 Perpus Digital</a>
        <a href="peminjaman_buku.php" class="btn btn-outline-light btn-sm rounded-pill">← Kembali</a>
    </div>
</nav>
<div class="container mt-3">
    <?php if($msg): ?><div class="alert alert-success rounded-pill small"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if($err): ?><div class="alert alert-danger rounded-pill small"><?= htmlspecialchars($err) ?></div><?php endif; ?>
    
    <div class="card shadow-sm">
        <div class="row g-0">
            <div class="col-12 col-md-4 p-3 text-center">
                <?php if($buku['sampul'] && file_exists("../uploads/".$buku['sampul'])): ?>
                    <img src="../uploads/<?= $buku['sampul'] ?>" class="img-fluid rounded" style="max-height: 250px;">
                <?php else: ?>
                    <div class="bg-light rounded py-5">📘</div>
                <?php endif; ?>
            </div>
            <div class="col-12 col-md-8">
                <div class="card-body">
                    <h2 class="fs-3 fw-bold"><?= htmlspecialchars($buku['judul']) ?></h2>
                    <p class="text-muted">oleh <strong><?= htmlspecialchars($buku['penulis']) ?></strong></p>
                    <hr>
                    <div class="row small mb-3">
                        <div class="col-6">📚 Penerbit: <?= htmlspecialchars($buku['penerbit'] ?: '-') ?></div>
                        <div class="col-6">📅 Tahun: <?= $buku['tahun'] ?: '-' ?></div>
                        <div class="col-6 mt-1">📦 Stok: <span class="badge bg-<?= $buku['stok']>0?'success':'danger' ?>"><?= $buku['stok'] ?></span></div>
                    </div>
                    <h6>📝 Sinopsis</h6>
                    <p class="small"><?= nl2br(htmlspecialchars($buku['sinopsis'] ?: 'Belum ada sinopsis.')) ?></p>
                    <?php if($buku['stok'] > 0): ?>
                        <a href="?pinjam=1" class="btn btn-primary rounded-pill w-100" onclick="return confirm('Pinjam buku ini?')">📖 Pinjam Sekarang</a>
                    <?php else: ?>
                        <button class="btn btn-secondary rounded-pill w-100" disabled>Stok Habis</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
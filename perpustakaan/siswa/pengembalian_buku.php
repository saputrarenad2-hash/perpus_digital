<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'siswa') header("Location: ../login.php");
require_once '../config.php';
$id_siswa = $_SESSION['user_id'];

if(isset($_GET['kembali'])) {
    $id_trans = $_GET['kembali'];
    mysqli_query($conn, "UPDATE transaksi SET tanggal_kembali=CURDATE(), status='dikembalikan' WHERE id=$id_trans AND id_siswa=$id_siswa");
    $tr = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_buku FROM transaksi WHERE id=$id_trans"));
    mysqli_query($conn, "UPDATE buku SET stok = stok+1 WHERE id=".$tr['id_buku']);
    header("Location: pengembalian_buku.php?success=1");
}

$pinjaman = mysqli_query($conn, "SELECT t.*, b.judul, b.sampul FROM transaksi t JOIN buku b ON t.id_buku=b.id WHERE t.id_siswa=$id_siswa AND t.status='dipinjam'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Pengembalian Buku</title>
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
    <h2 class="fw-bold fs-4 mb-3">📙 Pengembalian Buku</h2>
    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success rounded-pill small">Buku berhasil dikembalikan.</div>
    <?php endif; ?>

    <?php if(mysqli_num_rows($pinjaman) == 0): ?>
        <div class="alert alert-warning rounded-pill">Tidak ada buku yang dipinjam.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr><th>Sampul</th><th>Judul Buku</th><th>Tgl Pinjam</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php while($p = mysqli_fetch_assoc($pinjaman)): ?>
                    <tr>
                        <td style="width:60px">
                            <?php if($p['sampul'] && file_exists("../uploads/".$p['sampul'])): ?>
                                <img src="../uploads/<?= $p['sampul'] ?>" width="40" height="50" style="object-fit:cover; border-radius:6px;">
                            <?php else: ?>📘<?php endif; ?>
                        </td>
                        <td><strong><?= htmlspecialchars($p['judul']) ?></strong><br><small><?= $p['tanggal_pinjam'] ?></small></td>
                        <td><span class="badge bg-warning text-dark">Dipinjam</span></td>
                        <td><a href="?kembali=<?= $p['id'] ?>" class="btn btn-sm btn-success rounded-pill" onclick="return confirm('Kembalikan?')">Kembali</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
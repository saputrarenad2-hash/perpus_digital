<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') header("Location: ../login.php");
require_once '../config.php';

if(isset($_GET['kembali'])) {
    $id = $_GET['kembali'];
    $tgl = date('Y-m-d');
    mysqli_query($conn, "UPDATE transaksi SET tanggal_kembali='$tgl', status='dikembalikan' WHERE id=$id");
    $tr = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_buku FROM transaksi WHERE id=$id"));
    mysqli_query($conn, "UPDATE buku SET stok = stok+1 WHERE id=".$tr['id_buku']);
    header("Location: kelola_transaksi.php");
}

if(isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM transaksi WHERE id=$id");
    header("Location: kelola_transaksi.php");
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pinjam'])) {
    $id_siswa = $_POST['id_siswa'];
    $id_buku = $_POST['id_buku'];
    $cekStok = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stok FROM buku WHERE id=$id_buku"));
    if($cekStok['stok'] > 0) {
        mysqli_query($conn, "INSERT INTO transaksi (id_buku, id_siswa, tanggal_pinjam, status) VALUES ('$id_buku','$id_siswa', CURDATE(), 'dipinjam')");
        mysqli_query($conn, "UPDATE buku SET stok = stok-1 WHERE id=$id_buku");
        $msg = "Peminjaman berhasil.";
    } else {
        $msg = "Stok buku habis!";
    }
    header("Location: kelola_transaksi.php?msg=".urlencode($msg));
}

$transaksi = mysqli_query($conn, "SELECT t.*, b.judul, u.nama_lengkap FROM transaksi t JOIN buku b ON t.id_buku=b.id JOIN users u ON t.id_siswa=u.id ORDER BY t.tanggal_pinjam DESC");
$siswa = mysqli_query($conn, "SELECT id, nama_lengkap FROM users WHERE role='siswa'");
$buku_tersedia = mysqli_query($conn, "SELECT id, judul, stok FROM buku WHERE stok > 0");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Transaksi - Admin</title>
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
                <a href="dashboard.php" class="nav-link">
                    <span>🏠</span> Dashboard
                </a>
                <a href="kelola_buku.php" class="nav-link">
                    <span>📖</span> Kelola Buku
                </a>
                <a href="kelola_anggota.php" class="nav-link">
                    <span>👥</span> Kelola Anggota
                </a>
                <a href="kelola_transaksi.php" class="nav-link active">
                    <span>🔄</span> Kelola Transaksi
                </a>
                <a href="../logout.php" class="nav-link text-danger">
                    <span>🚪</span> Logout
                </a>
            </nav>
        </div>

        <!-- Konten Utama -->
        <div class="col-md-10 p-4 main-content fade-up">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">📋 Manajemen Transaksi</h2>
                <button class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalPinjam">
                    + Peminjaman Baru
                </button>
            </div>

            <?php if(isset($_GET['msg'])): ?>
                <div class="alert alert-info alert-dismissible fade show rounded-pill" role="alert">
                    <?= htmlspecialchars($_GET['msg']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr><th>ID</th><th>Siswa</th><th>Buku</th><th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Status</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($transaksi)): ?>
                        <tr class="fade-in">
                            <td><?= $row['id'] ?></td>
                            <td><strong><?= htmlspecialchars($row['nama_lengkap']) ?></strong></td>
                            <td><?= htmlspecialchars($row['judul']) ?></td>
                            <td><?= $row['tanggal_pinjam'] ?></td>
                            <td><?= $row['tanggal_kembali'] ?? '-' ?></td>
                            <td>
                                <?php if($row['status'] == 'dipinjam'): ?>
                                    <span class="badge bg-warning text-dark">Dipinjam</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Dikembalikan</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($row['status'] == 'dipinjam'): ?>
                                    <a href="?kembali=<?= $row['id'] ?>" class="btn btn-sm btn-info rounded-pill" onclick="return confirm('Kembalikan buku ini?')">↩️ Kembalikan</a>
                                <?php endif; ?>
                                <a href="?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger rounded-pill" onclick="return confirm('Hapus transaksi?')">🗑️ Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Peminjaman -->
<div class="modal fade" id="modalPinjam" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">📖 Form Peminjaman Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Siswa</label>
                        <select name="id_siswa" class="form-select" required>
                            <option value="">-- Pilih Siswa --</option>
                            <?php while($s = mysqli_fetch_assoc($siswa)): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama_lengkap']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pilih Buku (Stok > 0)</label>
                        <select name="id_buku" class="form-select" required>
                            <option value="">-- Pilih Buku --</option>
                            <?php while($b = mysqli_fetch_assoc($buku_tersedia)): ?>
                                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['judul']) ?> (Stok: <?= $b['stok'] ?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="pinjam" class="btn btn-primary">Pinjam</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
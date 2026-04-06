<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') header("Location: ../login.php");
require_once '../config.php';

if(isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM users WHERE id=$id AND role='siswa'");
    header("Location: kelola_anggota.php");
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $nama = $_POST['nama_lengkap'];
    $nis = $_POST['nis'];
    $kelas = $_POST['kelas'];
    $no_telp = $_POST['no_telp'];
    if(isset($_POST['id']) && $_POST['id'] != '') {
        $id = $_POST['id'];
        if(!empty($_POST['password'])) {
            $pass = md5($_POST['password']);
            mysqli_query($conn, "UPDATE users SET username='$username', password='$pass', nama_lengkap='$nama', nis='$nis', kelas='$kelas', no_telp='$no_telp' WHERE id=$id");
        } else {
            mysqli_query($conn, "UPDATE users SET username='$username', nama_lengkap='$nama', nis='$nis', kelas='$kelas', no_telp='$no_telp' WHERE id=$id");
        }
    } else {
        $pass = md5($_POST['password']);
        mysqli_query($conn, "INSERT INTO users (username, password, role, nama_lengkap, nis, kelas, no_telp) VALUES ('$username','$pass','siswa','$nama','$nis','$kelas','$no_telp')");
    }
    header("Location: kelola_anggota.php");
}

$data = mysqli_query($conn, "SELECT * FROM users WHERE role='siswa' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Anggota - Admin</title>
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
                <a href="kelola_anggota.php" class="nav-link active">
                    <span>👥</span> Kelola Anggota
                </a>
                <a href="kelola_transaksi.php" class="nav-link">
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
                <h2 class="fw-bold">👥 Data Anggota (Siswa)</h2>
                <button class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAnggota">
                    + Tambah Anggota
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr><th>ID</th><th>NIS</th><th>Nama Lengkap</th><th>Kelas</th><th>No Telepon</th><th>Username</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($data)): ?>
                        <tr class="fade-in">
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['nis']) ?></td>
                            <td><strong><?= htmlspecialchars($row['nama_lengkap']) ?></strong></td>
                            <td><?= htmlspecialchars($row['kelas']) ?></td>
                            <td><?= htmlspecialchars($row['no_telp']) ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning rounded-pill editBtn"
                                    data-id="<?= $row['id'] ?>"
                                    data-username="<?= htmlspecialchars($row['username']) ?>"
                                    data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                    data-nis="<?= htmlspecialchars($row['nis']) ?>"
                                    data-kelas="<?= htmlspecialchars($row['kelas']) ?>"
                                    data-telp="<?= htmlspecialchars($row['no_telp']) ?>">
                                    ✏️ Edit
                                </button>
                                <a href="?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger rounded-pill" onclick="return confirm('Yakin hapus anggota ini?')">🗑️ Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Anggota -->
<div class="modal fade" id="modalAnggota" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">👤 Form Anggota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="anggotaId">
                    <div class="mb-3"><label class="form-label">Username</label><input type="text" name="username" id="username" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Password (kosongkan jika tidak diubah)</label><input type="password" name="password" id="password" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Nama Lengkap</label><input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">NIS</label><input type="text" name="nis" id="nis" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Kelas</label><input type="text" name="kelas" id="kelas" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">No Telepon</label><input type="text" name="no_telp" id="no_telp" class="form-control"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('anggotaId').value = this.dataset.id;
            document.getElementById('username').value = this.dataset.username;
            document.getElementById('nama_lengkap').value = this.dataset.nama;
            document.getElementById('nis').value = this.dataset.nis;
            document.getElementById('kelas').value = this.dataset.kelas;
            document.getElementById('no_telp').value = this.dataset.telp;
            document.getElementById('password').value = '';
            new bootstrap.Modal(document.getElementById('modalAnggota')).show();
        });
    });
</script>
</body>
</html>
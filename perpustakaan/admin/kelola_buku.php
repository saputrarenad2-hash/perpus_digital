<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') header("Location: ../login.php");
require_once '../config.php';
$sinopsis = mysqli_real_escape_string($conn, $_POST['sinopsis']);
// Hapus buku beserta file sampul
if(isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    // Ambil nama file sampul
    $query = mysqli_query($conn, "SELECT sampul FROM buku WHERE id=$id");
    $data = mysqli_fetch_assoc($query);
    if($data['sampul'] && file_exists("../uploads/".$data['sampul'])) {
        unlink("../uploads/".$data['sampul']); // hapus file fisik
    }
    mysqli_query($conn, "DELETE FROM buku WHERE id=$id");
    header("Location: kelola_buku.php");
}

// Hapus sampul saja
if(isset($_GET['hapus_sampul'])) {
    $id = $_GET['hapus_sampul'];
    $query = mysqli_query($conn, "SELECT sampul FROM buku WHERE id=$id");
    $data = mysqli_fetch_assoc($query);
    if($data['sampul'] && file_exists("../uploads/".$data['sampul'])) {
        unlink("../uploads/".$data['sampul']);
    }
    mysqli_query($conn, "UPDATE buku SET sampul = NULL WHERE id=$id");
    header("Location: kelola_buku.php");
}

// Proses tambah/edit buku (dengan upload gambar)
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $penulis = mysqli_real_escape_string($conn, $_POST['penulis']);
    $penerbit = mysqli_real_escape_string($conn, $_POST['penerbit']);
    $tahun = intval($_POST['tahun']);
    $stok = intval($_POST['stok']);
    $id_buku = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    
    // Proses upload gambar
    $sampul_name = null;
    if(isset($_FILES['sampul']) && $_FILES['sampul']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_name = $_FILES['sampul']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if(in_array($file_ext, $allowed)) {
            $new_name = time() . '_' . rand(1000, 9999) . '.' . $file_ext;
            $target = "../uploads/" . $new_name;
            if(move_uploaded_file($_FILES['sampul']['tmp_name'], $target)) {
                $sampul_name = $new_name;
            }
        }
    }
    
    if($id_buku > 0) {
        // Update buku
        $update = "UPDATE buku SET judul='$judul', penulis='$penulis', penerbit='$penerbit', tahun='$tahun', stok='$stok'";
        if($sampul_name) {
            // Hapus sampul lama
            $query = mysqli_query($conn, "SELECT sampul FROM buku WHERE id=$id_buku");
            $old = mysqli_fetch_assoc($query);
            if($old['sampul'] && file_exists("../uploads/".$old['sampul'])) {
                unlink("../uploads/".$old['sampul']);
            }
            $update .= ", sampul='$sampul_name'";
        }
        $update .= " WHERE id=$id_buku";
        mysqli_query($conn, $update);
    } else {
        // Insert baru
        $query = "INSERT INTO buku (judul, penulis, penerbit, tahun, stok, sampul) VALUES ('$judul','$penulis','$penerbit','$tahun','$stok',";
        $query .= $sampul_name ? "'$sampul_name')" : "NULL)";
        mysqli_query($conn, $query);
    }
    header("Location: kelola_buku.php");
}

$data = mysqli_query($conn, "SELECT * FROM buku ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Buku - Admin</title>
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
                <a href="kelola_buku.php" class="nav-link active"><span>📖</span> Kelola Buku</a>
                <a href="kelola_anggota.php" class="nav-link"><span>👥</span> Kelola Anggota</a>
                <a href="kelola_transaksi.php" class="nav-link"><span>🔄</span> Kelola Transaksi</a>
                <a href="profil.php" class="nav-link"><span>👤</span> Profil</a>
                <a href="../logout.php" class="nav-link text-danger"><span>🚪</span> Logout</a>
            </nav>
        </div>

        <!-- Konten Utama -->
        <div class="col-md-10 p-4 main-content fade-up">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">📖 Data Buku</h2>
                <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalBuku">
                    + Tambah Buku
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr><th>ID</th><th>Sampul</th><th>Judul</th><th>Penulis</th><th>Stok</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($data)): ?>
                        <tr class="fade-in">
                            <td><?= $row['id'] ?></td>
                            <td style="width: 80px;">
                                <?php if($row['sampul'] && file_exists("../uploads/".$row['sampul'])): ?>
                                    <img src="../uploads/<?= $row['sampul'] ?>" width="60" height="70" style="object-fit: cover; border-radius: 8px;">
                                <?php else: ?>
                                    <div style="width:60px;height:70px;background:#eee;border-radius:8px;display:flex;align-items:center;justify-content:center;">📘</div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($row['judul']) ?></strong><br><small class="text-muted"><?= htmlspecialchars($row['penulis']) ?></small></td>
                            <td><?= $row['stok'] ?> <span class="badge bg-secondary">stok</span></td>
                            <td>
                                <button class="btn btn-sm btn-warning rounded-pill editBtn" 
                                    data-id="<?= $row['id'] ?>"
                                    data-judul="<?= htmlspecialchars($row['judul']) ?>"
                                    data-penulis="<?= htmlspecialchars($row['penulis']) ?>"
                                    data-penerbit="<?= htmlspecialchars($row['penerbit']) ?>"
                                    data-tahun="<?= $row['tahun'] ?>"
                                    data-stok="<?= $row['stok'] ?>"
                                    data-sampul="<?= $row['sampul'] ?>">
                                    ✏️ Edit
                                </button>
                                <?php if($row['sampul']): ?>
                                    <a href="?hapus_sampul=<?= $row['id'] ?>" class="btn btn-sm btn-secondary rounded-pill" onclick="return confirm('Hapus sampul buku?')">🖼️ Hapus Sampul</a>
                                <?php endif; ?>
                                <a href="?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger rounded-pill" onclick="return confirm('Yakin hapus buku?')">🗑️ Hapus</a>
                             </td>
                         </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form Buku dengan upload gambar -->
<div class="modal fade" id="modalBuku" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">📘 Form Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="bukuId">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3"><label class="form-label">Judul Buku</label><input type="text" name="judul" id="judul" class="form-control" required></div>
                            <div class="mb-3"><label class="form-label">Penulis</label><input type="text" name="penulis" id="penulis" class="form-control" required></div>
                            <div class="mb-3"><label class="form-label">Penerbit</label><input type="text" name="penerbit" id="penerbit" class="form-control"></div>
                            <div class="row">
                                <div class="col-md-6"><label class="form-label">Tahun</label><input type="number" name="tahun" id="tahun" class="form-control"></div>
                                <div class="col-md-6"><label class="form-label">Stok</label><input type="number" name="stok" id="stok" class="form-control" required></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Sampul Buku</label>
                                <input type="file" name="sampul" id="sampulInput" class="form-control" accept="image/*">
                                <div class="mt-2 text-center">
                                    <img id="previewSampul" src="" style="max-width:100%; max-height:120px; display:none; border-radius:8px; border:1px solid #ddd;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Preview gambar
    document.getElementById('sampulInput').addEventListener('change', function(e) {
        const preview = document.getElementById('previewSampul');
        if(this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                preview.src = ev.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(this.files[0]);
        } else {
            preview.style.display = 'none';
        }
    });

    // Edit button: isi form dan tampilkan preview sampul jika ada
    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('bukuId').value = this.dataset.id;
            document.getElementById('judul').value = this.dataset.judul;
            document.getElementById('penulis').value = this.dataset.penulis;
            document.getElementById('penerbit').value = this.dataset.penerbit;
            document.getElementById('tahun').value = this.dataset.tahun;
            document.getElementById('stok').value = this.dataset.stok;
            document.getElementById('sinopsis').value = this.dataset.sinopsis || '';
            <!-- Setelah field stok -->
            <div class="mb-3">
                <label class="form-label">Sinopsis Buku</label>
                <textarea name="sinopsis" id="sinopsis" class="form-control" rows="4" placeholder="Tulis sinopsis buku di sini..."></textarea>
            </div>
            data-sinopsis="<?= htmlspecialchars($row['sinopsis']) ?>"
            const sampul = this.dataset.sampul;
            const preview = document.getElementById('previewSampul');
            if(sampul && sampul !== '') {
                preview.src = '../uploads/' + sampul;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
            new bootstrap.Modal(document.getElementById('modalBuku')).show();
        });
    });
</script>
</body>
</html>
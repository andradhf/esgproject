<?php
include "../config/config.php";
session_start();

// ✅ Cek login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$umkm_id = $_SESSION['umkm_id'];

// ✅ Ambil data UMKM existing
$stmt = $conn->prepare("SELECT * FROM umkm_accounts WHERE id = ?");
$stmt->bind_param("i", $umkm_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

// ✅ Jika form disubmit → update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama_umkm  = $_POST['nama_umkm'];
    $pemilik    = $_POST['pemilik'];
    $email      = $_POST['email'];
    $telepon    = $_POST['telepon'];
    $alamat     = $_POST['alamat'];

    $stmt = $conn->prepare("UPDATE umkm_accounts 
                            SET nama_umkm=?, nama_pemilik=?, email=?, no_telepon=?, alamat=? 
                            WHERE id=?");
    $stmt->bind_param("sssssi", $nama_umkm, $pemilik, $email, $telepon, $alamat, $umkm_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Profil berhasil diperbarui!";
        header("Location: profile.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal update profil!";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile UMKM</title>
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../css/sb-admin-2.min.css" rel="stylesheet">
  <style>
    body { background: #f8f9fc; }
    .card-profile { border-radius: 15px; overflow: hidden; }
    .card-header { background: linear-gradient(90deg, #1565c0, #26a69a); color: white; }
    .list-group-item { border: none; border-bottom: 1px solid #eee; }
    .list-group-item:last-child { border-bottom: none; }
  </style>
</head>
<body>

<div class="container mt-4">
  <!-- Tombol Back -->
  <div class="mb-3">
    <button onclick="window.history.back()" class="btn btn-secondary">
      <i class="fas fa-arrow-left"></i> Back
    </button>
  </div>

  <div class="row">
    <!-- Form Profile -->
    <div class="col-md-6">
      <div class="card card-profile shadow mb-4">
        <div class="card-header py-3">
          <h4 class="m-0 font-weight-bold">
            <i class="fas fa-user-circle"></i> Profile UMKM
          </h4>
        </div>
        <div class="card-body">
          <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
          <?php elseif (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
          <?php endif; ?>

          <form method="POST" action="profile.php">
            <div class="form-group">
              <label for="nama_umkm">Nama UMKM</label>
              <input type="text" name="nama_umkm" class="form-control" 
                value="<?= htmlspecialchars($data['nama_umkm'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label for="pemilik">Nama Pemilik</label>
              <input type="text" name="pemilik" class="form-control" 
                value="<?= htmlspecialchars($data['nama_pemilik'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" name="email" class="form-control" 
                value="<?= htmlspecialchars($data['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label for="telepon">No. Telepon</label>
              <input type="text" name="telepon" class="form-control" 
                value="<?= htmlspecialchars($data['no_telepon'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label for="alamat">Alamat</label>
              <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($data['alamat'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-block">
              <i class="fas fa-save"></i> Simpan
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Data Profil -->
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-header bg-success text-white">
          <h5 class="m-0 font-weight-bold"><i class="fas fa-check-circle"></i> Data Profil</h5>
        </div>
        <div class="card-body">
          <ul class="list-group">
            <li class="list-group-item"><strong>Nama UMKM:</strong> <?= !empty($data['nama_umkm']) ? htmlspecialchars($data['nama_umkm']) : '-' ?></li>
            <li class="list-group-item"><strong>Pemilik:</strong> <?= !empty($data['nama_pemilik']) ? htmlspecialchars($data['nama_pemilik']) : '-' ?></li>
            <li class="list-group-item"><strong>Email:</strong> <?= !empty($data['email']) ? htmlspecialchars($data['email']) : '-' ?></li>
            <li class="list-group-item"><strong>Telepon:</strong> <?= !empty($data['no_telepon']) ? htmlspecialchars($data['no_telepon']) : '-' ?></li>
            <li class="list-group-item"><strong>Alamat:</strong> <?= !empty($data['alamat']) ? htmlspecialchars($data['alamat']) : '-' ?></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>

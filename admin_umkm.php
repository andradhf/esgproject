<?php
include 'koneksi.php';
session_start();

// cek role admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

// kalau form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_umkm     = $_POST['nama_umkm'];
    $nama_pemilik  = $_POST['nama_pemilik'];
    $email         = $_POST['email'];
    $no_telepon    = $_POST['no_telepon'];
    $alamat        = $_POST['alamat'];

    $sql = "INSERT INTO umkm_accounts (nama_umkm, nama_pemilik, email, no_telepon, alamat)
            VALUES ('$nama_umkm', '$nama_pemilik', '$email', '$no_telepon', '$alamat')";

    if ($conn->query($sql) === TRUE) {
        header("Location: admin_umkm.php?status=sukses");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah UMKM</title>
    <link rel="stylesheet" href="assets/css/sb-admin-2.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Tambah Data UMKM</h2>
    <form method="POST">
        <div class="form-group">
            <label>Nama UMKM</label>
            <input type="text" name="nama_umkm" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Nama Pemilik</label>
            <input type="text" name="nama_pemilik" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label>No. Telepon</label>
            <input type="text" name="no_telepon" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="admin_umkm.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>

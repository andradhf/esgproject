<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start(); // WAJIB: supaya $_SESSION bisa dipakai

include "../config/config.php";

// Cek login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $karyawan           = intval($_POST['jumlah_karyawan']);
    $karyawan_perempuan = intval($_POST['jumlah_karyawan_perempuan']);
    $insiden_k3         = intval($_POST['jumlah_insiden']);
    $kejadian           = $conn->real_escape_string($_POST['kejadian']);
    $pelatihan_sdm      = intval($_POST['jam_pelatihan']);
    $produk_halal       = isset($_POST['produk_halal']) ? $conn->real_escape_string($_POST['produk_halal']) : "";
    $csr                = isset($_POST['csr']) ? $conn->real_escape_string($_POST['csr']) : "";
    $ziswaf_input       = floatval($_POST['ziswaf']);

    // user isi 90 → simpan 90000
    $ziswaf = $ziswaf_input * 1000;

    // hapus data lama dulu
    $conn->query("TRUNCATE TABLE data_sosial");

    $stmt = $conn->prepare("INSERT INTO data_sosial 
        (karyawan, karyawan_perempuan, insiden_k3, kejadian, pelatihan_sdm, produk_halal, csr, ziswaf) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "iiiisssd", 
        $karyawan, $karyawan_perempuan, $insiden_k3, $kejadian, 
        $pelatihan_sdm, $produk_halal, $csr, $ziswaf
    );
    $stmt->execute();
    $stmt->close();

    header("Location: lihatsosial.php");
    exit;
}
$conn->close();
?>

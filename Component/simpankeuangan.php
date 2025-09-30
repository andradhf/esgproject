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
$pendapatan_halal      = $_POST['pendapatan_halal'] ?? '';
$maqasid_pendapatan    = isset($_POST['maqasid_pendapatan']) ? implode(", ", $_POST['maqasid_pendapatan']) : '';
$ziswaf                = isset($_POST['ziswaf']) ? floatval($_POST['ziswaf']) : 0.00;
$maqasid_ziswaf        = isset($_POST['maqasid_ziswaf']) ? implode(", ", $_POST['maqasid_ziswaf']) : '';
$pembiayaan            = $_POST['pembiayaan'] ?? '';
$maqasid_pembiayaan    = isset($_POST['maqasid_pembiayaan']) ? implode(", ", $_POST['maqasid_pembiayaan']) : '';

$sql = "INSERT INTO keuangan 
        (pendapatan_halal, maqasid_pendapatan, ziswaf, maqasid_ziswaf, pembiayaan, maqasid_pembiayaan, created_at) 
        VALUES ('$pendapatan_halal', '$maqasid_pendapatan', '$ziswaf', '$maqasid_ziswaf', '$pembiayaan', '$maqasid_pembiayaan', NOW())";

if ($conn->query($sql) === TRUE) {
    echo "✅ Data berhasil disimpan!";
    header("Location: lihatkeuangan.php");
    exit;
} else {
    echo "❌ Error: " . $conn->error;
}

$conn->close();

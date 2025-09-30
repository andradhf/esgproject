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
    // Ambil inputan form
    $legalitas          = $conn->real_escape_string($_POST['legalitas']);          // input teks 1/0
    $kepatuhan_syariah  = $conn->real_escape_string($_POST['kepatuhan_syariah']);  // input teks 1/0
    $transparansi       = $conn->real_escape_string($_POST['transparansi']);       // input teks x/bln
    $integritas         = $conn->real_escape_string($_POST['integritas']);         // input teks 1/0

    // Hapus data lama (opsional)
    $conn->query("TRUNCATE TABLE data_governance");

    // Simpan ke database
    $stmt = $conn->prepare("INSERT INTO data_governance (legalitas, kepatuhan_syariah, transparansi, integritas) 
                            VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $legalitas, $kepatuhan_syariah, $transparansi, $integritas);
    $stmt->execute();
    $stmt->close();

    // Redirect setelah simpan
    header("Location: lihatgovernance.php");
    exit;
}

$conn->close();
?>

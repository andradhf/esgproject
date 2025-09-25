<?php
include "config.php";
session_start();

// cek login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

// ambil id user
$umkm_id = $_SESSION['umkm_id'];

// jika form disubmit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $listrik = isset($_POST['listrik']) ? (int)$_POST['listrik'] : 0;
    $air = isset($_POST['air']) ? (int)$_POST['air'] : 0;
    $limbah = isset($_POST['limbah']) ? (int)$_POST['limbah'] : 0;
    $bahan = isset($_POST['bahan_baku']) ? (int)$_POST['bahan_baku'] : 0;

    // simpan ke database
    $stmt = $conn->prepare("INSERT INTO environmental (umkm_id, listrik, air, limbah, bahan_baku, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiiii", $umkm_id, $listrik, $air, $limbah, $bahan);
    $stmt->execute();
    $stmt->close();

    // redirect ke hasil
    header("Location: hasil_environmental.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Monitoring ESG - ENVIROMENTAL</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .bg-gradient-primary {
            background: linear-gradient(180deg, #1565c0 0%, #26a69a 100%) !important;
        }
        .submit-btn {
            background-color: teal;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: darkcyan;
        }
    </style>
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon">
                    <img src="img/logo_uhamka.png" alt="UHAMKA" style="width:70px; height:auto;">
                </div>
                <div class="sidebar-brand-text mx-3"> ESG Syariah UMKM</div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item active">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>
            <hr class="sidebar-divider">
            <li class="nav-item active">
                <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Pilar ESG</span>
                </a>
                <div id="collapseUtilities" class="collapse show" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Pilar ESG:</h6>
                        <a class="collapse-item active" href="environmental.php">Enviromental</a>
                        <a class="collapse-item" href="sosial.php">Sosial</a>
                        <a class="collapse-item" href="governance.php">Governance</a>
                        <a class="collapse-item" href="syariah.php">Keuangan Syariah</a>
                    </div>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="maqasid_syariah.php">
                    <i class="fas fa-balance-scale"></i>
                    <span>Maqasid Syariah</span>
                </a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <div class="container-fluid">
                    <h1 class="h3 mb-1 text-gray-800">ENVIRONMENTAL</h1>
                    <form action="environmental.php" method="post">
                        <p><strong>A. ENERGI</strong></p>
                        <div style="display: flex; align-items: center; margin-bottom: 10px;">
                            <label for="listrik" style="width: 250px;">1. Konsumsi Listrik Perbulan (kWh)</label>
                            <input type="text" id="listrik" name="listrik" style="margin-right: 20px;">
                            <label><input type="checkbox" name="hifzmal"> Hifz al-Mal (Energi)</label>
                        </div>

                        <p><strong>B. AIR</strong></p>
                        <div style="display: flex; align-items: center; margin-bottom: 10px;">
                            <label for="air" style="width: 250px;">1. Konsumsi Air Perbulan (m3)</label>
                            <input type="text" id="air" name="air" style="margin-right: 20px;">
                            <label><input type="checkbox" name="hifzmal2"> Hifz al-Mal (Air)</label>
                        </div>

                        <p><strong>C. LIMBAH</strong></p>
                        <div style="display: flex; align-items: center; margin-bottom: 10px;">
                            <label for="limbah" style="width: 250px;">1. Volume Limbah Padat/Cair per bulan</label>
                            <input type="text" id="limbah" name="limbah" style="margin-right: 20px;">
                            <label><input type="checkbox" name="hifznafs"> Hifz al-Nafs (Limbah)</label>
                        </div>

                        <p><strong>D. BAHAN BAKU</strong></p>
                        <div style="display: flex; align-items: center; margin-bottom: 10px;">
                            <label for="bahan" style="width: 250px;">1. Persentase Bahan Ramah Lingkungan (%)</label>
                            <input type="text" id="bahan" name="bahan_baku" style="margin-right: 20px;">
                            <label><input type="checkbox" name="hifzdin"> Hifz al-Din (Bahan)</label>
                        </div>

                        <button type="submit" class="submit-btn">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

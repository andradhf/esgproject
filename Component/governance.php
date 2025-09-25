<?php
include '../config/config.php'; // koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $umkm_id = 1; // sementara fixed

    $legalitas = $_POST['legalitas'] ?? 0;
    $kepatuhan_syariah = $_POST['kepatuhan_syariah'] ?? 0;
    $transparansi = $_POST['transparansi'] ?? 0;
    $integritas = $_POST['integritas'] ?? 0;

    $maqasid_legalitas = isset($_POST['maqasid_legalitas']) ? implode(", ", $_POST['maqasid_legalitas']) : '';
    $maqasid_syariah = isset($_POST['maqasid_syariah']) ? implode(", ", $_POST['maqasid_syariah']) : '';
    $maqasid_transparansi = isset($_POST['maqasid_transparansi']) ? implode(", ", $_POST['maqasid_transparansi']) : '';
    $maqasid_integritas = isset($_POST['maqasid_integritas']) ? implode(", ", $_POST['maqasid_integritas']) : '';

    $sql = "INSERT INTO data_governance 
        (umkm_id, legalitas, kepatuhan_syariah, transparansi, integritas,
         maqasid_legalitas, maqasid_syariah, maqasid_transparansi, maqasid_integritas)
        VALUES (?,?,?,?,?,?,?,?,?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "iiiiissss",
        $umkm_id,
        $legalitas,
        $kepatuhan_syariah,
        $transparansi,
        $integritas,
        $maqasid_legalitas,
        $maqasid_syariah,
        $maqasid_transparansi,
        $maqasid_integritas
    );

    if ($stmt->execute()) {
        echo "<script>alert('Data governance berhasil disimpan!'); window.location.href='lihatgovernance.php';</script>";
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>

<!-- ====== HTML asli governance (tanpa perubahan UI/UX) ====== -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SB Admin 2 - Governance</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .bg-gradient-primary {
            background: linear-gradient(180deg, #1565c0 0%, #26a69a 100%) !important;
        }
        .section {background:#fff;padding:15px;margin-bottom:15px;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,0.1);}
        label{display:block;margin:6px 0 3px;}
        input[type="text"],input[type="number"]{width:200px;padding:6px;margin-bottom:8px;border:1px solid #ccc;border-radius:4px;}
        .checkbox-group{display:flex;flex-wrap:wrap;gap:12px;margin-top:8px;}
        .submit-btn{background:#00796b;color:white;padding:10px 20px;border:none;border-radius:6px;cursor:pointer;margin-top:20px;}
        .submit-btn:hover{background:#005f56;}
    </style>
</head>
<body id="page-top">

<div id="wrapper">
    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="../index.php">
            <div class="sidebar-brand-icon">
                <img src="../img/logo_uhamka.png" alt="UHAMKA" style="width:70px; height:auto;">
            </div>
            <div class="sidebar-brand-text mx-3"> ESG Syariah UMKM</div>
        </a>
        <hr class="sidebar-divider my-0">
        <li class="nav-item"><a class="nav-link" href="../index.php"><i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span></a></li>
        <hr class="sidebar-divider">
        <li class="nav-item active">
            <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
                <i class="fas fa-fw fa-wrench"></i><span>Pilar ESG</span>
            </a>
            <div id="collapseUtilities" class="collapse show" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Pilar ESG:</h6>
                    <a class="collapse-item" href="environmental.php">Enviromental</a>
                    <a class="collapse-item" href="sosial.php">Sosial</a>
                    <a class="collapse-item active" href="governance.php">Governance</a>
                    <a class="collapse-item" href="keuangan.php">Keuangan Syariah</a>
                </div>
            </div>
        </li>
        <li class="nav-item"><a class="nav-link" href="maqasid_syariah.php"><i class="fas fa-balance-scale"></i><span>Maqasid Syariah</span></a></li>
        <hr class="sidebar-divider d-none d-md-block">
    </ul>
    <!-- End Sidebar -->

    <!-- Content -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow"></nav>
            <div class="container-fluid">
                <h1 class="h3 mb-1 text-gray-800">GOVERNANCE</h1>

                <form method="POST" action="">
                    <div class="section">
                        <h2>A. Legalitas</h2>
                        <label>Apakah mempunyai NIB/NPWP/Sertifikat Halal</label>
                        <input type="text" name="legalitas" placeholder="1/0">
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="maqasid_legalitas[]" value="Hifz al-Din"> Hifz al-Din</label>
                            <label><input type="checkbox" name="maqasid_legalitas[]" value="Hifz al-Mal"> Hifz al-Mal</label>
                        </div>
                    </div>

                    <div class="section">
                        <h2>B. Kepatuhan Syariah</h2>
                        <label>Dewan pengawas syariah aktif</label>
                        <input type="text" name="kepatuhan_syariah" placeholder="1/0">
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="maqasid_syariah[]" value="Hifz al-Din"> Hifz al-Din</label>
                        </div>
                    </div>

                    <div class="section">
                        <h2>C. Transparansi</h2>
                        <label>Frekuensi laporan keuangan</label>
                        <input type="text" name="transparansi" placeholder="x/bln">
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="maqasid_transparansi[]" value="Hifz al-Mal"> Hifz al-Mal</label>
                        </div>
                    </div>

                    <div class="section">
                        <h2>D. Integritas</h2>
                        <label>Apakah ada kebijakan anti-Fraud/SOP</label>
                        <input type="text" name="integritas" placeholder="1/0">
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="maqasid_integritas[]" value="Hifz al-Mal"> Hifz al-Mal</label>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">Submit</button>
                </form>
            </div>
            <footer class="sticky-footer bg-white"><div class="container my-auto"><div class="copyright text-center my-auto"><span>Copyright &copy; ESG UMKM SYARIAH 2025</span></div></div></footer>
        </div>
    </div>
</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
</body>
</html>

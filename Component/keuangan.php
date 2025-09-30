<?php
session_start(); // WAJIB: supaya $_SESSION bisa dipakai

include "../config/config.php";

// Cek login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$umkm_id  = $_SESSION['umkm_id'];
$username = $_SESSION['username']; // ini biar bisa dipakai di topbar

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pendapatan = $_POST['pendapatan'] ?? '';
    $jumlah_ziswaf = $_POST['jumlah_ziswaf'] ?? 0;
    $akad = $_POST['akad'] ?? '';

    $maqasid_pendapatan = isset($_POST['maqasid_pendapatan']) ? implode(", ", $_POST['maqasid_pendapatan']) : '';
    $maqasid_ziswaf = isset($_POST['maqasid_ziswaf']) ? implode(", ", $_POST['maqasid_ziswaf']) : '';
    $maqasid_pembiayaan = isset($_POST['maqasid_pembiayaan']) ? implode(", ", $_POST['maqasid_pembiayaan']) : '';

    $insert = $conn->prepare("INSERT INTO data_keuangan 
        (pendapatan_halal, ziswaf, pembiayaan, maqasid_pendapatan, maqasid_ziswaf, maqasid_pembiayaan) 
        VALUES (?, ?, ?, ?, ?, ?)");
    $insert->bind_param("sissss", $pendapatan, $jumlah_ziswaf, $akad, $maqasid_pendapatan, $maqasid_ziswaf, $maqasid_pembiayaan);
    $insert->execute();

    header("Location: ../Component/lihatkeuangan.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>ESG SYARIAH UMKM - Pilar ESG</title>

    <!-- Custom fonts for this template-->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
          rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .bg-gradient-primary {
            background: linear-gradient(180deg, #1565c0 0%, #26a69a 100%) !important;
        }
         body {
      font-family: Arial, sans-serif;
      margin: 0px;
      background: #f9f9f9;
    }
    h2 {
      font-size: 16px;
      margin: 15px 0 5px;
      color: #333;
    }
    .section {
      background: #fff;
      padding: 15px;
      margin-bottom: 15px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    label {
      display: block;
      margin: 6px 0 3px;
    }
    .row {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 5px 0;
    }
    input[type="number"], input[type="text"] {
      width: 120px;
      padding: 5px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    .checkbox-group {
      margin-top: 5px;
    }
    .submit-btn {
      background: #00796b;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      margin-top: 20px;
    }
    .submit-btn:hover {
      background: #005f56;
    }
    </style>
</head>

<body id="page-top">
<div id="wrapper">
    <!-- Sidebar -->
      <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="../index.php">
                <div class="sidebar-brand-icon">
                    <img src="../img/logo_uhamka.png" alt="UHAMKA" style="width:70px; height:auto;">
                </div>
                <div class="sidebar-brand-text mx-3">ESG Syariah UMKM</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="../index.php">
                    <i class="fas fa-fw fa-th-large"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Nav Item - Pilar ESG -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="false" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-leaf"></i>
                    <span>Pilar ESG</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Pilar ESG</h6>
                        <a class="collapse-item" href="environmental.php">🌿 Environmental</a>
                        <a class="collapse-item" href="sosial.php">👥 Sosial</a>
                        <a class="collapse-item" href="governance.php">⚖️ Governance</a>
                        <a class="collapse-item" href="keuangan.php">💰 Keuangan Syariah</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Maqasid Syariah -->
            <li class="nav-item">
                <a class="nav-link" href="maqasid_syariah.php">
                    <i class="fas fa-balance-scale"></i>
                    <span>Maqasid Syariah</span>
                </a>
            </li>

             <!-- Nav Item - Setting -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
                    aria-expanded="false" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Setting</span>
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">User Menu:</h6>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <!-- Kalau sudah login -->
                            <a class="collapse-item" href="profile.php">Akun</a>
                            <a class="collapse-item" href="logout.php">Logout</a>
                        <?php else: ?>
                            <!-- Kalau belum login -->
                            <a class="collapse-item" href="/login.php">Login</a>
                        <?php endif; ?>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

            <!-- Motto -->
            <div class="sidebar-brand d-flex align-items-center justify-content-">
                <div style="font-size:12px; color:#dbeffa; margin-top:4px;">
                    "Membangun UMKM Berkelanjutan dengan Prinsip ESG Syariah"
                </div>
            </div>
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="">
                <div class="sidebar-brand-icon">
                    <img src="../img/logo_kemendikbud.png" alt="Kemendikbud" style="width:150px; height:auto;">
                </div>
            </a>
        </ul>
        <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
                 <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                                </span>
                                <i class="fa-solid fa-user"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
                <ul class="navbar-nav ml-auto">
                    <div class="topbar-divider d-none d-sm-block"></div>
                    <li class="nav-item dropdown no-arrow">
                    </li>
                </ul>
            </nav>

            <!-- Begin Page Content -->
            <div class="container-fluid">
                <h1 class="h3 mb-1 text-gray-800">KEUANGAN SYARIAH</h1>

                <!-- FORM -->
                 <!-- Form utama -->
  <form action="" method="post">
    
    <!-- A. PENDAPATAN HALAL -->
    <div class="section">
      <h2>A. Pendapatan Halal</h2>
      <label>Rasio pendapatan halal/non halal</label>
      <div class="row">
        <label><input type="radio" name="pendapatan" value="Halal"> Halal</label>
        <label><input type="radio" name="pendapatan" value="Non Halal"> Non Halal</label>
      </div>
      <div class="checkbox-group">
        <label><input type="checkbox" name="maqasid_pendapatan[]" value="Hifz al-Din"> Hifz al-Din</label>
        <label><input type="checkbox" name="maqasid_pendapatan[]" value="Hifz al-Mal"> Hifz al-Mal</label>
      </div>
    </div>

    <!-- B. DISTRIBUSI ZISWAF -->
    <div class="section">
      <h2>B. Distribusi ZISWAF</h2>
      <label>Jumlah Zakat/Infak/Sedekah/Wakaf</label>
      <input type="number" name="jumlah_ziswaf" style="width:250px;">
      <div class="checkbox-group">
        <label><input type="checkbox" name="maqasid_ziswaf[]" value="Hifz al-Din"> Hifz al-Din</label>
        <label><input type="checkbox" name="maqasid_ziswaf[]" value="Hifz al-Mal"> Hifz al-Mal</label>
      </div>
    </div>

    <!-- C. PEMBIAYAAN -->
    <div class="section">
      <h2>C. Pembiayaan</h2>
      <label>Jenis Akad Syariah</label>
      <p>Pilih salah satu:</p>
      <div class="row">
        <label><input type="radio" name="akad" value="Murabahah"> Murabahah</label>
      </div>
      <div class="row">
        <label><input type="radio" name="akad" value="Mudharabah"> Mudharabah</label>
      </div>
      <div class="row">
        <label><input type="radio" name="akad" value="Ijarah"> Ijarah</label>
      </div>
      <div class="checkbox-group">
        <label><input type="checkbox" name="maqasid_pembiayaan[]" value="Hifz al-Mal"> Hifz al-Mal</label>
      </div>
    </div>

    <!-- Tombol submit -->
    <button type="submit" class="submit-btn">Submit</button>
  </form>
            </div>
        </div>
        <footer class="sticky-footer bg-white">
            <div class="container my-auto"><div class="copyright text-center my-auto"><span>Copyright &copy; ESG UMKM 2025</span></div></div>
        </footer>
    </div>
</div>
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>
</body>
</html>

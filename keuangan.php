<?php
include "config.php";

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

    header("Location: lihatkeuangan.php");
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
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
          rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .bg-gradient-primary {
            background: linear-gradient(180deg, #1565c0 0%, #26a69a 100%) !important;
        }
    </style>
</head>

<body id="page-top">
<div id="wrapper">
    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
            <div class="sidebar-brand-icon">
                <img src="img/logo_uhamka.png" alt="UHAMKA" style="width:70px; height:auto;">
            </div>
            <div class="sidebar-brand-text mx-3"> ESG Syariah UMKM</div>
        </a>
        <hr class="sidebar-divider my-0">
        <li class="nav-item"><a class="nav-link" href="index.html"><i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span></a></li>
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
                    <a class="collapse-item" href="utilities-color.html">Eviromental</a>
                    <a class="collapse-item" href="utilities-border.html">Sosial</a>
                    <a class="collapse-item" href="utilities-animation.html">Governanace</a>
                    <a class="collapse-item active" href="utilities-other.html">Keuangan Syariah</a>
                </div>
            </div>
        </li>
        <li class="nav-item"><a class="nav-link" href="maqasid_syariah.php"><i class="fas fa-balance-scale"></i><span>Maqasid Syariah</span></a></li>
        <li class="nav-item"><a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
                                aria-expanded="true" aria-controls="collapsePages">
                <i class="fas fa-fw fa-folder"></i><span>Setting</span></a>
            <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Login Screens:</h6>
                    <a class="collapse-item" href="login.html">Login</a>
                    <a class="collapse-item" href="profile.html">Akun</a>
                </div>
            </div>
        </li>
        <hr class="sidebar-divider d-none d-md-block">
        <div class="text-center d-none d-md-inline"><button class="rounded-circle border-0" id="sidebarToggle"></button></div>
    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
                <ul class="navbar-nav ml-auto">
                    <div class="topbar-divider d-none d-sm-block"></div>
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown"><span class="mr-2 d-none d-lg-inline text-gray-600 small">User UMKM</span>
                            <img class="img-profile rounded-circle" src="img/undraw_profile.svg"></a>
                    </li>
                </ul>
            </nav>

            <!-- Begin Page Content -->
            <div class="container-fluid">
                <h1 class="h3 mb-1 text-gray-800">KEUANGAN SYARIAH</h1>

                <!-- FORM -->
                <form action="" method="post">
                    <div class="card shadow mb-4 p-4">
                        <h2>A. Pendapatan Halal</h2>
                        <label>Rasio pendapatan halal/non halal</label>
                        <div>
                            <label><input type="radio" name="pendapatan" value="Halal"> Halal</label>
                            <label><input type="radio" name="pendapatan" value="Non Halal"> Non Halal</label>
                        </div>
                        <div>
                            <label><input type="checkbox" name="maqasid_pendapatan[]" value="Hifz al-Din"> Hifz al-Din</label>
                            <label><input type="checkbox" name="maqasid_pendapatan[]" value="Hifz al-Mal"> Hifz al-Mal</label>
                        </div>
                    </div>

                    <div class="card shadow mb-4 p-4">
                        <h2>B. Distribusi ZISWAF</h2>
                        <label>Jumlah Zakat/Infak/Sedekah/Wakaf</label>
                        <input type="number" name="jumlah_ziswaf" class="form-control" style="max-width:300px;">
                        <div>
                            <label><input type="checkbox" name="maqasid_ziswaf[]" value="Hifz al-Din"> Hifz al-Din</label>
                            <label><input type="checkbox" name="maqasid_ziswaf[]" value="Hifz al-Mal"> Hifz al-Mal</label>
                        </div>
                    </div>

                    <div class="card shadow mb-4 p-4">
                        <h2>C. Pembiayaan</h2>
                        <label>Jenis Akad Syariah</label>
                        <div><label><input type="radio" name="akad" value="Murabahah"> Murabahah</label></div>
                        <div><label><input type="radio" name="akad" value="Mudharabah"> Mudharabah</label></div>
                        <div><label><input type="radio" name="akad" value="Ijarah"> Ijarah</label></div>
                        <div><label><input type="checkbox" name="maqasid_pembiayaan[]" value="Hifz al-Mal"> Hifz al-Mal</label></div>
                    </div>

                    <button type="submit" class="btn btn-success">Submit</button>
                </form>
            </div>
        </div>
        <footer class="sticky-footer bg-white">
            <div class="container my-auto"><div class="copyright text-center my-auto"><span>Copyright &copy; ESG UMKM 2025</span></div></div>
        </footer>
    </div>
</div>
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
</body>
</html>

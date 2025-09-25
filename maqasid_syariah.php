<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>ESG Syariah - Maqasid Syariah</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Override warna sidebar -->
    <style>
        .bg-gradient-primary {
            background: linear-gradient(180deg, #1565c0 0%, #26a69a 100%) !important;
        }
        .section {
            background: #fff;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        h2 {
            font-size: 18px;
            margin-top: 15px;
            color: #333;
        }
        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 8px;
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

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            
<!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
    <div class="sidebar-brand-icon">
        <img src="img/logo_uhamka.png" alt="UHAMKA" style="width:70px; height:auto;">
    </div>
                <div class="sidebar-brand-text mx-3"> ESG Syariah UMKM</sup></div>
            </a>


            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="index.html">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Pilar ESG</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Pilar ESG:</h6>
                        <a class="collapse-item" href="utilities-color.html">Enviromental</a>
                        <a class="collapse-item" href="utilities-border.html">Sosial</a>
                        <a class="collapse-item" href="utilities-animation.html">Governance</a>
                        <a class="collapse-item" href="utilities-other.html">Keuangan Syariah</a>
                    </div>
                </div>
            </li>

            <!-- Tambahan menu Maqasid Syariah -->
            <li class="nav-item active">
                <a class="nav-link" href="maqasid_syariah.php">
                    <i class="fas fa-balance-scale"></i>
                    <span>Maqasid Syariah</span>
                </a>
            </li>
<!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
                    aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Setting</span>
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Login Screens:</h6>
                        <a class="collapse-item" href="login.html">Login</a>
                         <a class="collapse-item" href="profile.html">Akun</a>
                    </div>
                </div>
            </li>


            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

            
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>


                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

   <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>


                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">User UMKM</span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                    
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Form ESG - Maqasid Syariah</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
      background: #f9f9f9;
    }
    h2 {
      font-size: 18px; /* ukuran judul dikecilkan */
      margin-top: 20px;
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
    input[type="text"], input[type="number"] {
      width: 200px;
      padding: 6px;
      margin-bottom: 8px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    .checkbox-group {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin-top: 8px;
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
    <!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Penilaian Maqasid</title>
  <style>
    .section {margin-bottom: 20px; padding: 10px; border: 1px solid #ddd; border-radius: 8px;}
    .row {margin: 8px 0;}
    .submit-btn {padding: 8px 16px; border: none; border-radius: 6px; background: teal; color: #fff; cursor: pointer;}
    .submit-btn:hover {background: darkcyan;}
  </style>
</head>
<body>
<form method="POST" action="simpan_maqasidsyariah.php">

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800"> MAQASID SYARIAH</h1>

                    <!-- Form Maqasid Syariah -->
                        <form method="POST" action="simpan_maqasidsyariah.php">
    <div class="section">
        <h2>Aspek Maqasid Syariah</h2>
        <label><input type="checkbox" name="hifz_al_din" value="1"> Hifz al-Din (Agama)</label><br>
        <label><input type="checkbox" name="hifz_al_nafs" value="1"> Hifz al-Nafs (Jiwa)</label><br>
        <label><input type="checkbox" name="hifz_al_aql" value="1"> Hifz al-Aql (Akal)</label><br>
        <label><input type="checkbox" name="hifz_al_nasl" value="1"> Hifz al-Nasl (Keturunan)</label><br>
        <label><input type="checkbox" name="hifz_al_mal" value="1"> Hifz al-Mal (Harta)</label><br>
    </div>
   
    <!-- Submit button -->
    <button type="submit" class="submit-btn">Submit</button>
    
</form>

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; ESG UMKM SYARIAH 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

</body>
</html>

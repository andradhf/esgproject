<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "config/config.php"; 
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: Component/login.php");
    exit;
}

$umkm_id = $_SESSION['umkm_id'];

// Ambil data dari tabel pilar_esg_detail khusus user yang login
$env = $conn->query("SELECT SUM(listrik) as total FROM environmental WHERE umkm_id = $umkm_id")->fetch_assoc()['total'] ?? 0;
$sos = $conn->query("SELECT SUM(karyawan) as total FROM data_sosial WHERE umkm_id = $umkm_id")->fetch_assoc()['total'] ?? 0;
$gov = $conn->query("SELECT COUNT(*) as total FROM data_governance WHERE umkm_id = $umkm_id")->fetch_assoc()['total'] ?? 0; 
$syariah = $conn->query("SELECT SUM(pendapatan_halal) as total FROM data_keuangan WHERE umkm_id = $umkm_id")->fetch_assoc()['total'] ?? 0;

// query ambil env, sos, gov, keu terbaru berdasarkan umkm_id
$sql = "SELECT env, sos, gov, keu 
        FROM laporan_esg 
        WHERE umkm_id = ? 
        ORDER BY id DESC 
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $umkm_id);
$stmt->execute();
$result = $stmt->get_result();

// default nilai 0 biar tidak error kalau belum ada data
$env = $sos = $gov = $keu = 0;

if ($row = $result->fetch_assoc()) {
    $env = $row['env'];
    $sos = $row['sos'];
    $gov = $row['gov'];
    $keu = $row['keu'];
}


$sql = "
    SELECT sub.tanggal, sub.avg_score AS rata2
    FROM (
        SELECT DATE(dc.timestamp) AS tanggal,
               dc.avg_score,
               ROW_NUMBER() OVER (
                   PARTITION BY le.umkm_id, DATE(dc.timestamp)
                   ORDER BY dc.timestamp DESC
               ) AS rn
        FROM data_chart dc
        INNER JOIN laporan_esg le ON dc.laporan_id = le.id
        WHERE le.umkm_id = ?
    ) AS sub
    WHERE sub.rn = 1
    ORDER BY sub.tanggal;
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $umkm_id);
$stmt->execute();
$res = $stmt->get_result();

$labels = [];
$scores = [];

while ($row = $res->fetch_assoc()) {
    $labels[] = $row['tanggal'];
    $scores[] = round($row['rata2'], 2);
}

// ambil 12 terakhir aja
if (count($labels) > 12) {
    $labels = array_slice($labels, -12);
    $scores = array_slice($scores, -12);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Monitoring ESG - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <!-- Font Awesome 6 (latest) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Override warna sidebar -->
    <style>
        .bg-gradient-primary {
            background: linear-gradient(180deg, #1565c0 0%, #26a69a 100%) !important;
        }
          @media (max-width: 576px) { /* ukuran HP kecil */
    .sidebar-tagline {
      font-size: 10px !important; /* lebih kecil di HP */
      line-height: 1.2;
    }
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
                <div class="sidebar-brand-text mx-3">ESG Syariah UMKM</div>
            </a>

            <!-- Divider -->
             
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="index.php">
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
                        <a class="collapse-item" href="Component/environmental.php">🌿 Environmental</a>
                        <a class="collapse-item" href="Component/sosial.php">👥 Sosial</a>
                        <a class="collapse-item" href="Component/governance.php">⚖️ Governance</a>
                        <a class="collapse-item" href="Component/keuangan.php">💰 Keuangan Syariah</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Maqasid Syariah -->
            <li class="nav-item">
                <a class="nav-link" href="Component/maqasid_syariah.php">
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
                            <a class="collapse-item" href="Component/profile.php">Akun</a>
                            <a class="collapse-item" href="Component/logout.php">Logout</a>
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
            <div class="sidebar-brand d-flex flex-column align-items-center text-center px-2">
             <div class="sidebar-tagline text-wrap text-break w-100 fs-6 fs-md-5 fs-lg-4" 
                style="font-size:12px; color:#dbeffa; margin-top:4px;">
            "Membangun UMKM Berkelanjutan dengan Prinsip ESG Syariah"
            </div>
            <div class="sidebar-brand-icon my-2">
                <img src="img/logo_kemendikbud.png" alt="Kemendikbud" class="img-fluid" style="max-width:120px;">
            </div>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
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

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Environmental Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Environmental</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php echo number_format($env, 0); ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-leaf fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sosial Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Sosial</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php echo number_format($sos, 0); ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Governance Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Governance</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php echo number_format($gov, 0); ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Keuangan Syariah Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Keuangan Syariah</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php echo number_format($keu, 0); ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Charts Row -->
                    <div class="row">

                        <!-- Area Chart -->
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Data Bulanan</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="myAreaChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pie Chart -->
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Pilar ESG</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2">
                                        <canvas id="myPieChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; ESG UMKM Syariah 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Chart Data Bulanan -->
    <script>
        Chart.defaults.global.defaultFontFamily = 'Nunito';
        Chart.defaults.global.defaultFontColor = '#858796';

        var ctx = document.getElementById("myAreaChart");
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
               labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: "Rata-rata ESG Harian",
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: <?= json_encode($scores) ?>,
                }],
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                   yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            max: 100,   // supaya skala mentok 100
                            stepSize: 20,
                            callback: function(value) { return value; }
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }],
                },
                legend: { display: false }
            }
        });
    </script>

    <!-- Chart Pilar ESG -->
    <script>
        var ctx = document.getElementById("myPieChart");
        var myPieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ["Environmental", "Sosial", "Governance", "Keuangan Syariah"],
                datasets: [{
                    data: [<?php echo $env; ?>, <?php echo $sos; ?>, <?php echo $gov; ?>, <?php echo $syariah; ?>],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: { display: true },
                cutoutPercentage: 70,
            },
        });
    </script>

</body>
</html>

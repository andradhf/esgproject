<?php
include "../config/config.php";
session_start();

// Cek login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

// Ambil id user
$umkm_id = $_SESSION['umkm_id'];

// Inisialisasi variabel untuk form
$data_existing = null;
$is_update = false;

// Cek apakah user sudah pernah input data
$stmt = $conn->prepare("SELECT id, listrik, air, limbah, bahan_baku, hifzmal, hifzmal2, hifznafs, hifzdin, created_at FROM environmental WHERE umkm_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("i", $umkm_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data_existing = $result->fetch_assoc();
    $is_update = true;
}
$stmt->close();

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validasi dan sanitasi input
    $listrik = isset($_POST['listrik']) && $_POST['listrik'] !== '' ? max(0, floatval($_POST['listrik'])) : 0;
    $air = isset($_POST['air']) && $_POST['air'] !== '' ? max(0, floatval($_POST['air'])) : 0;
    $limbah = isset($_POST['limbah']) && $_POST['limbah'] !== '' ? max(0, floatval($_POST['limbah'])) : 0;
    $bahan = isset($_POST['bahan_baku']) && $_POST['bahan_baku'] !== '' ? max(0, floatval($_POST['bahan_baku'])) : 0;
    
    // Checkbox values
    $hifzmal = isset($_POST['hifzmal']) ? 1 : 0;
    $hifzmal2 = isset($_POST['hifzmal2']) ? 1 : 0;
    $hifznafs = isset($_POST['hifznafs']) ? 1 : 0;
    $hifzdin = isset($_POST['hifzdin']) ? 1 : 0;

    // Validasi: pastikan minimal ada satu input yang diisi
    if ($listrik === 0 && $air === 0 && $limbah === 0 && $bahan === 0) {
        $_SESSION['error'] = "Minimal satu field harus diisi dengan nilai lebih dari 0";
        header("Location: environmental.php");
        exit;
    }

    // Mulai transaction untuk keamanan data
    $conn->begin_transaction();

    try {
        if ($is_update) {
            // UPDATE data yang sudah ada, update created_at ke waktu sekarang
            $stmt = $conn->prepare("UPDATE environmental SET listrik = ?, air = ?, limbah = ?, bahan_baku = ?, hifzmal = ?, hifzmal2 = ?, hifznafs = ?, hifzdin = ?, created_at = NOW() WHERE umkm_id = ?");
            $stmt->bind_param("ddddiiiii", $listrik, $air, $limbah, $bahan, $hifzmal, $hifzmal2, $hifznafs, $hifzdin, $umkm_id);
            $stmt->execute();
            
            if ($stmt->affected_rows >= 0) {
                $_SESSION['success'] = "Data berhasil diperbarui!";
            } else {
                throw new Exception("Gagal memperbarui data");
            }
        } else {
            // INSERT data baru
            $stmt = $conn->prepare("INSERT INTO environmental (umkm_id, listrik, air, limbah, bahan_baku, hifzmal, hifzmal2, hifznafs, hifzdin, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("idddiiiii", $umkm_id, $listrik, $air, $limbah, $bahan, $hifzmal, $hifzmal2, $hifznafs, $hifzdin);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                $_SESSION['success'] = "Data berhasil disimpan!";
            } else {
                throw new Exception("Gagal menyimpan data");
            }
        }
        
        $stmt->close();
        
        // Commit transaction
        $conn->commit();
        
        // Redirect ke hasil
        header("Location: hasil_environmental.php");
        exit;
        
    } catch (Exception $e) {
        // Rollback jika terjadi error
        $conn->rollback();
        $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
        header("Location: environmental.php");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Monitoring ESG - ENVIRONMENTAL</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="../css/sb-admin-2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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
            font-size: 16px;
            margin-top: 20px;
        }
        .submit-btn:hover {
            background-color: darkcyan;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 14px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .form-row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .form-row label:first-child {
            width: 250px;
        }
        .form-row input[type="text"],
        .form-row input[type="number"] {
            margin-right: 20px;
            padding: 8px;
            border: 1px solid #d1d3e2;
            border-radius: 5px;
            width: 200px;
        }
        .form-row input[type="text"]:focus,
        .form-row input[type="number"]:focus {
            outline: none;
            border-color: #26a69a;
        }
        .section-title {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .update-badge {
            display: inline-block;
            background-color: #17a2b8;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            margin-left: 10px;
        }
        .info-box {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-left: 4px solid #17a2b8;
            margin-bottom: 15px;
            font-size: 13px;
            color: #666;
        }
        .info-box strong {
            color: #333;
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
             <div class="sidebar-brand d-flex flex-column align-items-center text-center px-2">
             <div class="sidebar-tagline text-wrap text-break w-100 fs-6 fs-md-5 fs-lg-4" 
                style="font-size:12px; color:#dbeffa; margin-top:4px;">
            "Membangun UMKM Berkelanjutan dengan Prinsip ESG Syariah"
            </div>
            <div class="sidebar-brand-icon my-2">
                <img src="../img/logo_kemendikbud.png" alt="Kemendikbud" class="img-fluid" style="max-width:120px;">
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

                <div class="container-fluid">
                    <h1 class="h3 mb-1 text-gray-800">
                        ENVIRONMENTAL
                        <?php if ($is_update): ?>
                            <span class="update-badge">Mode Update</span>
                        <?php endif; ?>
                    </h1>
                    
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            ✅ <?= htmlspecialchars($_SESSION['success']) ?>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-error">
                            ❌ <?= htmlspecialchars($_SESSION['error']) ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    
                    <?php if ($is_update): ?>
                        <div class="alert alert-info">
                            ℹ️ Anda sudah memiliki data. Klik submit akan memperbarui data yang ada.
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($is_update && $data_existing): ?>
                        <div class="info-box">
                            <strong>Data terakhir diinput:</strong> <?= date('d M Y H:i', strtotime($data_existing['created_at'])) ?> WIB
                        </div>
                    <?php endif; ?>
                    
                    <form action="environmental.php" method="post">
                        <p class="section-title">A. ENERGI</p>
                        <div class="form-row">
                            <label for="listrik">1. Konsumsi Listrik Perbulan (kWh)</label>
                            <input type="number" 
                                   id="listrik" 
                                   name="listrik" 
                                   step="0.01" 
                                   min="0"
                                   value="<?= $data_existing ? htmlspecialchars($data_existing['listrik']) : '' ?>" 
                                   placeholder="0">
                            <label>
                                <input type="checkbox" 
                                       name="hifzmal"
                                       <?= ($data_existing && $data_existing['hifzmal']) ? 'checked' : '' ?>> 
                                Hifz al-Mal (Energi)
                            </label>
                        </div>

                        <p class="section-title">B. AIR</p>
                        <div class="form-row">
                            <label for="air">1. Konsumsi Air Perbulan (m3)</label>
                            <input type="number" 
                                   id="air" 
                                   name="air" 
                                   step="0.01" 
                                   min="0"
                                   value="<?= $data_existing ? htmlspecialchars($data_existing['air']) : '' ?>" 
                                   placeholder="0">
                            <label>
                                <input type="checkbox" 
                                       name="hifzmal2"
                                       <?= ($data_existing && $data_existing['hifzmal2']) ? 'checked' : '' ?>> 
                                Hifz al-Mal (Air)
                            </label>
                        </div>

                        <p class="section-title">C. LIMBAH</p>
                        <div class="form-row">
                            <label for="limbah">1. Volume Limbah Padat/Cair per bulan</label>
                            <input type="number" 
                                   id="limbah" 
                                   name="limbah" 
                                   step="0.01" 
                                   min="0"
                                   value="<?= $data_existing ? htmlspecialchars($data_existing['limbah']) : '' ?>" 
                                   placeholder="0">
                            <label>
                                <input type="checkbox" 
                                       name="hifznafs"
                                       <?= ($data_existing && $data_existing['hifznafs']) ? 'checked' : '' ?>> 
                                Hifz al-Nafs (Limbah)
                            </label>
                        </div>

                        <p class="section-title">D. BAHAN BAKU</p>
                        <div class="form-row">
                            <label for="bahan">1. Persentase Bahan Ramah Lingkungan (%)</label>
                            <input type="number" 
                                   id="bahan" 
                                   name="bahan_baku" 
                                   step="0.01" 
                                   min="0" 
                                   max="100"
                                   value="<?= $data_existing ? htmlspecialchars($data_existing['bahan_baku']) : '' ?>" 
                                   placeholder="0">
                            <label>
                                <input type="checkbox" 
                                       name="hifzdin"
                                       <?= ($data_existing && $data_existing['hifzdin']) ? 'checked' : '' ?>> 
                                Hifz al-Din (Bahan)
                            </label>
                        </div>

                        <button type="submit" class="submit-btn">
                            <?= $is_update ? 'Submit' : '💾 Submit Data' ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Core Scripts -->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../js/sb-admin-2.min.js"></script>
</body>
</html>
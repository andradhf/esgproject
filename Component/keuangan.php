<?php
session_start();
include "../config/config.php";

// Cek login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$umkm_id  = $_SESSION['umkm_id'];
$username = $_SESSION['username'];

// Inisialisasi variabel untuk form
$data_existing = null;
$is_update = false;

// Cek apakah user sudah pernah input data
$stmt = $conn->prepare("SELECT * FROM data_keuangan WHERE umkm_id = ? ORDER BY created_at DESC LIMIT 1");
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
    if (isset($_POST['pendapatan'])) {
    if ($_POST['pendapatan'] === "Halal") {
        $pendapatan = 1.00;
    } else {
        $pendapatan = 0.00;
    }
} else {
    $pendapatan = null; // kalau tidak dipilih
}
    $jumlah_ziswaf = $_POST['jumlah_ziswaf'] ?? 0;
    $akad = $_POST['akad'] ?? '';

    $maqasid_pendapatan = isset($_POST['maqasid_pendapatan']) ? implode(", ", $_POST['maqasid_pendapatan']) : '';
    $maqasid_ziswaf = isset($_POST['maqasid_ziswaf']) ? implode(", ", $_POST['maqasid_ziswaf']) : '';
    $maqasid_pembiayaan = isset($_POST['maqasid_pembiayaan']) ? implode(", ", $_POST['maqasid_pembiayaan']) : '';

    // Validasi minimal satu field terisi
    if (empty($pendapatan) && $jumlah_ziswaf == 0 && empty($akad)) {
        $_SESSION['error'] = "Minimal satu field harus diisi";
        header("Location: keuangan.php");
        exit;
    }

    // Mulai transaction
    $conn->begin_transaction();

    try {
        if ($is_update) {
            // UPDATE data yang sudah ada
            $sql = "UPDATE data_keuangan SET 
                    pendapatan_halal = ?, 
                    ziswaf = ?, 
                    pembiayaan = ?, 
                    maqasid_pendapatan = ?, 
                    maqasid_ziswaf = ?, 
                    maqasid_pembiayaan = ?,
                    created_at = NOW()
                    WHERE umkm_id = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "ddssssi",
                $pendapatan,
                $jumlah_ziswaf,
                $akad,
                $maqasid_pendapatan,
                $maqasid_ziswaf,
                $maqasid_pembiayaan,
                $umkm_id
            );

            if ($stmt->execute()) {
                $_SESSION['success'] = "Data berhasil diperbarui!";
            } else {
                throw new Exception("Gagal memperbarui data");
            }
        } else {
            // INSERT data baru
            $sql = "INSERT INTO data_keuangan 
                    (umkm_id, pendapatan_halal, ziswaf, pembiayaan, maqasid_pendapatan, maqasid_ziswaf, maqasid_pembiayaan, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "iddssss",
                $umkm_id,
                $pendapatan,
                $jumlah_ziswaf,
                $akad,
                $maqasid_pendapatan,
                $maqasid_ziswaf,
                $maqasid_pembiayaan
            );

            if ($stmt->execute()) {
                $_SESSION['success'] = "Data berhasil disimpan!";
            } else {
                throw new Exception("Gagal menyimpan data");
            }
        }

        $stmt->close();
        $conn->commit();

        header("Location: ../Component/lihatkeuangan.php");
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
        header("Location: keuangan.php");
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
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Monitoring ESG - Keuangan Syariah</title>

    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
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
        input[type="number"]:focus, input[type="text"]:focus {
            outline: none;
            border-color: #26a69a;
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
            font-size: 16px;
        }
        .submit-btn:hover {
            background: #005f56;
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
<div id="wrapper">
    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="../index.php">
            <div class="sidebar-brand-icon">
                <img src="../img/logo_uhamka.png" alt="UHAMKA" style="width:70px; height:auto;">
            </div>
            <div class="sidebar-brand-text mx-3">ESG Syariah UMKM</div>
        </a>
        <hr class="sidebar-divider my-0">
        <li class="nav-item active">
            <a class="nav-link" href="../index.php">
                <i class="fas fa-fw fa-th-large"></i>
                <span>Dashboard</span></a>
        </li>
        <hr class="sidebar-divider">
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
        <li class="nav-item">
            <a class="nav-link" href="maqasid_syariah.php">
                <i class="fas fa-balance-scale"></i>
                <span>Maqasid Syariah</span>
            </a>
        </li>
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
                        <a class="collapse-item" href="profile.php">Akun</a>
                        <a class="collapse-item" href="logout.php">Logout</a>
                    <?php else: ?>
                        <a class="collapse-item" href="/login.php">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </li>
        <hr class="sidebar-divider d-none d-md-block">
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>
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
                                <?php echo htmlspecialchars($username); ?>
                            </span>
                            <i class="fa-solid fa-user"></i>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="container-fluid">
                <h1 class="h3 mb-1 text-gray-800">
                    KEUANGAN SYARIAH
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

                <!-- FORM -->
                <form action="" method="post">
                    <!-- A. PENDAPATAN HALAL -->
                    <div class="section">
                        <h2>A. Pendapatan Halal</h2>
                        <label>Rasio pendapatan halal/non halal</label>
                        <div class="row">
                           <label><input type="radio" 
                            name="pendapatan" 
                            value="Halal"
                            <?= ($data_existing && $data_existing['pendapatan_halal'] == 1.00) ? 'checked' : '' ?>> Halal</label>
                            <label><input type="radio" 
                            name="pendapatan" 
                            value="Non Halal"
                            <?= ($data_existing && $data_existing['pendapatan_halal'] == 0.00) ? 'checked' : '' ?>> Non Halal</label>
                        </div>
                        <div class="checkbox-group">
                            <label><input type="checkbox" 
                                          name="maqasid_pendapatan[]" 
                                          value="Hifz al-Din"
                                          <?= ($data_existing && strpos($data_existing['maqasid_pendapatan'], 'Hifz al-Din') !== false) ? 'checked' : '' ?>> Hifz al-Din</label>
                            <label><input type="checkbox" 
                                          name="maqasid_pendapatan[]" 
                                          value="Hifz al-Mal"
                                          <?= ($data_existing && strpos($data_existing['maqasid_pendapatan'], 'Hifz al-Mal') !== false) ? 'checked' : '' ?>> Hifz al-Mal</label>
                        </div>
                    </div>

                    <!-- B. DISTRIBUSI ZISWAF -->
                    <div class="section">
                        <h2>B. Distribusi ZISWAF</h2>
                        <label>Jumlah Zakat/Infak/Sedekah/Wakaf</label>
                        <input type="number" 
                               name="jumlah_ziswaf" 
                               style="width:250px;"
                               min="0"
                               value="<?= $data_existing ? htmlspecialchars($data_existing['ziswaf']) : '' ?>">
                        <div class="checkbox-group">
                            <label><input type="checkbox" 
                                          name="maqasid_ziswaf[]" 
                                          value="Hifz al-Din"
                                          <?= ($data_existing && strpos($data_existing['maqasid_ziswaf'], 'Hifz al-Din') !== false) ? 'checked' : '' ?>> Hifz al-Din</label>
                            <label><input type="checkbox" 
                                          name="maqasid_ziswaf[]" 
                                          value="Hifz al-Mal"
                                          <?= ($data_existing && strpos($data_existing['maqasid_ziswaf'], 'Hifz al-Mal') !== false) ? 'checked' : '' ?>> Hifz al-Mal</label>
                        </div>
                    </div>

                    <!-- C. PEMBIAYAAN -->
                    <div class="section">
                        <h2>C. Pembiayaan</h2>
                        <label>Jenis Akad Syariah</label>
                        <p>Pilih salah satu:</p>
                        <div class="row">
                            <label><input type="radio" 
                                          name="akad" 
                                          value="Murabahah"
                                          <?= ($data_existing && $data_existing['pembiayaan'] == 'Murabahah') ? 'checked' : '' ?>> Murabahah</label>
                        </div>
                        <div class="row">
                            <label><input type="radio" 
                                          name="akad" 
                                          value="Mudharabah"
                                          <?= ($data_existing && $data_existing['pembiayaan'] == 'Mudharabah') ? 'checked' : '' ?>> Mudharabah</label>
                        </div>
                        <div class="row">
                            <label><input type="radio" 
                                          name="akad" 
                                          value="Ijarah"
                                          <?= ($data_existing && $data_existing['pembiayaan'] == 'Ijarah') ? 'checked' : '' ?>> Ijarah</label>
                        </div>
                        <div class="checkbox-group">
                            <label><input type="checkbox" 
                                          name="maqasid_pembiayaan[]" 
                                          value="Hifz al-Mal"
                                          <?= ($data_existing && strpos($data_existing['maqasid_pembiayaan'], 'Hifz al-Mal') !== false) ? 'checked' : '' ?>> Hifz al-Mal</label>
                        </div>
                    </div>

                    <!-- Tombol submit -->
                    <button type="submit" class="submit-btn">
                        <?= $is_update ? '🔄 Update Data' : '💾 Submit Data' ?>
                    </button>
                </form>
            </div>
        </div>
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; ESG UMKM 2025</span>
                </div>
            </div>
        </footer>
    </div>
</div>
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>
</body>
</html>
<?php
include '../config/config.php';
session_start();

// Cek login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

// Ambil id user
$umkm_id = $_SESSION['umkm_id'];

// Inisialisasi variabel untuk form
// Inisialisasi variabel untuk form
$data_existing = null;
$is_update = false;

// Cek apakah user sudah pernah input data
$stmt = $conn->prepare("SELECT * FROM data_sosial WHERE umkm_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("i", $umkm_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data_existing = $result->fetch_assoc();
    $is_update = true;
}
$stmt->close();

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jumlah_karyawan = intval($_POST['jumlah_karyawan'] ?? 0);
    $jumlah_karyawan_perempuan = intval($_POST['jumlah_karyawan_perempuan'] ?? 0);
    $jumlah_insiden = intval($_POST['jumlah_insiden'] ?? 0);
    $kejadian = $_POST['kejadian'] ?? '';
    $jam_pelatihan = intval($_POST['jam_pelatihan'] ?? 0);
    $produk_halal = $_POST['produk_halal'] ?? '';
    $csr = $_POST['csr'] ?? '';
    $ziswaf = intval($_POST['ziswaf'] ?? 0);

    // ✅ Maqasid Tenaga
    $maqasid_tenaga_nasl = (isset($_POST['maqasid_tenaga']) && in_array('Hifz al-Nasl', $_POST['maqasid_tenaga'])) ? 1 : 0;
    $maqasid_tenaga_nafs = (isset($_POST['maqasid_tenaga']) && in_array('Hifz al-Nafs', $_POST['maqasid_tenaga'])) ? 1 : 0;

    // ✅ Maqasid Produk
    $maqasid_produk_din  = (isset($_POST['maqasid_produk']) && in_array('Hifz al-Din', $_POST['maqasid_produk'])) ? 1 : 0;
    $maqasid_produk_nafs = (isset($_POST['maqasid_produk']) && in_array('Hifz al-Nafs', $_POST['maqasid_produk'])) ? 1 : 0;

    // ✅ Maqasid Sosial
    $maqasid_sosial_mal  = (isset($_POST['maqasid_sosial']) && in_array('Hifz al-Mal', $_POST['maqasid_sosial'])) ? 1 : 0;
    $maqasid_sosial_nasl = (isset($_POST['maqasid_sosial']) && in_array('Hifz al-Nasl', $_POST['maqasid_sosial'])) ? 1 : 0;

    // ✅ Maqasid lainnya (jika masih dalam bentuk string)
    $maqasid_k3  = isset($_POST['maqasid_k3']) ? implode(", ", $_POST['maqasid_k3']) : '';
    $maqasid_sdm = isset($_POST['maqasid_sdm']) ? implode(", ", $_POST['maqasid_sdm']) : '';

    // Validasi minimal satu field
    if ($jumlah_karyawan == 0 && $jumlah_karyawan_perempuan == 0 && $jumlah_insiden == 0 &&
        $jam_pelatihan == 0 && empty($kejadian) && empty($produk_halal) && empty($csr) && $ziswaf == 0 &&
        $maqasid_tenaga_nasl == 0 && $maqasid_tenaga_nafs == 0 &&
        $maqasid_produk_din == 0 && $maqasid_produk_nafs == 0 &&
        $maqasid_sosial_mal == 0 && $maqasid_sosial_nasl == 0) {
        $_SESSION['error'] = "Minimal satu field harus diisi";
        header("Location: sosial.php");
        exit;
    }

    $conn->begin_transaction();

    try {
        if ($is_update) {
            // ✅ UPDATE data
            $sql = "UPDATE data_sosial SET 
                        karyawan = ?, 
                        karyawan_perempuan = ?, 
                        insiden_k3 = ?, 
                        kejadian = ?, 
                        pelatihan_sdm = ?, 
                        produk_halal = ?, 
                        csr = ?, 
                        ziswaf = ?, 
                        maqasid_tenaga_nasl = ?, 
                        maqasid_tenaga_nafs = ?, 
                        maqasid_produk_din = ?, 
                        maqasid_produk_nafs = ?, 
                        maqasid_sosial_mal = ?, 
                        maqasid_sosial_nasl = ?, 
                        maqasid_k3 = ?, 
                        maqasid_sdm = ?, 
                        created_at = NOW()
                    WHERE umkm_id = ?";

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed (UPDATE): " . $conn->error);
            }

            // 17 variabel total
            $types = "iiisissiiiiiiissi";
            $stmt->bind_param(
                $types,
                $jumlah_karyawan,
                $jumlah_karyawan_perempuan,
                $jumlah_insiden,
                $kejadian,
                $jam_pelatihan,
                $produk_halal,
                $csr,
                $ziswaf,
                $maqasid_tenaga_nasl,
                $maqasid_tenaga_nafs,
                $maqasid_produk_din,
                $maqasid_produk_nafs,
                $maqasid_sosial_mal,
                $maqasid_sosial_nasl,
                $maqasid_k3,
                $maqasid_sdm,
                $umkm_id
            );

            if (!$stmt->execute()) {
                throw new Exception("Gagal memperbarui data: " . $stmt->error);
            }
            $_SESSION['success'] = "Data berhasil diperbarui!";
        } else {
            // ✅ INSERT data baru
            $sql = "INSERT INTO data_sosial 
                    (umkm_id, karyawan, karyawan_perempuan, insiden_k3, kejadian, pelatihan_sdm, 
                     produk_halal, csr, ziswaf, maqasid_tenaga_nasl, maqasid_tenaga_nafs,
                     maqasid_produk_din, maqasid_produk_nafs, maqasid_sosial_mal, maqasid_sosial_nasl,
                     maqasid_k3, maqasid_sdm, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed (INSERT): " . $conn->error);
            }

            // 17 parameter total
            $types = "iiiisissiiiiiiiss";
            $stmt->bind_param(
                $types,
                $umkm_id,
                $jumlah_karyawan,
                $jumlah_karyawan_perempuan,
                $jumlah_insiden,
                $kejadian,
                $jam_pelatihan,
                $produk_halal,
                $csr,
                $ziswaf,
                $maqasid_tenaga_nasl,
                $maqasid_tenaga_nafs,
                $maqasid_produk_din,
                $maqasid_produk_nafs,
                $maqasid_sosial_mal,
                $maqasid_sosial_nasl,
                $maqasid_k3,
                $maqasid_sdm
            );

            if (!$stmt->execute()) {
                throw new Exception("Gagal menyimpan data: " . $stmt->error);
            }
            $_SESSION['success'] = "Data berhasil disimpan!";
        }

        $stmt->close();
        $conn->commit();
        header("Location: lihatsosial.php");
        exit;

    } catch (Exception $e) {
        if ($conn->in_transaction) {
            $conn->rollback();
        }
        $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
        header("Location: sosial.php");
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
  <title>Monitoring ESG - SOSIAL</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <link href="../css/sb-admin-2.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <style>
    /* === FIX GAP DEKAT SIDEBAR & TOPBAR === */
/* === FIX GAP DEKAT SIDEBAR === */
body {
    margin: 0 !important;
    padding: 0 !important;
    font-family: Arial, sans-serif;
    background: #f9f9f9;
}

#wrapper {
    margin: 0 !important;
    padding: 0 !important;
}

#content-wrapper {
    margin: 0 !important;
    padding: 0 !important;
}

#content {
    margin: 0 !important;
    padding: 0 !important;
}

/* Hanya konten utama yang rapat, tidak mengubah navbar */
.container-fluid {
    margin: 0 !important;
    padding: 15px !important;
}

/* === SIDEBAR COLOR === */
.bg-gradient-primary {
    background: linear-gradient(180deg, #1565c0 0%, #26a69a 100%) !important;
}

/* === FORM & SECTION === */
h2 {
    font-size: 18px;
    margin-top: 0px;
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

input[type="text"],
input[type="number"] {
    width: 200px;
    padding: 6px;
    margin-bottom: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

input[type="text"]:focus,
input[type="number"]:focus {
    outline: none;
    border-color: #26a69a;
}

.checkbox-group {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 8px;
}

/* === BUTTON === */
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

/* === ALERTS === */
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

/* === BADGE UPDATE MODE === */
.update-badge {
    display: inline-block;
    background-color: #17a2b8;
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 12px;
    margin-left: 10px;
}

/* === INFO BOX === */
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
          SOSIAL
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

        <!-- FORM INPUT -->
        <form method="POST" action="">
          <div class="section">
            <h2>1. Tenaga Kerja</h2>
            <label>Jumlah Karyawan (orang):</label>
            <input type="number" 
                   name="jumlah_karyawan" 
                   min="0"
                   value="<?= $data_existing ? htmlspecialchars($data_existing['karyawan']) : '' ?>">
            <label>Jumlah Karyawan Perempuan (orang):</label>
            <input type="number" 
                   name="jumlah_karyawan_perempuan" 
                   min="0"
                   value="<?= $data_existing ? htmlspecialchars($data_existing['karyawan_perempuan']) : '' ?>">
            <div class="checkbox-group">
            <label>
                <input type="checkbox" 
                    name="maqasid_tenaga[]" 
                    value="Hifz al-Nasl"
                    <?= ($data_existing && $data_existing['maqasid_tenaga_nasl'] == 1) ? 'checked' : '' ?>>
                Hifz al-Nasl
            </label>

            <label>
                <input type="checkbox" 
                    name="maqasid_tenaga[]" 
                    value="Hifz al-Nafs"
                    <?= ($data_existing && $data_existing['maqasid_tenaga_nafs'] == 1) ? 'checked' : '' ?>>
                Hifz al-Nafs
            </label>
            </div>

          </div>

          <div class="section">
            <h2>2. Kesehatan & Keselamatan Kerja</h2>
            <label>Jumlah Insiden K3 (bulan):</label>
            <input type="number" 
                   name="jumlah_insiden" 
                   min="0"
                   value="<?= $data_existing ? htmlspecialchars($data_existing['insiden_k3']) : '' ?>">
            <label>Kejadian:</label>
            <input type="text" 
                   name="kejadian"
                   value="<?= $data_existing ? htmlspecialchars($data_existing['kejadian']) : '' ?>">
            <div class="checkbox-group">
              <label><input type="checkbox" 
                            name="maqasid_k3[]" 
                            value="Hifz al-Aql"
                            <?= ($data_existing && strpos($data_existing['maqasid_k3'], 'Hifz al-Aql') !== false) ? 'checked' : '' ?>> Hifz al-Aql</label>
            </div>
          </div>

          <div class="section">
            <h2>3. Pengembangan SDM</h2>
            <label>Jam Pelatihan Karyawan (Jam/Bln):</label>
            <input type="number" 
                   name="jam_pelatihan" 
                   min="0"
                   value="<?= $data_existing ? htmlspecialchars($data_existing['pelatihan_sdm']) : '' ?>">
            <div class="checkbox-group">
              <label><input type="checkbox" 
                            name="maqasid_sdm[]" 
                            value="Hifz al-Aql"
                            <?= ($data_existing && strpos($data_existing['maqasid_sdm'], 'Hifz al-Aql') !== false) ? 'checked' : '' ?>> Hifz al-Aql</label>
            </div>
          </div>

          <div class="section">
            <h2>4. Produk & Layanan</h2>
            <label>Produk Halal & Aman:</label>
            <div>
              <label><input type="radio" 
                            name="produk_halal" 
                            value="Ya"
                            <?= ($data_existing && $data_existing['produk_halal'] == 'Ya') ? 'checked' : '' ?>> Ya</label>
              <label><input type="radio" 
                            name="produk_halal" 
                            value="Tidak"
                            <?= ($data_existing && $data_existing['produk_halal'] == 'Tidak') ? 'checked' : '' ?>> Tidak</label>
            </div>
            <div class="checkbox-group"> 
            <label>
                <input type="checkbox" 
                    name="maqasid_produk[]" 
                    value="Hifz al-Din"
                    <?= ($data_existing && $data_existing['maqasid_produk_din'] == 1) ? 'checked' : '' ?>>
                Hifz al-Din
            </label>

            <label>
                <input type="checkbox" 
                    name="maqasid_produk[]" 
                    value="Hifz al-Nafs"
                    <?= ($data_existing && $data_existing['maqasid_produk_nafs'] == 1) ? 'checked' : '' ?>>
                Hifz al-Nafs
            </label>
            </div>

          </div>

          <div class="section">
            <h2>5. Kontribusi Sosial</h2>
            <label>CSR:</label>
            <div>
              <label><input type="radio" 
                            name="csr" 
                            value="Ya"
                            <?= ($data_existing && $data_existing['csr'] == 'Ya') ? 'checked' : '' ?>> Ya</label>
              <label><input type="radio" 
                            name="csr" 
                            value="Tidak"
                            <?= ($data_existing && $data_existing['csr'] == 'Tidak') ? 'checked' : '' ?>> Tidak</label>
            </div>
            <label>ZISWAF (Rp):</label>
            <input type="number" 
                   name="ziswaf" 
                   min="0"
                   value="<?= $data_existing ? htmlspecialchars($data_existing['ziswaf']) : '' ?>">
            <div class="checkbox-group">
  <label>
    <input type="checkbox" 
           name="maqasid_sosial[]" 
           value="Hifz al-Mal"
           <?= ($data_existing && $data_existing['maqasid_sosial_mal'] == 1) ? 'checked' : '' ?>>
                Hifz al-Mal
            </label>

            <label>
                <input type="checkbox" 
                    name="maqasid_sosial[]" 
                    value="Hifz al-Nasl"
                    <?= ($data_existing && $data_existing['maqasid_sosial_nasl'] == 1) ? 'checked' : '' ?>>
                Hifz al-Nasl
            </label>
            </div>

          </div>

          <button type="submit" class="submit-btn">
              <?= $is_update ? '🔄 Update Data' : '💾 Submit Data' ?>
          </button>
        </form>

      </div>
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; ESG UMKM SYARIAH 2025</span>
          </div>
        </div>
      </footer>
    </div>
  </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>
</body>
</html>
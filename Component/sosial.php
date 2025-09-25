<?php
include '../config/config.php'; // koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $umkm_id = 1; // sementara fixed (nanti bisa ambil dari session user login)

    $jumlah_karyawan = $_POST['jumlah_karyawan'] ?? 0;
    $jumlah_karyawan_perempuan = $_POST['jumlah_karyawan_perempuan'] ?? 0;
    $jumlah_insiden = $_POST['jumlah_insiden'] ?? 0;
    $kejadian = $_POST['kejadian'] ?? '';
    $jam_pelatihan = $_POST['jam_pelatihan'] ?? 0;
    $produk_halal = $_POST['produk_halal'] ?? '';
    $csr = $_POST['csr'] ?? '';
    $ziswaf = $_POST['ziswaf'] ?? 0;

    $maqasid_tenaga = isset($_POST['maqasid_tenaga']) ? implode(", ", $_POST['maqasid_tenaga']) : '';
    $maqasid_k3 = isset($_POST['maqasid_k3']) ? implode(", ", $_POST['maqasid_k3']) : '';
    $maqasid_sdm = isset($_POST['maqasid_sdm']) ? implode(", ", $_POST['maqasid_sdm']) : '';
    $maqasid_produk = isset($_POST['maqasid_produk']) ? implode(", ", $_POST['maqasid_produk']) : '';
    $maqasid_sosial = isset($_POST['maqasid_sosial']) ? implode(", ", $_POST['maqasid_sosial']) : '';

    $sql = "INSERT INTO data_sosial 
        (umkm_id, karyawan, karyawan_perempuan, insiden_k3, kejadian, pelatihan_sdm, produk_halal, csr, ziswaf, 
         maqasid_tenaga, maqasid_k3, maqasid_sdm, maqasid_produk, maqasid_sosial) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "iiiisississsss",
        $umkm_id,
        $jumlah_karyawan,
        $jumlah_karyawan_perempuan,
        $jumlah_insiden,
        $kejadian,
        $jam_pelatihan,
        $produk_halal,
        $csr,
        $ziswaf,
        $maqasid_tenaga,
        $maqasid_k3,
        $maqasid_sdm,
        $maqasid_produk,
        $maqasid_sosial
    );

    if ($stmt->execute()) {
        echo "<script>alert('Data sosial berhasil disimpan!'); window.location.href='lihatsosial.php';</script>";
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
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
  <title>SB Admin 2 - Border Utilities</title>
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <link href="../css/sb-admin-2.min.css" rel="stylesheet">
  <style>
    .bg-gradient-primary {
        background: linear-gradient(180deg, #1565c0 0%, #26a69a 100%) !important;
    }
    body { font-family: Arial, sans-serif; margin: 20px; background: #f9f9f9; }
    h2 { font-size: 18px; margin-top: 20px; color: #333; }
    .section { background: #fff; padding: 15px; margin-bottom: 15px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    label { display: block; margin: 6px 0 3px; }
    input[type="text"], input[type="number"] { width: 200px; padding: 6px; margin-bottom: 8px; border: 1px solid #ccc; border-radius: 4px; }
    .checkbox-group { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 8px; }
    .submit-btn { background: #00796b; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; margin-top: 20px; }
    .submit-btn:hover { background: #005f56; }
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
    <li class="nav-item"><a class="nav-link" href="../index.php"><i class="fas fa-fw fa-th-large"></i><span>Dashboard</span></a></li>
    <hr class="sidebar-divider">
    <li class="nav-item active">
      <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
        <i class="fas fa-fw fa-leaf"></i><span>Pilar ESG</span>
      </a>
      <div id="collapseUtilities" class="collapse show" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">Pilar ESG:</h6>
          <a class="collapse-item" href="environmental.php">🌿 Enviromental</a>
          <a class="collapse-item active" href="sosial.php">👥 Sosial</a>
          <a class="collapse-item" href="governance.php"> ⚖️ Governance</a>
          <a class="collapse-item" href="keuangan.php">💰 Keuangan Syariah</a>
        </div>
      </div>
    </li>
    <li class="nav-item"><a class="nav-link" href="maqasid_syariah.php"><i class="fas fa-balance-scale"></i><span>Maqasid Syariah</span></a></li>
    <hr class="sidebar-divider d-none d-md-block">
  </ul>
  <!-- End of Sidebar -->

  <!-- Content Wrapper -->
  <div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
      <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow"></nav>
      <div class="container-fluid">
        <h1 class="h3 mb-1 text-gray-800">SOSIAL</h1>

        <!-- FORM INPUT -->
        <form method="POST" action="">
          <div class="section">
            <h2>1. Tenaga Kerja</h2>
            <label>Jumlah Karyawan (orang):</label>
            <input type="number" name="jumlah_karyawan">
            <label>Jumlah Karyawan Perempuan (orang):</label>
            <input type="number" name="jumlah_karyawan_perempuan">
            <div class="checkbox-group">
              <label><input type="checkbox" name="maqasid_tenaga[]" value="Hifz al-Nasl"> Hifz al-Nasl</label>
              <label><input type="checkbox" name="maqasid_tenaga[]" value="Hifz al-Nafs"> Hifz al-Nafs</label>
            </div>
          </div>

          <div class="section">
            <h2>2. Kesehatan & Keselamatan Kerja</h2>
            <label>Jumlah Insiden K3 (bulan):</label>
            <input type="number" name="jumlah_insiden">
            <label>Kejadian:</label>
            <input type="text" name="kejadian">
            <div class="checkbox-group">
              <label><input type="checkbox" name="maqasid_k3[]" value="Hifz al-Aql"> Hifz al-Aql</label>
            </div>
          </div>

          <div class="section">
            <h2>3. Pengembangan SDM</h2>
            <label>Jam Pelatihan Karyawan (Jam/Bln):</label>
            <input type="number" name="jam_pelatihan">
            <div class="checkbox-group">
              <label><input type="checkbox" name="maqasid_sdm[]" value="Hifz al-Aql"> Hifz al-Aql</label>
            </div>
          </div>

          <div class="section">
            <h2>4. Produk & Layanan</h2>
            <label>Produk Halal & Aman:</label>
            <div>
              <label><input type="radio" name="produk_halal" value="Ya"> Ya</label>
              <label><input type="radio" name="produk_halal" value="Tidak"> Tidak</label>
            </div>
            <div class="checkbox-group">
              <label><input type="checkbox" name="maqasid_produk[]" value="Hifz al-Din"> Hifz al-Din</label>
              <label><input type="checkbox" name="maqasid_produk[]" value="Hifz al-Nafs"> Hifz al-Nafs</label>
            </div>
          </div>

          <div class="section">
            <h2>5. Kontribusi Sosial</h2>
            <label>CSR:</label>
            <div>
              <label><input type="radio" name="csr" value="Ya"> Ya</label>
              <label><input type="radio" name="csr" value="Tidak"> Tidak</label>
            </div>
            <label>ZISWAF (Rp):</label>
            <input type="number" name="ziswaf">
            <div class="checkbox-group">
              <label><input type="checkbox" name="maqasid_sosial[]" value="Hifz al-Mal"> Hifz al-Mal</label>
              <label><input type="checkbox" name="maqasid_sosial[]" value="Hifz al-Nasl"> Hifz al-Nasl</label>
            </div>
          </div>

          <button type="submit" class="submit-btn">Submit</button>
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

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
</body>
</html>

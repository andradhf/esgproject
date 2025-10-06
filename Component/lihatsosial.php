<?php
include "../config/config.php";
session_start();

// cek login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$umkm_id = $_SESSION['umkm_id'];


$sql = "SELECT * FROM data_sosial WHERE umkm_id = ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $umkm_id);
$stmt->execute();
$result = $stmt->get_result();

$sosial_skor = 0;
$kategori_sosial = "Belum ada data";
$kategori_maqasid = "Belum ada data";
$row = null;

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
$sosial_skor = 0;

// Jika insiden lebih dari 10, langsung set kategori Kurang
if ($row["insiden_k3"] > 10) {
    $kategori_sosial = "Kurang ❌";
} else {
    // Hitung skor sosial hanya jika insiden tidak melebihi 10
    if ($row["karyawan"] > 0) $sosial_skor += 25;
    if ($row["produk_halal"] == "Ya") $sosial_skor += 25;
    if ($row["pelatihan_sdm"] > 0) $sosial_skor += 25;
    if ($row["csr"] == "Ya") $sosial_skor += 25;

    // Kategorisasi berdasarkan skor
    if ($sosial_skor >= 75) {
        $kategori_sosial = "Baik ✅";
    } elseif ($sosial_skor >= 50) {
        $kategori_sosial = "Cukup ⚠️";
    } else {
        $kategori_sosial = "Kurang ❌";
    }
}

    // Hitung jumlah kolom maqasid yang terisi
$maqasid_count = 0;

// Kolom berbasis angka
$maqasid_fields_numeric = [
    "maqasid_tenaga_nasl",
    "maqasid_tenaga_nafs",
    "maqasid_produk_din",
    "maqasid_produk_nafs",
    "maqasid_sosial_mal",
    "maqasid_sosial_nasl"
];

// Cek kolom bernilai angka
foreach ($maqasid_fields_numeric as $field) {
    if (isset($row[$field]) && $row[$field] == 1) {
        $maqasid_count++;
    }
}

// Cek kolom berbasis teks (string)
if (!empty($row["maqasid_k3"])) $maqasid_count++;
if (!empty($row["maqasid_sdm"])) $maqasid_count++;

// Tentukan kategori maqasid berdasarkan jumlah kolom terisi
if ($maqasid_count >= 7) {
    $kategori_maqasid = "✅ Sesuai dengan Syariah Maqasid";
} elseif ($maqasid_count >= 4) {
    $kategori_maqasid = "⚠️ Cukup sesuai dengan Syariah Maqasid";
} else {
    $kategori_maqasid = "❌ Belum sesuai Syariah Maqasid";
}
}
// cek apakah umkm_id sudah ada di laporan_esg
$cek = $conn->prepare("SELECT id FROM laporan_esg WHERE umkm_id = ?");
$cek->bind_param("i", $umkm_id);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    // sudah ada → update
    $stmt = $conn->prepare("UPDATE laporan_esg SET sos = ? WHERE umkm_id = ?");
    $stmt->bind_param("ii", $sosial_skor, $umkm_id);
} else {
    // belum ada → insert
    $stmt = $conn->prepare("INSERT INTO laporan_esg (umkm_id, sos) VALUES (?, ?)");
    $stmt->bind_param("ii", $umkm_id, $sosial_skor);
}

$stmt->execute();
$stmt->close();
$cek->close();


?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Data Sosial</title>

  <!-- Font Awesome & SB Admin -->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../css/sb-admin-2.min.css" rel="stylesheet">

  <style>
    body { 
      font-family: Arial, sans-serif; 
      background: linear-gradient(135deg, #3fab0e, #0096c7);
      padding: 20px; 
    }
    h2, h3 { 
      text-align: center; 
      color: #1e3a8a; 
      margin-bottom: 20px; 
    }
    table {
      width: 100%; 
      margin: auto; 
      border-collapse: collapse; 
      background: #ffffff;
      box-shadow: 0px 4px 12px rgba(0,0,0,0.1); 
      border-radius: 10px; 
      overflow: hidden;
    }
    th, td { 
      padding: 10px 14px; 
      text-align: center; 
      font-size: 14px;
    }
    th { 
      background-color: #1e40af; 
      color: white; 
      text-transform: uppercase; 
    }
    tr:nth-child(even) { background-color: #f1f5f9; }
    tr:hover { background-color: #e0f2fe; transition: 0.3s; }
    .highlight { font-weight: bold; color: #047857; }
    .box { 
      background: #ecfdf5; 
      padding: 15px; 
      margin: 20px auto; 
      border-radius: 10px; 
      width: 100%; 
      max-width: 600px; 
      text-align: center; 
    }

    /* Responsif untuk layar kecil */
    @media (max-width: 768px) {
      th, td { font-size: 12px; padding: 8px; }
      h2, h3 { font-size: 18px; }
      .box { width: 95%; padding: 12px; }
    }
    @media (max-width: 480px) {
      th, td { font-size: 11px; }
      h2 { font-size: 16px; }
      h3 { font-size: 14px; }
    }
  </style>
</head>

<body>

<h2>Data Sosial UMKM</h2>
<div class="table-responsive">
<table class="table table-bordered table-striped">
  <thead>
  <tr>
    <th>Karyawan</th>
    <th>Karyawan Perempuan</th>
    <th>Insiden K3</th>
    <th>Kejadian</th>
    <th>Pelatihan SDM</th>
    <th>Produk Halal</th>
    <th>CSR</th>
    <th>ZISWAF</th>
  </tr>
  </thead>
<?php
if ($row) {
    echo "<tr>";
    echo "<td>" . $row["karyawan"] . "</td>";
    echo "<td>" . $row["karyawan_perempuan"] . "</td>";
    echo "<td>" . $row["insiden_k3"] . "</td>";
    echo "<td>" . $row["kejadian"] . "</td>";
    echo "<td>" . $row["pelatihan_sdm"] . "</td>";
    echo "<td>" . $row["produk_halal"] . "</td>";
    echo "<td>" . $row["csr"] . "</td>";
    echo "<td>Rp" . number_format($row["ziswaf"], 0, ',', '.') . "</td>";
    echo "</tr>";
} else {
    echo "<tr><td colspan='8'>Belum ada data</td></tr>";
}
?>
</table>
</div>

<div class="box">
  <h3>Penilaian Sosial</h3>
  Skor: <span class="highlight"><?= $sosial_skor ?>%</span><br>
  Kategori: <span class="highlight"><?= $kategori_sosial ?></span>
</div>

<div class="box">
  <h3>Penilaian Maqasid</h3>
  <span class="highlight"><?= $kategori_maqasid ?></span>
</div>
<div class="mb-3">
    <button onclick="window.history.back()" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </button>
    </div>
</body>
</html>
<?php $conn->close(); ?>

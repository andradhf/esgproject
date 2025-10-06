<?php
include '../config/config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['umkm_id'])) {
    die("Error: umkm_id belum diset di session.");
}

$umkm_id = $_SESSION['umkm_id'];

// ambil cuma data terakhir
$result = $conn->query("SELECT * FROM data_governance ORDER BY id DESC LIMIT 1");
$row = $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;

$score = 0;
$persen = 0;
$kategori = "Belum Ada Data";

if ($row) {
    // hitung skor
    if ($row['legalitas'] == "1") $score++;
    if ($row['kepatuhan_syariah'] == "1") $score++;
    if ($row['transparansi'] == "1") $score++;
    if ($row['integritas'] == "1") $score++;

    $total_aspek = 4;
    $persen = ($score / $total_aspek) * 100;

    if ($persen == 100) {
        $kategori = "Baik ✅";
    } elseif ($persen >= 50) {
        $kategori = "Cukup ⚠️";
    } else {
        $kategori = "Kurang ❌";
    }
}

$maqasid_legalitas     = $row['maqasid_legalitas']     ?? "Belum Dinilai";
$maqasid_syariah       = $row['maqasid_syariah']       ?? "Belum Dinilai";
$maqasid_transparansi  = $row['maqasid_transparansi']  ?? "Belum Dinilai";
$maqasid_integritas    = $row['maqasid_integritas']    ?? "Belum Dinilai";

// Hitung jumlah "Ya"
$jumlah_ya = 0;
if (stripos($maqasid_legalitas, 'ya') !== false) $jumlah_ya++;
if (stripos($maqasid_syariah, 'ya') !== false) $jumlah_ya++;
if (stripos($maqasid_transparansi, 'ya') !== false) $jumlah_ya++;
if (stripos($maqasid_integritas, 'ya') !== false) $jumlah_ya++;

// Tentukan kategori penilaian maqasid
if ($jumlah_ya >= 4) {
    $maqasid_kategori = "Sesuai ✅";
} elseif ($jumlah_ya == 3) {
    $maqasid_kategori = "Cukup Sesuai ⚠️";
} elseif ($jumlah_ya >= 1 && $jumlah_ya <= 2) {
    $maqasid_kategori = "Tidak Sesuai ❌";
} else {
    $maqasid_kategori = "Belum Dinilai";
}


// cek apakah umkm_id sudah ada di laporan_esg
$cek = $conn->prepare("SELECT id FROM laporan_esg WHERE umkm_id = ?");
$cek->bind_param("i", $umkm_id);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    // sudah ada → update
    $stmt = $conn->prepare("UPDATE laporan_esg SET gov = ? WHERE umkm_id = ?");
    $stmt->bind_param("ii", $persen, $umkm_id);
} else {
    // belum ada → insert
    $stmt = $conn->prepare("INSERT INTO laporan_esg (umkm_id, gov) VALUES (?, ?)");
    $stmt->bind_param("ii", $umkm_id, $persen);
}

$stmt->execute();
$stmt->close();
$cek->close();

$conn->close();
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Governance</title>

    <!-- Font & Template bawaan -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #3fab0e, #0096c7);
        }
        h2 {
            text-align: center;
            color: #2c3e50;
        }
        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px 15px;
            text-align: center;
        }
        th {
            background: #2c3e50;
            color: #fff;
        }
        tr:nth-child(even) { background: #f9f9f9; }

        .box {
            width: 80%;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .score {
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
        }
        .baik { color: green; }
        .cukup { color: orange; }
        .kurang { color: red; }
        canvas { margin-top: 20px; }

        /* ===== RESPONSIVE SECTION ===== */
        @media (max-width: 992px) {
            table, .box {
                width: 95%;
            }
        }
        @media (max-width: 576px) {
            table {
                width: 100%;
                font-size: 12px;
            }
            th, td {
                padding: 6px 8px;
            }
            .box {
                width: 100%;
                padding: 12px;
            }
            .score {
                font-size: 14px;
            }
            h2 { font-size: 18px; }
        }
    </style>
</head>

<body>

<h2>Data Governance</h2>

<table>
    <tr>
        <th>Legalitas</th>
        <th>Kepatuhan Syariah</th>
        <th>Transparansi</th>
        <th>Integritas</th>
    </tr>
    <?php if ($row): ?>
    <tr>
        <td><?= $row['legalitas']; ?></td>
        <td><?= $row['kepatuhan_syariah']; ?></td>
        <td><?= $row['transparansi']; ?></td>
        <td><?= $row['integritas']; ?></td>
    </tr>
    <?php else: ?>
    <tr><td colspan="4">Belum ada data</td></tr>
    <?php endif; ?>
</table>

<div class="box">
    <h3>Penilaian Governance:</h3>
    <p class="score">Skor: <b><?= round($persen); ?>%</b></p>
    <p class="score">Kategori: 
        <span class="<?php 
            if ($persen == 100) echo 'baik'; 
            elseif ($persen >= 50) echo 'cukup'; 
            else echo 'kurang'; 
        ?>">
        <?= $kategori; ?>
        </span>
    </p>

    <h3>Penilaian Maqasid:</h3>
    <p class="score"><?= $maqasid_kategori; ?></p>

    <div style="width: 300px; height: 300px; margin: auto;">
        <canvas id="governanceChart"></canvas>
    </div>
</div>
<div class="mb-3">
    <button onclick="window.history.back()" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </button>
</div>

<script>
    const ctx = document.getElementById('governanceChart');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Legalitas', 'Kepatuhan Syariah', 'Transparansi', 'Integritas'],
            datasets: [{
                label: 'Governance',
                data: [
                    <?= $row['legalitas'] ?? 0 ?>,
                    <?= $row['kepatuhan_syariah'] ?? 0 ?>,
                    <?= $row['transparansi'] ?? 0 ?>,
                    <?= $row['integritas'] ?? 0 ?>
                ],
                backgroundColor: ['#27ae60','#2980b9','#f39c12','#e74c3c'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>

</body>
</html>

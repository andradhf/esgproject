<?php
include "../config/config.php";
session_start();

// cek login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$umkm_id = $_SESSION['umkm_id'];

// ambil data terakhir dari database
$result = $conn->query("SELECT * FROM environmental WHERE umkm_id = $umkm_id ORDER BY id DESC LIMIT 1");
$data = $result->fetch_assoc();

if ($data) {
    $listrik = (int)$data['listrik'];
    $air     = (int)$data['air'];
    $limbah  = (int)$data['limbah'];
    $bahan   = (int)$data['bahan_baku'];

    // ambil indikator maqasid
    $hifzmal   = (int)$data['hifzmal'];
    $hifzmal2  = (int)$data['hifzmal2'];
    $hifznafs  = (int)$data['hifznafs'];
    $hifzdin   = (int)$data['hifzdin'];

    // hitung jumlah centang
    $total_hifz = $hifzmal + $hifzmal2 + $hifznafs + $hifzdin;

    // tentukan kategori maqasid
    if ($total_hifz <= 2) {
        $kategori_maqasid = "<span style='color:red;font-weight:bold;'>Tidak Sesuai ❌</span>";
    } elseif ($total_hifz == 3) {
        $kategori_maqasid = "<span style='color:orange;font-weight:bold;'>Cukup Sesuai ⚠️</span>";
    } else { // 4
        $kategori_maqasid = "<span style='color:green;font-weight:bold;'>Sesuai ✅</span>";
    }
} else {
    $listrik = $air = $limbah = $bahan = 0;
    $kategori_maqasid = "<span style='color:gray;'>Belum ada data</span>";
}

// fungsi nilai
function nilai($val, $batas1, $batas2, $reverse = false) {
    if ($reverse) {
        if ($val >= $batas1) return 100;
        elseif ($val >= $batas2) return 70;
        else return 40;
    } else {
        if ($val <= $batas1) return 100;
        elseif ($val <= $batas2) return 70;
        else return 40;
    }
}

$score_listrik = nilai($listrik, 500, 1000);
$score_air = nilai($air, 50, 100);
$score_limbah = nilai($limbah, 50, 500);
$score_bahan = nilai($bahan, 80, 50, true);

$total = ($score_listrik + $score_air + $score_limbah + $score_bahan) / 4;

// cek apakah umkm_id sudah ada di laporan_esg
$cek = $conn->prepare("SELECT id FROM laporan_esg WHERE umkm_id = ?");
$cek->bind_param("i", $umkm_id);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    // sudah ada → update
    $stmt = $conn->prepare("UPDATE laporan_esg SET env = ? WHERE umkm_id = ?");
    $stmt->bind_param("ii", $total, $umkm_id);
} else {
    // belum ada → insert
    $stmt = $conn->prepare("INSERT INTO laporan_esg (umkm_id, env) VALUES (?, ?)");
    $stmt->bind_param("ii", $umkm_id, $total);
}

$stmt->execute();
$stmt->close();
$cek->close();

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Data Environmental</title>

    <!-- Vendor CSS -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: linear-gradient(135deg, #3fab0e, #0096c7);
        }

        /* Judul */
        h2 { 
            color: #2c3e50; 
            text-align: center; 
        }

        /* Tabel responsif */
        table {
            width: 100%;
            max-width: 800px;
            margin: auto;
            background: #fff;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background: #2ecc71;
            color: white;
        }
        td {
            background: #ecf0f1;
        }

        /* Box skor */
        .score-box {
            margin: 20px auto;
            padding: 15px;
            max-width: 400px;
            background: #fff;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        /* Chart responsif */
        .chart-container {
            position: relative;
            width: 100%;
            max-width: 500px;
            height: 400px;   /* fix tinggi biar jelas */
            margin: 30px auto;
            background: #fff;
            padding: 15px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        /* Responsive untuk layar kecil */
        @media (max-width: 576px) {
            body { margin: 10px; }
            table { font-size: 12px; }
            .score-box { width: 100%; padding: 10px; }
            .chart-container { max-width: 100%; height: 300px; padding: 10px; }
        }
    </style>
</head>

<body>
    <h2>Data yang sudah tersimpan</h2>
    <table border='1' cellpadding='5' cellspacing='0'>
        <tr>
            <th>Konsumsi Listrik (kWh)</th>
            <th>Konsumsi Air (m3)</th>
            <th>Volume Limbah</th>
            <th>Bahan Baku(%)</th>
        </tr>
        <tr>
            <td><?= $listrik ?></td>
            <td><?= $air ?></td>
            <td><?= $limbah ?></td>
            <td><?= $bahan ?></td>
        </tr>
    </table>

    <div class="score-box">
        <h3>Penilaian Environmental:</h3>
        Skor: <b><?= round($total, 2) ?>%</b><br>
        <?php
        if ($total >= 85) {
            echo "Kategori: <span style='color:green;font-weight:bold;'>Baik ✅</span>";
        } elseif ($total >= 60) {
            echo "Kategori: <span style='color:orange;font-weight:bold;'>Cukup ⚠️</span>";
        } else {
            echo "Kategori: <span style='color:red;font-weight:bold;'>Kurang ❌</span>";
        }
        ?>
    </div>

    <h3 style="text-align:center;">Penilaian Maqasid:</h3> 
    <p style="text-align:center;">Kategori: <?= $kategori_maqasid ?></p>

    <div class="chart-container">
        <canvas id="envChart"></canvas>
    </div>
    <div class="mb-3">
    <button onclick="window.history.back()" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </button>
    </div>
    
    <script>
        const ctx = document.getElementById('envChart').getContext('2d');
        const envChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Listrik (kWh)', 'Air (m3)', 'Limbah', 'Bahan (%)'],
                datasets: [{
                    label: 'Data Environmental',
                    data: [<?= $listrik ?>, <?= $air ?>, <?= $limbah ?>, <?= $bahan ?>],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            }
        });
    </script>
</body>
</html>

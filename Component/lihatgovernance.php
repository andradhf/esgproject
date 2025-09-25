<?php

include '../config/config.php';

// ambil cuma data terakhir
$result = $conn->query("SELECT * FROM data_governance ORDER BY id DESC LIMIT 1");

$row = $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;

$score = 0;
$persen = 0;
$kategori = "Belum Ada Data";
$maqasid = "Belum Dinilai";

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
        $maqasid = "Sesuai dengan Syariah Maqasid ✅";
    } elseif ($persen >= 50) {
        $kategori = "Cukup ⚠️";
        $maqasid = "Perlu Perbaikan ⚠️";
    } else {
        $kategori = "Kurang ❌";
        $maqasid = "Tidak Sesuai dengan Syariah ❌";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Governance</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #a8e063, #56ab2f);
            margin: 20px;
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
        tr:nth-child(even) {
            background: #f9f9f9;
        }
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
        canvas {
            margin-top: 20px;
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
    <p class="score"><?= $maqasid; ?></p>

    <div style="width: 300px; height: 300px; margin: auto;">
        <canvas id="governanceChart"></canvas>
    </div>
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

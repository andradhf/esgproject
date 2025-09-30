<?php
session_start();
include "../config/config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

// Ambil data terakhir
$sql = "SELECT * FROM maqasid_syariah ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Penilaian Maqasid Syariah</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #56ab2f, #a8e063);
            margin: 0;
            padding: 40px;
        }
        .container {
            width: 85%;
            margin: auto;
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        th, td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #2c3e50;
            color: #fff;
            font-weight: bold;
        }
        tr:hover {
            background: #f1f7ff;
        }
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            color: #fff;
            font-weight: bold;
        }
        .baik {
            background: #27ae60;
        }
        .cukup {
            background: #f39c12;
        }
        .kurang {
            background: #e74c3c;
        }
        .card {
            margin-top: 25px;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<div class="container">
    <h2> Hasil Penilaian Maqasid Syariah</h2>

    <?php
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Hitung jumlah aspek maqasid yang dipilih
        $jumlah = $row['hifz_al_din'] + $row['hifz_al_nafs'] + $row['hifz_al_aql'] + $row['hifz_al_nasl'] + $row['hifz_al_mal'];
        $total_skor = $jumlah * 20; // 5 aspek, bobot 20 poin

        // Tentukan kategori
        if ($total_skor == 100) {
            $kategori = "<span class='badge baik'>Baik ✅</span>";
        } elseif ($total_skor >= 60) {
            $kategori = "<span class='badge cukup'>Cukup ⚠️</span>";
        } else {
            $kategori = "<span class='badge kurang'>Kurang ❌</span>";
        }

        echo "<table>
                <tr>
                    <th>Hifz al-Din (Agama)</th>
                    <th>Hifz al-Nafs (Jiwa)</th>
                    <th>Hifz al-Aql (Akal)</th>
                    <th>Hifz al-Nasl (Keturunan)</th>
                    <th>Hifz al-Mal (Harta)</th>
                    <th>Total Skor</th>
                    <th>Kategori</th>
                </tr>
                <tr>
                    <td>".($row['hifz_al_din'] ? "✅" : "❌")."</td>
                    <td>".($row['hifz_al_nafs'] ? "✅" : "❌")."</td>
                    <td>".($row['hifz_al_aql'] ? "✅" : "❌")."</td>
                    <td>".($row['hifz_al_nasl'] ? "✅" : "❌")."</td>
                    <td>".($row['hifz_al_mal'] ? "✅" : "❌")."</td>
                    <td><b>$total_skor</b></td>
                    <td>$kategori</td>
                </tr>
              </table>";

        echo "<div class='card'>
                <h3>📌 Detail Aspek Maqasid:</h3>
                <p><b>Hifz al-Din:</b> ".($row['hifz_al_din'] ? "Dipilih" : "Tidak")."</p>
                <p><b>Hifz al-Nafs:</b> ".($row['hifz_al_nafs'] ? "Dipilih" : "Tidak")."</p>
                <p><b>Hifz al-Aql:</b> ".($row['hifz_al_aql'] ? "Dipilih" : "Tidak")."</p>
                <p><b>Hifz al-Nasl:</b> ".($row['hifz_al_nasl'] ? "Dipilih" : "Tidak")."</p>
                <p><b>Hifz al-Mal:</b> ".($row['hifz_al_mal'] ? "Dipilih" : "Tidak")."</p>
              </div>";
    } else {
        echo "<p>Belum ada data.</p>";
    }

    $conn->close();
    ?>
</div>
</body>
</html>

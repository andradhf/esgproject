<?php
// debug on supaya error terlihat
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_esgsyariah";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

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
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // pastikan kolom ada dan selalu diparse ke integer 0/1
        $hifz_al_din  = isset($row['hifz_al_din'])  ? intval($row['hifz_al_din'])  : 0;
        $hifz_al_nafs = isset($row['hifz_al_nafs']) ? intval($row['hifz_al_nafs']) : 0;
        $hifz_al_aql  = isset($row['hifz_al_aql'])  ? intval($row['hifz_al_aql'])  : 0;
        $hifz_al_nasl = isset($row['hifz_al_nasl']) ? intval($row['hifz_al_nasl']) : 0;
        $hifz_al_mal  = isset($row['hifz_al_mal'])  ? intval($row['hifz_al_mal'])  : 0;

        // Hitung jumlah dan skor (setiap aspek = 20 poin)
        $jumlah = $hifz_al_din + $hifz_al_nafs + $hifz_al_aql + $hifz_al_nasl + $hifz_al_mal;
        $total_skor = $jumlah * 20;

        // Kategori: "Baik" harus tampil sebagai teks biasa (tanpa badge/background)
        if ($total_skor == 100) {
            $kategori = "Baik";
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
                    <td>".($hifz_al_din ? "✅" : "❌")."</td>
                    <td>".($hifz_al_nafs ? "✅" : "❌")."</td>
                    <td>".($hifz_al_aql ? "✅" : "❌")."</td>
                    <td>".($hifz_al_nasl ? "✅" : "❌")."</td>
                    <td>".($hifz_al_mal ? "✅" : "❌")."</td>
                    <td><b>$total_skor</b></td>
                    <td>$kategori</td>
                </tr>
              </table>";

        echo "<div class='card'>
                <h3>📌 Detail Aspek Maqasid:</h3>
                <p><b>Hifz al-Din:</b> ".($hifz_al_din ? "Dipilih" : "Tidak")."</p>
                <p><b>Hifz al-Nafs:</b> ".($hifz_al_nafs ? "Dipilih" : "Tidak")."</p>
                <p><b>Hifz al-Aql:</b> ".($hifz_al_aql ? "Dipilih" : "Tidak")."</p>
                <p><b>Hifz al-Nasl:</b> ".($hifz_al_nasl ? "Dipilih" : "Tidak")."</p>
                <p><b>Hifz al-Mal:</b> ".($hifz_al_mal ? "Dipilih" : "Tidak")."</p>
              </div>";
    } else {
        echo "<p>Belum ada data.</p>";
    }

    $conn->close();
    ?>
</div>
</body>
</html>

<?php
include "../config/config.php";
session_start();

// Cek login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['umkm_id'])) {
    die("Error: umkm_id belum diset di session.");
}

$umkm_id = $_SESSION['umkm_id'];

// Ambil data terakhir milik user
$sql = "SELECT * FROM data_keuangan WHERE umkm_id = ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $umkm_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <title>Hasil Penilaian Keuangan Syariah</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #3fab0e, #0096c7);
            margin-top: 20px;
            padding: 40px;
        }
        .container { width: 85%; margin: auto; }
        h2 { text-align: center; color: #2c3e50; margin-bottom: 30px; }
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
        tr:hover { background: #f1f7ff; }
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            color: #fff;
            font-weight: bold;
        }
        .baik { background: #27ae60; }
        .cukup { background: #f39c12; }
        .kurang { background: #e74c3c; }
        .card {
            margin-top: 25px;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        @media (max-width: 768px) {
            body { padding: 15px; }
            .container { width: 100%; }
            table { font-size: 12px; }
            th, td { padding: 8px; }
            h2 { font-size: 18px; }
            .card { padding: 15px; font-size: 13px; }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Hasil Penilaian Keuangan Syariah</h2>

    <?php
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // 🔹 Hitung jumlah maqasid yang terisi
        $maqasid_total = 0;
        $maqasid_detail = [];

        if (!empty($row['maqasid_pendapatan_din'])) {
            $maqasid_total++;
            $maqasid_detail[] = "Pendapatan: " . $row['maqasid_pendapatan_din'];
        }
        if ($row['maqasid_pendapatan_mal'] == 1) {
            $maqasid_total++;
            $maqasid_detail[] = "Pendapatan: Hifz al-Mal";
        }
        if (!empty($row['maqasid_ziswaf_din'])) {
            $maqasid_total++;
            $maqasid_detail[] = "ZISWAF: " . $row['maqasid_ziswaf_din'];
        }
        if ($row['maqasid_ziswaf_mal'] == 1) {
            $maqasid_total++;
            $maqasid_detail[] = "ZISWAF: Hifz al-Mal";
        }
        if (!empty($row['maqasid_pembiayaan'])) {
            $maqasid_total++;
            $maqasid_detail[] = "Pembiayaan: " . $row['maqasid_pembiayaan'];
        }

        // 🔹 Tentukan kategori maqasid
        if ($maqasid_total >= 5) {
            $kategori = "<span class='badge baik'>Sesuai ✅</span>";
            $skor_maqasid = 100;
        } elseif ($maqasid_total >= 3) {
            $kategori = "<span class='badge cukup'>Cukup Sesuai ⚠️</span>";
            $skor_maqasid = 80;
        } elseif ($maqasid_total >= 1) {
            $kategori = "<span class='badge kurang'>Tidak Sesuai ❌</span>";
            $skor_maqasid = 60;
        } else {
            $kategori = "<span class='badge kurang'>Tidak Ada Data ❌</span>";
            $skor_maqasid = 0;
        }

        // 🔹 Tampilkan "Ya" / "Tidak" untuk pendapatan halal
        $pendapatan_halal_tampil = ($row['pendapatan_halal'] == 1.00) ? 'Ya' : 'Tidak';

        // 🔹 Tampilkan hasil
        echo "
        <div class='table-responsive'>
        <table class='table table-bordered table-striped table-sm'>
            <tr>
                <th>Pendapatan Halal</th>
                <th>ZISWAF</th>
                <th>Pembiayaan</th>
                <th>Jumlah Maqasid</th>
                <th>Penilaian Maqasid</th>
            </tr>
            <tr>
                <td>{$pendapatan_halal_tampil}</td>
                <td>{$row['ziswaf']}</td>
                <td>{$row['pembiayaan']}</td>
                <td><b>{$maqasid_total}</b></td>
                <td>{$kategori}</td>
            </tr>
        </table>
        </div>";

        // 🔹 Detail maqasid
        echo "<div class='card'>
            <h5>📜 Detail Maqasid yang Dipilih:</h5>";
        if (!empty($maqasid_detail)) {
            echo "<ul>";
            foreach ($maqasid_detail as $m) {
                echo "<li>{$m}</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Tidak ada maqasid yang dipilih.</p>";
        }
        echo "</div>";

        // 🔹 Simpan kategori/score ke laporan_esg
        $cek = $conn->prepare("SELECT id FROM laporan_esg WHERE umkm_id = ?");
        $cek->bind_param("i", $umkm_id);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            $stmt2 = $conn->prepare("UPDATE laporan_esg SET keu = ? WHERE umkm_id = ?");
            $stmt2->bind_param("ii", $skor_maqasid, $umkm_id);
        } else {
            $stmt2 = $conn->prepare("INSERT INTO laporan_esg (umkm_id, keu) VALUES (?, ?)");
            $stmt2->bind_param("ii", $umkm_id, $skor_maqasid);
        }
        $stmt2->execute();
        $stmt2->close();
        $cek->close();

    } else {
        echo "<p>Belum ada data keuangan yang dimasukkan.</p>";
    }

    $stmt->close();
    $conn->close();
    ?>
</div>

<div class="mb-3">
    <button onclick="window.history.back()" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </button>
</div>
</body>
</html>




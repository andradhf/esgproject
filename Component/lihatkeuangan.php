<?php
include "../config/config.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['umkm_id'])) {
    die("Error: umkm_id belum diset di session.");
}

$umkm_id = $_SESSION['umkm_id'];

$sql = "SELECT * FROM data_keuangan ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);


?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <title>Hasil Penilaian Keuangan Syariah</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(to right, #83a4d4, #b6fbff); margin: 0; padding: 40px; }
        .container { width: 85%; margin: auto; }
        h2 { text-align: center; color: #2c3e50; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        th, td { padding: 15px; text-align: center; border-bottom: 1px solid #eee; }
        th { background: #2c3e50; color: #fff; font-weight: bold; }
        tr:hover { background: #f1f7ff; }
        .badge { padding: 6px 12px; border-radius: 20px; color: #fff; font-weight: bold; }
        .baik { background: #27ae60; }
        .cukup { background: #f39c12; }
        .kurang { background: #e74c3c; }
        .card { margin-top: 25px; background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="container">
    <h2>Hasil Penilaian Keuangan Syariah</h2>
    <?php
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $skor_pendapatan = ($row['pendapatan_halal'] == "Halal") ? 100 : 50;
        $skor_ziswaf     = ($row['ziswaf'] > 0) ? 100 : 50;
        $skor_pembiayaan = (!empty($row['pembiayaan'])) ? 100 : 50;
        $total_skor = round(($skor_pendapatan + $skor_ziswaf + $skor_pembiayaan) / 3);

        $maqasid_pendapatan = !empty($row['maqasid_pendapatan']) ? explode(", ", $row['maqasid_pendapatan']) : [];
        $maqasid_ziswaf     = !empty($row['maqasid_ziswaf']) ? explode(", ", $row['maqasid_ziswaf']) : [];
        $maqasid_pembiayaan = !empty($row['maqasid_pembiayaan']) ? explode(", ", $row['maqasid_pembiayaan']) : [];
        $jumlah_maqasid = count($maqasid_pendapatan) + count($maqasid_ziswaf) + count($maqasid_pembiayaan);

        if ($jumlah_maqasid >= 5) {
            $kategori = "<span class='badge baik'>Baik ✅</span>"; $total_skor = 100;
        } elseif ($total_skor >= 80) {
            $kategori = "<span class='badge baik'>Baik ✅</span>";
        } elseif ($total_skor >= 60) {
            $kategori = "<span class='badge cukup'>Cukup ⚠️</span>";
        } else {
            $kategori = "<span class='badge kurang'>Kurang ❌</span>";
        }

        echo "<table>
                <tr><th>Pendapatan Halal</th><th>ZISWAF</th><th>Pembiayaan</th><th>Maqasid Dipilih</th><th>Total Skor</th><th>Kategori</th></tr>
                <tr>
                    <td>{$row['pendapatan_halal']} <br><small>(Skor: $skor_pendapatan)</small></td>
                    <td>{$row['ziswaf']} <br><small>(Skor: $skor_ziswaf)</small></td>
                    <td>{$row['pembiayaan']} <br><small>(Skor: $skor_pembiayaan)</small></td>
                    <td>$jumlah_maqasid</td>
                    <td><b>$total_skor</b></td>
                    <td>$kategori</td>
                </tr>
              </table>";

        echo "<div class='card'>
                <h3>📌 Detail Maqasid yang Dipilih:</h3>
                <p><b>Pendapatan:</b> ".implode(", ", $maqasid_pendapatan)."</p>
                <p><b>ZISWAF:</b> ".implode(", ", $maqasid_ziswaf)."</p>
                <p><b>Pembiayaan:</b> ".implode(", ", $maqasid_pembiayaan)."</p>
              </div>";
    } else {
        echo "<p>Belum ada data.</p>";
    }
    $cek = $conn->prepare("SELECT id FROM laporan_esg WHERE umkm_id = ?");
    $cek->bind_param("i", $umkm_id);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        // sudah ada → update
        $stmt = $conn->prepare("UPDATE laporan_esg SET keu = ? WHERE umkm_id = ?");
        $stmt->bind_param("ii", $total_skor, $umkm_id);
    } else {
        // belum ada → insert
        $stmt = $conn->prepare("INSERT INTO laporan_esg (umkm_id, keu) VALUES (?, ?)");
        $stmt->bind_param("ii", $umkm_id, $total_skor);
    }

    $stmt->execute();
    $stmt->close();
    $cek->close();
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

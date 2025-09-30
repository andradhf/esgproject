<?php
include "../config/config.php";
session_start();

// cek login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$umkm_id = $_SESSION['umkm_id'];

// ambil dari form
$din  = isset($_POST['hifz_al_din']) ? 1 : 0;
$nafs = isset($_POST['hifz_al_nafs']) ? 1 : 0;
$aql  = isset($_POST['hifz_al_aql']) ? 1 : 0;
$nasl = isset($_POST['hifz_al_nasl']) ? 1 : 0;
$mal  = isset($_POST['hifz_al_mal']) ? 1 : 0;

// hitung total skor
$jumlah     = $din + $nafs + $aql + $nasl + $mal;
$total_skor = $jumlah * 20;

// tentukan kategori
if ($total_skor == 100) {
    $kategori = "Baik";
} elseif ($total_skor >= 60) {
    $kategori = "Cukup";
} else {
    $kategori = "Kurang";
}

// cek apakah umkm_id sudah ada di tabel
$cek = $conn->prepare("SELECT id FROM maqasid_syariah WHERE umkm_id = ?");
$cek->bind_param("i", $umkm_id);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    // update
    $stmt = $conn->prepare("UPDATE maqasid_syariah SET indikator = ?, skor = ?, created_at = NOW() WHERE umkm_id = ?");
    $stmt->bind_param("sii", $kategori, $total_skor, $umkm_id);
} else {
    // insert
    $stmt = $conn->prepare("INSERT INTO maqasid_syariah (umkm_id, indikator, skor) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $umkm_id, $kategori, $total_skor);
}
$stmt->execute();
$stmt->close();
$cek->close();

// ambil hasil terakhir untuk tampil
$sql = "SELECT * FROM maqasid_syariah WHERE umkm_id = $umkm_id LIMIT 1";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Penilaian Maqasid Syariah</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
               background: linear-gradient(to right, #56ab2f, #a8e063);
               margin: 0; padding: 40px; }
        .container { width: 85%; margin: auto; }
        h2 { text-align: center; color: #2c3e50; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; background: #fff;
                border-radius: 12px; overflow: hidden;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        th, td { padding: 15px; text-align: center; border-bottom: 1px solid #eee; }
        th { background: #2c3e50; color: #fff; font-weight: bold; }
        tr:hover { background: #f1f7ff; }
        .badge { padding: 6px 12px; border-radius: 20px; color: #fff; font-weight: bold; }
        .cukup { background: #f39c12; }
        .kurang { background: #e74c3c; }
        .baik { background: #27ae60; }
    </style>
</head>
<body>
<div class="container">
    <h2>Hasil Penilaian Maqasid Syariah</h2>
    <table>
        <tr>
            <th>Total Skor</th>
            <th>Kategori</th>
        </tr>
        <tr>
            <td><b><?php echo $row['skor']; ?></b></td>
            <td>
                <?php
                if ($row['indikator'] == "Baik") {
                    echo "<span class='badge baik'>Baik ✅</span>";
                } elseif ($row['indikator'] == "Cukup") {
                    echo "<span class='badge cukup'>Cukup ⚠️</span>";
                } else {
                    echo "<span class='badge kurang'>Kurang ❌</span>";
                }
                ?>
            </td>
        </tr>
    </table>
</div>
</body>
</html>

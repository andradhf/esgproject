<?php
// debug_penilaian_sosial.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/*
  1) Simpan file ini ke: C:\xampp\htdocs\esgmonitoring\debug_penilaian_sosial.php
  2) Buka: http://localhost/esgmonitoring/debug_penilaian_sosial.php
  3) Kalau muncul error, copy seluruh output dan kirim ke saya.
*/

// -- KONFIG: ubah kalau MySQL user/pass berbeda --
$host = 'localhost';
$user = 'root';
$pass = '';

// connect ke server (tanpa memilih database)
$conn = @new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    echo "<h2 style='color:red;'>Gagal konek ke MySQL server:</h2><pre>" . htmlentities($conn->connect_error) . "</pre>";
    exit;
}
echo "<h3>Koneksi ke MySQL server: <span style='color:green;'>OK</span></h3>";

// list databases
$dbs = [];
$res = $conn->query("SHOW DATABASES");
if ($res) {
    echo "<b>Daftar database di server:</b><br>";
    while ($r = $res->fetch_assoc()) {
        $dbs[] = $r['Database'];
    }
    echo "<pre>" . htmlentities(implode(", ", $dbs)) . "</pre>";
} else {
    echo "<p style='color:red;'>Gagal menjalankan SHOW DATABASES: " . htmlentities($conn->error) . "</p>";
}

// cari database yang punya tabel data_sosial
$found_db = null;
foreach ($dbs as $d) {
    $conn->select_db($d);
    $q = $conn->query("SHOW TABLES LIKE 'data_sosial'");
    if ($q && $q->num_rows > 0) {
        $found_db = $d;
        break;
    }
}

if (!$found_db) {
    echo "<h3 style='color:orange;'>Tabel <code>data_sosial</code> tidak ditemukan di semua DB di server.</h3>";
    echo "<p>Jika kamu ingin saya buat tabel contoh, jalankan SQL CREATE TABLE berikut di phpMyAdmin (atau beritahu nama DB yang benar):</p>";
    echo "<pre>
CREATE TABLE data_sosial (
  id INT AUTO_INCREMENT PRIMARY KEY,
  karyawan INT DEFAULT 0,
  karyawan_perempuan INT DEFAULT 0,
  insiden_k3 INT DEFAULT 0,
  kejadian VARCHAR(255) DEFAULT '',
  pelatihan_sdm INT DEFAULT 0,
  produk_halal VARCHAR(20) DEFAULT '',
  csr VARCHAR(20) DEFAULT '',
  ziswaf BIGINT DEFAULT 0,
  maqasid_tenaga TEXT,
  maqasid_k3 TEXT,
  maqasid_sdm TEXT,
  maqasid_produk TEXT,
  maqasid_sosial TEXT
);
    </pre>";
    $conn->close();
    exit;
}

echo "<h3>Menemukan tabel <code>data_sosial</code> di database: <span style='color:green;'>$found_db</span></h3>";

// gunakan db yang ditemukan
$conn->select_db($found_db);

// tampilkan struktur tabel
echo "<h4>DESCRIBE data_sosial</h4>";
$desc = $conn->query("DESCRIBE data_sosial");
if ($desc) {
    echo "<table border='1' cellpadding='6' style='border-collapse:collapse;'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    while ($d = $desc->fetch_assoc()) {
        echo "<tr><td>" . htmlentities($d['Field']) . "</td><td>" . htmlentities($d['Type']) . "</td><td>" . htmlentities($d['Null']) . "</td><td>" . htmlentities($d['Key']) . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red;'>Gagal DESCRIBE: " . htmlentities($conn->error) . "</p>";
    $conn->close();
    exit;
}

// ambil 1 record terbaru
$res = $conn->query("SELECT * FROM data_sosial ORDER BY id DESC LIMIT 1");
if (!$res) {
    echo "<p style='color:red;'>Query SELECT error: " . htmlentities($conn->error) . "</p>";
    $conn->close();
    exit;
}

if ($res->num_rows === 0) {
    echo "<h3 style='color:orange;'>Tabel kosong — belum ada data (INSERT manual via phpMyAdmin dulu atau submit form).</h3>";
    echo "<p>Contoh INSERT SQL (jalankan di phpMyAdmin → SQL):</p>";
    echo "<pre>INSERT INTO data_sosial (karyawan, karyawan_perempuan, insiden_k3, kejadian, pelatihan_sdm, produk_halal, csr, ziswaf)
VALUES (100, 40, 0, 'Tidak ada', 8, 'Ya', 'Ya', 90000);</pre>";
    $conn->close();
    exit;
}

$row = $res->fetch_assoc();
echo "<h4>Data terbaru (raw):</h4><pre>" . htmlentities(print_r($row, true)) . "</pre>";

// helper untuk kompatibilitas nama kolom (cek beberapa kemungkinan nama)
function getf($row, $candidates) {
    foreach ($candidates as $c) {
        if (isset($row[$c])) return $row[$c];
    }
    return null;
}

// ambil nilai dengan fallback nama-nama kolom yang mungkin
$karyawan = intval(getf($row, ['karyawan','jumlah_karyawan']));
$karyawan_perempuan = intval(getf($row, ['karyawan_perempuan','jumlah_karyawan_perempuan']));
$insiden_k3 = intval(getf($row, ['insiden_k3','jumlah_insiden']));
$pelatihan_sdm = intval(getf($row, ['pelatihan_sdm','pelatihan_sdm','jam_pelatihan']));
$produk_halal = getf($row, ['produk_halal','produkhalal','produk_halal']);
$csr = getf($row, ['csr']);
$ziswaf = floatval(getf($row, ['ziswaf','ziswaf_rp','ziswaf_value']));

// hitung skor berdasarkan bobot yang kamu minta (total = 100)
$skor = 0;
$indicators = [];

if ($karyawan > 0) { $skor += 20; $indicators[] = ['Jumlah Karyawan', '✅']; } else { $indicators[] = ['Jumlah Karyawan', '❌']; }
if ($karyawan_perempuan > 0) { $skor += 10; $indicators[] = ['Karyawan Perempuan', '✅']; } else { $indicators[] = ['Karyawan Perempuan', '❌']; }
if ($insiden_k3 === 0) { $skor += 20; $indicators[] = ['Insiden K3 = 0', '✅']; } else { $indicators[] = ['Insiden K3 = 0', '❌ (' . $insiden_k3 . ')']; }
if ($pelatihan_sdm > 0) { $skor += 20; $indicators[] = ['Pelatihan SDM', '✅']; } else { $indicators[] = ['Pelatihan SDM', '❌']; }
if (strtolower($produk_halal) === 'ya') { $skor += 15; $indicators[] = ['Produk Halal', '✅']; } else { $indicators[] = ['Produk Halal', '❌']; }
if (strtolower($csr) === 'ya') { $skor += 15; $indicators[] = ['CSR', '✅']; } else { $indicators[] = ['CSR', '❌']; }

$kategori = 'Kurang ❌';
if ($skor >= 80) $kategori = 'Sangat Baik

<?php include "../config/config.php"; ?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pembiayaan = $_POST['pembiayaan'];
    $investasi  = $_POST['investasi'];

    $sql = "INSERT INTO pilar_esg_detail (kategori, indikator, nilai) VALUES
        ('Keuangan Syariah', 'Pembiayaan Syariah', '$pembiayaan'),
        ('Keuangan Syariah', 'Investasi Syariah', '$investasi')";
    
    if ($conn->query($sql)) {
        echo "<script>alert('Data Syariah tersimpan'); window.location='index.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Input Keuangan Syariah</title>
  <link href="../css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h3>Form Keuangan Syariah</h3>
  <form method="POST">
    <div class="form-group">
      <label>Pembiayaan Syariah (Rp)</label>
      <input type="number" name="pembiayaan" class="form-control" required>
    </div>
    <div class="form-group">
      <label>Investasi Syariah (Rp)</label>
      <input type="number" name="investasi" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-warning">Simpan</button>
  </form>
</div>
</body>
</html>
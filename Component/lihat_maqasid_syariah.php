<?php
session_start();
include "../config/config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$sql = "SELECT * FROM maqasid_syariah ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Maqasid Syariah</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

  <div class="container">
    <h2 class="mb-4">Data Maqasid Syariah</h2>

    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Hifz al-Din</th>
          <th>Hifz al-Nafs</th>
          <th>Hifz al-Aql</th>
          <th>Hifz al-Nasl</th>
          <th>Hifz al-Mal</th>
          <th>Created At</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id']; ?></td>
              <td><?= $row['hifz_al_din'] ? "✅" : "❌"; ?></td>
              <td><?= $row['hifz_al_nafs'] ? "✅" : "❌"; ?></td>
              <td><?= $row['hifz_al_aql'] ? "✅" : "❌"; ?></td>
              <td><?= $row['hifz_al_nasl'] ? "✅" : "❌"; ?></td>
              <td><?= $row['hifz_al_mal'] ? "✅" : "❌"; ?></td>
              <td><?= $row['created_at']; ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="text-center">Belum ada data</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <a href="maqasid_syariah.php" class="btn btn-success">+ Input Baru</a>
    <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
  </div>

</body>
</html>

<?php $conn->close(); ?>

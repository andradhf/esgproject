<?php
include "../config/config.php";
session_start();

// Cek login
// Pastikan user sudah login dan role = admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // jika bukan admin, redirect ke login
    header("Location: login.php");
    exit;
}

// Simpan role ke variabel (opsional)
$role = $_SESSION['role'];
// Hitung total UMKM
$total_umkm = 0;
$res = $conn->query("SELECT COUNT(*) AS total FROM umkm_accounts");
if ($res && $row = $res->fetch_assoc()) {
    $total_umkm = $row['total'];
}

// Ambil rata-rata ESG
$avg_esg = 0;
$res2 = $conn->query("SELECT AVG(avg_score) AS rata2 FROM laporan_esg");
if ($res2 && $row2 = $res2->fetch_assoc()) {
    $avg_esg = round($row2['rata2'], 2);
}

// Grafik tren ESG
$labels = [];
$data_scores = [];

$sql = "SELECT DATE(dc.timestamp) AS tanggal, AVG(dc.avg_score) AS rata2
        FROM (
            SELECT dc.*
            FROM data_chart dc
            JOIN (
                SELECT laporan_id, DATE(timestamp) AS tgl, MAX(timestamp) AS latest_ts
                FROM data_chart
                GROUP BY laporan_id, DATE(timestamp)
            ) latest
            ON dc.laporan_id = latest.laporan_id 
               AND DATE(dc.timestamp) = latest.tgl
               AND dc.timestamp = latest.latest_ts
        ) dc
        GROUP BY DATE(dc.timestamp)
        ORDER BY DATE(dc.timestamp)";

$res = $conn->query($sql);

while ($row = $res->fetch_assoc()) {
    $labels[] = $row['tanggal'];
    $data_scores[] = round($row['rata2'], 2);
}
// Data UMKM + laporan ESG + maqasid
$sql = "
    SELECT u.id, u.nama_umkm, u.created_at,
           l.env, l.sos, l.gov, l.keu, l.avg_score,
           GROUP_CONCAT(m.indikator SEPARATOR ', ') AS maqasid
    FROM umkm_accounts u
    LEFT JOIN laporan_esg l 
        ON l.umkm_id = u.id 
       AND l.created_at = (
            SELECT MAX(l2.created_at) 
            FROM laporan_esg l2 
            WHERE l2.umkm_id = u.id
       )
    LEFT JOIN maqasid_syariah m 
        ON m.umkm_id = u.id
    GROUP BY u.id, u.nama_umkm, u.created_at, l.env, l.sos, l.gov, l.keu, l.avg_score
    ORDER BY u.nama_umkm ASC
";
$result = $conn->query($sql);

// Dropdown UMKM
$umkmList = [];
$res = $conn->query("SELECT id, nama_umkm FROM umkm_accounts ORDER BY nama_umkm ASC");
while ($row = $res->fetch_assoc()) {
    $umkmList[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>ESG Syariah UMKM</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <style>
    body {margin:0;font-family:Arial,sans-serif;background:#f8f9fa;}
    .sidebar {position:fixed;top:0;left:0;width:220px;height:100%;background:#183a37;color:#fff;padding-top:30px;}
    .sidebar h2{text-align:center;margin-bottom:40px;color:#5ec576;}
    .sidebar a{display:block;padding:12px 20px;color:#fff;text-decoration:none;font-size:16px;border-radius:5px;}
    .sidebar a:hover,.sidebar a.active{background:#14524f;}
    .brand-logo img{width:50px;height:auto;}
    .main-content{margin-left:240px;padding:20px;}
    .content .card{background:#fff;padding:10px;border-radius:10px;box-shadow:0 2px 5px rgba(0,0,0,.1);margin-bottom:20px;}
    .card-header{font-weight:bold;font-size:24px;margin-bottom:10px;text-align:center;}
    .cards{display:flex;gap:15px;margin-bottom:20px;}
    .cards .card{flex:1;background:#5ec576;color:#fff;text-align:center;padding:12px;border-radius:8px;font-weight:bold;}
    .cards .card h3{font-size:20px;margin-top:8px;}
    .table-container{margin-top:20px;background:#fff;padding:20px;border-radius:10px;box-shadow:0 2px 5px rgba(0,0,0,.1);}
    table{width:100%;border-collapse:collapse;}
    th,td{padding:10px;border-bottom:1px solid #ddd;text-align:center;}
    th{background:#f1f1f1;}
    .export-btn{float:right;background:#28a745;color:#fff;padding:6px 12px;border-radius:5px;border:none;cursor:pointer;}
    #reportArea{background:#fff;padding:30px;margin:20px auto;border-radius:8px;border:2px solid #333;max-width:700px;box-shadow:0 2px 8px rgba(0,0,0,.2);}
    #reportArea h2{text-align:center;margin-bottom:20px;}
    .gauge{text-align:center;margin:20px 0;}
    .gauge-value{font-size:48px;font-weight:bold;color:#2e7d32;}
    .legend{display:flex;justify-content:center;gap:20px;margin:20px 0;}
    .legend div{display:flex;align-items:center;gap:6px;font-size:14px;}
    .circle{width:14px;height:14px;border-radius:50%;display:inline-block;}
    .baik{background:green;}
    .cukup{background:blue;}
    .kurang{background:red;}
    .cards-report{display:flex;justify-content:space-around;flex-wrap:wrap;margin-top:20px;}
    .cards-report .card{width:160px;padding:15px;border-radius:10px;color:#fff;text-align:center;margin:10px;}
    .env{background:#2ecc71;}
    .sosial{background:#3498db;}
    .gov{background:#d4ac0d;color:#000;}
    .keuangan{background:#e84393;}
    .footer{margin-top:25px;font-weight:bold;}
    .btn{display:inline-block;background:green;color:#fff;padding:10px 18px;border:none;border-radius:8px;cursor:pointer;margin-top:20px;}
    .hidden{display:none;}
  </style>
</head>
<body>
<!-- Sidebar -->
<div class="sidebar">
  <a class="sidebar-brand d-flex align-items-center" href="#">
    <div class="brand-logo"><img src="../img/logo_uhamka.png" alt="UHAMKA"></div>
    <h2 class="brand-text">ESG SYARIAH<br>UMKM</h2>
  </a>
  <a href="#" class="active" onclick="showPage('dashboard', event)">🏠 Dashboard</a>
  <a href="#" onclick="showPage('dataumkm', event)">🏪 Data UMKM</a>
  <a href="#" onclick="showPage('reports', event)">📑 Reports</a>
  <?php if (isset($_SESSION['user_id'])): ?>
  <a class="collapse-item" href="logout.php">⚙️ Logout</a>
  <?php endif; ?>
</div>

<!-- Main Content -->
<div class="main-content">

  <!-- Dashboard -->
  <div id="dashboard">
    <div class="content">
      <div class="card mb-4">
        <div class="card-header">Selamat Datang Admin</div>
        <div class="card-body">
          <p>Anda berhasil login sebagai <b>Administrator</b>.</p>
          <p>Silakan pilih menu di sidebar untuk mulai mengelola sistem.</p>
        </div>
      </div>
    </div>
    <div class="cards">
      <div class="card">Total UMKM <h3><?= $total_umkm ?></h3></div>
      <div class="card">Rata Rata Skor ESG <h3><?= $avg_esg ?></h3></div>
    </div>
    <div class="table-container">
      <h3>Tren Penilaian ESG <button class="export-btn">Export</button></h3>
      <canvas id="esgChart" height="100"></canvas>
    </div>
  </div>

  <!-- Data UMKM -->
  <div id="dataumkm" class="hidden">
    <div class="table-container">
      <h3>Data UMKM</h3>
      <table>
        <thead>
          <tr>
            <th>No</th><th>Nama UMKM</th><th>Tanggal</th><th>Env</th>
            <th>Sosial</th><th>Gov</th><th>Keu Syariah</th><th>Maqasid Syariah</th><th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          if ($result && $result->num_rows > 0) {
              $no = 1;
              while ($row = $result->fetch_assoc()) {
                  $status = "Belum Ada";
                  if ($row['avg_score'] >= 80) {
                        $status = "Baik";
                    } elseif ($row['avg_score'] >= 60 && $row['avg_score'] < 80) {
                        $status = "Cukup";
                    } elseif ($row['avg_score'] < 60) {
                        $status = "Kurang";
                    }
                  echo "<tr>";
                  echo "<td>".$no++."</td>";
                  echo "<td>".htmlspecialchars($row['nama_umkm'])."</td>";
                  echo "<td>".date("d-m-Y", strtotime($row['created_at']))."</td>";
                  echo "<td>".($row['env'] ?? '-')."</td>";
                  echo "<td>".($row['sos'] ?? '-')."</td>";
                  echo "<td>".($row['gov'] ?? '-')."</td>";
                  echo "<td>".($row['keu'] ?? '-')."</td>";
                  echo "<td>".($row['maqasid'] ?? '-')."</td>";
                  echo "<td>".$status."</td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='9'>Belum ada data UMKM</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Reports -->
  <div id="reports" class="hidden">
    <div class="table-container">
      <h3>Reports</h3>
      <label for="umkmSelect"><b>Pilih UMKM:</b></label>
      <select id="umkmSelect" onchange="updateReport()">
        <option value="">-- Pilih UMKM --</option>
        <?php foreach ($umkmList as $umkm): ?>
          <option value="<?= $umkm['id'] ?>"><?= htmlspecialchars($umkm['nama_umkm']) ?></option>
        <?php endforeach; ?>
      </select>

      <div id="reportArea">
        <h2>Hasil Penilaian ESG Syariah UMKM</h2>
        <div class="info" id="reportInfo"></div>
        <div class="gauge"><div class="gauge-value" id="reportScore">0</div></div>
        <div class="legend">
          <div><span class="circle baik"></span> Baik</div>
          <div><span class="circle cukup"></span> Cukup Baik</div>
          <div><span class="circle kurang"></span> Kurang Baik</div>
        </div>
        <div class="cards-report">
          <div class="card env" id="envCard"></div>
          <div class="card sosial" id="sosialCard"></div>
          <div class="card gov" id="govCard"></div>
          <div class="card keuangan" id="keuCard"></div>
        </div>
        <div class="footer" id="reportFooter"></div>
      </div>
      <div style="text-align:center;">
        <button id="downloadPdf" class="btn">Download PDF</button>
      </div>
    </div>
  </div>
</div>

<script>
const ctx = document.getElementById('esgChart').getContext('2d');
new Chart(ctx,{
  type:'line',
  data:{labels:<?= json_encode($labels) ?>,
        datasets:[{label:'Rata-rata Skor ESG',
                   data:<?= json_encode($data_scores) ?>,
                   borderColor:'rgba(75,192,192,1)',
                   backgroundColor:'rgba(75,192,192,0.2)',
                   borderWidth:2,tension:0.3,fill:true,
                   pointBackgroundColor:'rgba(75,192,192,1)'}]},
  options:{responsive:true,
    scales:{y:{beginAtZero:true,max:100,title:{display:true,text:'Average Score'}},
            x:{title:{display:true,text:'Tanggal'}}}}
});

function updateReport(){
  const umkmId=document.getElementById("umkmSelect").value;
  if(!umkmId) return;
  fetch(`get_report.php?umkm_id=${umkmId}`)
  .then(res=>res.json())
  .then(data=>{
      if(data.error){
      document.getElementById("reportInfo").innerHTML=`<p>${data.error}</p>`;
      document.getElementById("reportScore").innerText="0";
      document.getElementById("envCard").innerHTML="<h4>Environmental</h4><p>-</p>";
      document.getElementById("sosialCard").innerHTML="<h4>Sosial</h4><p>-</p>";
      document.getElementById("govCard").innerHTML="<h4>Governance</h4><p>-</p>";
      document.getElementById("keuCard").innerHTML="<h4>Keuangan Syariah</h4><p>-</p>";
      document.getElementById("reportFooter").innerHTML="";
      return;
    }
    document.getElementById("reportInfo").innerHTML=
      `<p>Nama: <b>${data.nama_umkm}</b></p>
       <p>Tanggal: ${data.created_at}</p>
       <p>Ringkasan Skor ESG</p>
       <p><b>Skor Total ESG</b></p>`;
    document.getElementById("reportScore").innerText=data.avg_score ?? 0;
    document.getElementById("envCard").innerHTML=`<h4>Environmental</h4><p>${data.env ?? '-'}/100</p>`;
    document.getElementById("sosialCard").innerHTML=`<h4>Sosial</h4><p>${data.sos ?? '-'}/100</p>`;
    document.getElementById("govCard").innerHTML=`<h4>Governance</h4><p>${data.gov ?? '-'}/100</p>`;
    document.getElementById("keuCard").innerHTML=`<h4>Keuangan Syariah</h4><p>${data.keu ?? '-'}/100</p>`;
    document.getElementById("reportFooter").innerHTML=
      `Integrasi Maqasid Syariah: <span style="color:green;">${data.maqasid_syariah ?? '-'}</span>`;
  });
}
function showPage(pageId,event){
  document.querySelectorAll('.main-content > div').forEach(div=>div.classList.add('hidden'));
  document.getElementById(pageId).classList.remove('hidden');
  document.querySelectorAll('.sidebar a').forEach(a=>a.classList.remove('active'));
  event.target.classList.add('active');
}
document.getElementById("downloadPdf").addEventListener("click",function(){
  var element=document.getElementById("reportArea");
  var opt={margin:[0.3,0.3,0.3,0.3],filename:'report_umkm.pdf',
           image:{type:'jpeg',quality:0.98},
           html2canvas:{scale:3,useCORS:true,scrollY:0},
           jsPDF:{unit:'mm',format:'a4',orientation:'portrait'}};
  html2pdf().set(opt).from(element).save();
});
</script>
</body>
</html>
<?php
include "../config/config.php";

if (!isset($_GET['umkm_id'])) {
    echo json_encode(["error" => "UMKM ID diperlukan"]);
    exit;
}

$umkm_id = intval($_GET['umkm_id']);

$sql = "
    SELECT l.env, l.sos, l.gov, l.keu, l.avg_score, l.created_at,
           u.nama_umkm,
           (SELECT GROUP_CONCAT(indikator SEPARATOR ', ')
            FROM maqasid_syariah m WHERE m.umkm_id = u.id) AS maqasid_syariah
    FROM laporan_esg l
    JOIN umkm_accounts u ON l.umkm_id = u.id
    WHERE l.umkm_id = ?
    ORDER BY l.created_at DESC
    LIMIT 1
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $umkm_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode($row);
} else {
    echo json_encode(["error" => "Belum ada laporan"]);
}
$stmt->close();

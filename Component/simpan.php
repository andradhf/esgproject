
?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $listrik = isset($_POST['listrik']) ? $_POST['listrik'] : 0;
    $air = isset($_POST['air']) ? $_POST['air'] : 0;
    $limbah = isset($_POST['limbah']) ? $_POST['limbah'] : 0;
    $bahan = isset($_POST['bahan']) ? $_POST['bahan'] : 0;

    // Format penyimpanan → hanya simpan 1 data terbaru
    $data = "Listrik: $listrik,Air: $air,Limbah: $limbah,Bahan: $bahan";

    // Simpan ke file (overwrite agar tidak menumpuk)
    file_put_contents("data.txt", $data);

    // Redirect ke lihat.php setelah simpan
    header("Location: lihat.php");
    exit();
} else {
    echo "Tidak ada data yang dikirim.";
}
?>

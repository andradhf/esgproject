<?php
$host = "localhost";
$user = "root"; // ganti sesuai MySQL kamu
$pass = "";     // password MySQL kamu
$db   = "esg_monitoring";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
<?php
$host = "localhost"; 
$user = "root"; 
$pass = ""; 
$db   = "db_esgsyariah"; 

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>

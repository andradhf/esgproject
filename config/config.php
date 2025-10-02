<?php
$host = "localhost"; 
$user = "umkmesgr_report"; 
$pass = "a&JtY*D3CAyC#}q&"; 
$db   = "umkmesgr_db_esgsyariah"; 

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>

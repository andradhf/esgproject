<?php
$host = "localhost";
$user = "root";   // default XAMPP
$pass = "";       // default XAMPP (kosong)
$dbname = "esgsyariah"; // harus sama dengan yang dibuat di phpMyAdmin

$koneksi = new mysqli($host, $user, $pass, $dbname);

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}
?>

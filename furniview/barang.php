<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "furniview_db";

// Buat koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id_barang, nama_barang, harga, stok, foto FROM Barang";
$result = $conn->query($sql);
?>

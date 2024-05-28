<?php
require 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST["fullname"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $telepon = $_POST["telepon"]; // Tambah baris ini untuk mengambil nilai telepon dari form
    $alamat = $_POST["alamat"]; // Tambah baris ini untuk mengambil nilai alamat dari form

    $query_sql = "INSERT INTO users (fullname, username, email, password, telepon, alamat)
    VALUES ('$fullname', '$username', '$email', '$password', '$telepon', '$alamat')"; // Ubah nama tabel menjadi 'users'

    if (mysqli_query($conn, $query_sql)) {
        header("Location: index.html");
    } else {
        echo "Pendaftaran Gagal : " . mysqli_error($conn);
    }
}
?>

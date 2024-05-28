<?php
require('koneksi.php');

// Pastikan request menggunakan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["email"]) && isset($_POST["password"])) {
        $email = $_POST["email"];
        $password = $_POST["password"];

        $stmt = $conn->prepare("SELECT iduser, fullname FROM users WHERE email = ? AND password = ?");
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("ss", $email, $password);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Mulai session
            session_start();
            // Ambil data pengguna dari hasil query
            $row = $result->fetch_assoc();
            // Simpan informasi pengguna ke dalam sesi
            $_SESSION['user_id'] = $row['iduser'];
            $_SESSION['username'] = $row['fullname'];
            // Redirect ke halaman dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<center><h1>Email atau Password Anda Salah. Silahkan Coba Login Kembali.</h1>
            <button><strong><a href='index.html'>Login</a></strong></button></center>";
        }

        $stmt->close();
    } else {
        echo "<center><h1>Email dan Password harus diisi.</h1>
        <button><strong><a href='index.html'>Login</a></strong></button></center>";
    }
} else {
    // Jika request bukan menggunakan metode POST, tampilkan pesan error
    echo "<center><h1>Invalid request method.</h1>
    <button><strong><a href='index.html'>Login</a></strong></button></center>";
}

$conn->close();
?>

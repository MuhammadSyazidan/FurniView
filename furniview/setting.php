<?php
session_start();
require('koneksi.php');

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil user ID dari sesi
$user_id = $_SESSION['user_id'];

// Lakukan query untuk mengambil data pengguna berdasarkan ID
$stmt = $conn->prepare("SELECT * FROM users WHERE iduser = ?");
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Ambil data pengguna
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $username = $user['username'];
    $nama = $user['fullname'];
    $no_telp = $user['telepon'];
    $email = $user['email'];
    $alamat = $user['alamat'];
} else {
    // Jika data pengguna tidak ditemukan, lakukan tindakan lainnya
    // Misalnya, kembali ke halaman login
    header("Location: login.php");
    exit;
}

$stmt->close();

// Inisialisasi pesan notifikasi
$notification = "";

// Tangani permintaan pengeditan profil
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari formulir
    $new_nama = $_POST['nama'];
    $new_no_telp = $_POST['no_telp'];
    $new_email = $_POST['email'];
    $new_alamat = $_POST['alamat'];

    // Lakukan query untuk mengupdate informasi profil pengguna
    $stmt_update = $conn->prepare("UPDATE users SET fullname = ?, telepon = ?, email = ?, alamat = ? WHERE iduser = ?");
    if ($stmt_update === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt_update->bind_param("ssssi", $new_nama, $new_no_telp, $new_email, $new_alamat, $user_id);
    if ($stmt_update->execute()) {
        // Set pesan notifikasi
        $notification = "Data profil berhasil diperbarui";
    } else {
        // Jika terjadi kesalahan dalam mengupdate, tampilkan pesan kesalahan
        $error_message = "Error updating profile: " . htmlspecialchars($conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="stylebrd.css">
    <style>
        /* Ini adalah gaya tambahan yang perlu disesuaikan */
        .profile form input[type="text"] {
            width: calc(100% - 20px); /* Lebar textbox */
            padding: 12px; /* Padding yang lebih besar */
            margin-bottom: 15px; /* Jarak antar textbox */
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .logout-container {
            text-align: center; /* Mengatur agar konten berada di tengah */
            margin-top: 20px; /* Menambahkan margin atas untuk memisahkan dengan form */
        }

        .logout-container .logbutton {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background-color: #f44336; /* Warna merah untuk logout button */
            color: white;
        }

        .logout-container .logbutton:hover {
            background-color: #d32f2f; /* Warna merah yang lebih gelap saat hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2><a href="dashboard.html" style="text-decoration: none; color: #fff;">FurniView</a></h2>
               <ul>
                   <li><a href="dashboard.php">Products</a></li>
                   <li><a href="order.php">Order History</a></li>
                   <li><a href="setting.php">Profile</a></li>
               </ul>
        </div>
        <div class="content">
            <div class="header">
                <h1>Profile</h1>
            </div>
            <div class="main-content">
                <div class="profile">
                    <!-- Tampilkan pesan notifikasi -->
                    <?php if (!empty($notification)) : ?>
                        <div class="notification"><?php echo $notification; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <label for="nama">Nama:</label>
                        <input type="text" id="nama" name="nama" value="<?php echo $nama; ?>">

                        <label for="no_telp">Nomor Telepon:</label>
                        <input type="text" id="no_telp" name="no_telp" value="<?php echo $no_telp; ?>">

                        <label for="email">Alamat Email:</label>
                        <input type="text" id="email" name="email" value="<?php echo $email; ?>">

                        <label for="alamat">Alamat:</label>
                        <input type="text" id="alamat" name="alamat" value="<?php echo $alamat; ?>">

                        <input type="submit" value="Simpan">
                    </form>
                    <?php
                    // Tampilkan pesan kesalahan jika ada
                    if (isset($error_message)) {
                        echo '<div class="error">' . $error_message . '</div>';
                    }
                    ?>
                </div>
            </div>
            <div class="logout-container">
                <a href="logout.php"><button class="logbutton">Log Out</button></a>
            </div>
        </div>
    </div>
</body>
</html>

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

// Ambil nama pengguna
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $username = $user['username'];
} else {
    // Jika data pengguna tidak ditemukan, lakukan tindakan lainnya
    $username = "Guest";
}

$stmt->close();

// Ambil id_barang dari URL
$id_barang = $_GET['id'];

// Query untuk mengambil detail produk berdasarkan id_barang
$sql = "SELECT id_barang, nama_barang, harga, stok, deskripsi, foto FROM Barang WHERE id_barang = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_barang);
$stmt->execute();
$result = $stmt->get_result();

// Saat tombol "Buy" ditekan
if (isset($_POST['buy'])) {
    // Ambil data dari form
    $id_barang = $_POST['id_barang'];
    $harga = $_POST['harga'];

    // Cek stok produk
    $sql_stok = "SELECT stok FROM Barang WHERE id_barang = ?";
    $stmt_stok = $conn->prepare($sql_stok);
    $stmt_stok->bind_param("i", $id_barang);
    $stmt_stok->execute();
    $result_stok = $stmt_stok->get_result();
    $row_stok = $result_stok->fetch_assoc();
    $stok = $row_stok['stok'];

    if ($stok > 0) {
        // Kurangi stok
        $new_stok = $stok - 1;
        $update_sql = "UPDATE Barang SET stok = ? WHERE id_barang = ?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param("ii", $new_stok, $id_barang);
        if ($stmt_update->execute()) {
            // Generate ID transaksi secara otomatis
            $id_transaksi = uniqid();

            // Simpan ke order history
            $status = 'Processed';
            $insert_sql = "INSERT INTO Transaksi (id_transaksi, custUser, id_barang, jumlah, status, total_harga) 
               VALUES (?, ?, ?, 1, ?, ?)";
            $stmt_insert = $conn->prepare($insert_sql);
            $stmt_insert->bind_param("ssssd", $id_transaksi, $user_id, $id_barang, $status, $harga);
            if ($stmt_insert->execute()) {
                echo "<p>Product purchased successfully!</p>";
            } else {
                echo "<p>Error: " . htmlspecialchars($conn->error) . "</p>";
            }
        } else {
            echo "<p>Error updating record: " . htmlspecialchars($conn->error) . "</p>";
        }
    } else {
        echo "<p>Product out of stock.</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Detail - FurniView</title>
    <link rel="stylesheet" href="styledetail.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2><a href="dashboard.php" style="text-decoration: none; color: #fff;">FurniView</a></h2>
            <ul>
                <li><a href="dashboard.php">Products</a></li>
                <li><a href="order.php">Order History</a></li>
                <li><a href="setting.html">Profile</a></li>
            </ul>
        </div>
        <div class="content">
            <div class="header">
                <h1>Product Detail</h1>
            </div>
            <div class="main-content">
                <div id="product-detail" class="card">
                    <?php
                    if ($result->num_rows > 0) {
                        // Tampilkan data produk
                        $row = $result->fetch_assoc();
                        echo "<div class='product-image'>
                                <img src='uploads/" . $row["foto"] . "' alt='" . $row["nama_barang"] . "'>
                              </div>";
                        echo "<div class='product-info'>
                                <h2>" . $row["nama_barang"] . "</h2>
                                <p><strong>Price:</strong> Rp" . number_format($row["harga"], 2, ',', '.') . "</p>
                                <p><strong>Stock:</strong> " . $row["stok"] . "</p>
                                <p><strong>Description:</strong> " . $row["deskripsi"] . "</p>
                                <form method='POST' action=''>
                                    <input type='hidden' name='id_barang' value='" . $row["id_barang"] . "'>
                                    <input type='hidden' name='harga' value='" . $row["harga"] . "'>
                                    <button type='submit' name='buy' class='buy-button'>Buy Product</button>
                                </form>
                              </div>";
                    } else {
                        echo "<<p>Product not found.</p>";
                    }
                    ?>
                    <div class="back-button">
                        <a href="dashboard.php">Back to Products</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

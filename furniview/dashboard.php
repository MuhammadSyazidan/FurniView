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
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FurniView - Products</title>
    <link rel="stylesheet" href="stylebrd.css">
    <style>
        .welcome-text {
            float: left;
        }
        .title-text {
            float: right;
        }
        .header:after {
            content: "";
            display: table;
            clear: both;
        }
        .main-content {
            overflow: auto;
        }
        .products-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2><a href="dashboard.php" style="text-decoration: none; color: #fff;">FurniView</a></h2>
            <ul>
                <li><a href="dashboard.php">Products</a></li>
                <li><a href="order.php">Order History</a></li>
                <li><a href="setting.php">Profile</a></li>
            </ul>
        </div>
        <div class="content">
            <div class="header">
                <!-- Tampilkan informasi pengguna di sini -->
                <div class="welcome-text"><h2>Welcome, <?= $username ?>!</h2></div>
            </div>
            <div class="main-content">
                <div class="products-container">
                    <div id="products" class="card">
                        <h2>Products</h2>
                        <table>
                            <tr>
                                <th>Product ID</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Photo</th>
                                <th></th>
                            </tr>
                            <?php include 'barang.php'; ?>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?= $row["id_barang"] ?></td>
                                        <td><a href="product_detail.php?id=<?= $row["id_barang"] ?>"><?= $row["nama_barang"] ?></a></td>
                                        <td>Rp<?= $row["harga"] ?></td>
                                        <td><?= $row["stok"] ?></td>
                                        <td><img src="uploads/<?= $row["foto"] ?>" alt="<?= $row["nama_barang"] ?>" style="width: 50px;"></td>
                                        <td><a href="product_detail.php?id=<?= $row["id_barang"] ?>"><button>View Details</button></a></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6">No products found</td></tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
require('koneksi.php');

// Ambil id_transaksi dari URL
$id_transaksi = $_GET['id_transaksi'];

// Query untuk mengambil detail transaksi berdasarkan id_transaksi
$sql = "SELECT * FROM Transaksi WHERE id_transaksi = '$id_transaksi'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $custUser = $row['custUser'];
    $id_barang = $row['id_barang'];
    $jumlah = $row['jumlah'];
    $tanggal_transaksi = $row['tanggal_transaksi'];
    $status = $row['status'];
    $total_harga = $row['total_harga'];
} else {
    echo "No transaction found.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Detail</title>
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
                <h1>Order Detail</h1>
            </div>
            <div class="main-content">
                <div id="order-detail" class="card">
                    <h2>Order Detail</h2>
                    <p><strong>Order ID:</strong> <?php echo $id_transaksi; ?></p>
                    <p><strong>Customer:</strong> <?php echo $custUser; ?></p>
                    <p><strong>Product ID:</strong> <?php echo $id_barang; ?></p>
                    <p><strong>Quantity:</strong> <?php echo $jumlah; ?></p>
                    <p><strong>Date:</strong> <?php echo $tanggal_transaksi; ?></p>
                    <p><strong>Status:</strong> <?php echo $status; ?></p>
                    <p><strong>Total:</strong> Rp<?php echo number_format($total_harga, 2, ',', '.'); ?></p>
                    <div class="back-button">
                        <a href="order.php">Back to Order History</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Menutup koneksi
$conn->close();
?>

<?php
require('koneksi.php');

// Tangani permintaan penghapusan order
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_order'])) {
    // Ambil ID transaksi yang akan dihapus
    $id_transaksi = $_POST['id_transaksi'];

    // Lakukan query untuk menghapus order dari database
    $stmt_delete = $conn->prepare("DELETE FROM Transaksi WHERE id_transaksi = ?");
    $stmt_delete->bind_param("i", $id_transaksi);

    if ($stmt_delete->execute()) {
        // Redirect kembali ke halaman order.php setelah berhasil menghapus
        header("Location: order.php");
        exit;
    } else {
        // Jika terjadi kesalahan dalam menghapus, tampilkan pesan kesalahan
        $error_message = "Error deleting order: " . htmlspecialchars($stmt_delete->error);
    }
}

// Ambil semua data dari tabel Transaksi
$sql = "SELECT id_transaksi, custUser, tanggal_transaksi, total_harga, status FROM Transaksi";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" href="stylebrd.css">
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
                <h1>Order History</h1>
            </div>
            <div class="main-content">
                <div id="orders" class="card">
                    <h2>Order History</h2>
                    <table>
                        <tr>
                            <th>Order ID</th>
                            <th>User ID</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        <?php
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['id_transaksi'] . "</td>";
                                echo "<td>" . $row['custUser'] . "</td>";
                                echo "<td>" . $row['tanggal_transaksi'] . "</td>";
                                echo "<td>Rp" . number_format($row['total_harga'], 2, ',', '.') . "</td>";
                                echo "<td>" . $row['status'] . "</td>";
                                echo "<td>";
                                echo "<form method='POST' action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "'>";
                                echo "<input type='hidden' name='id_transaksi' value='" . $row['id_transaksi'] . "'>";
                                echo "<input type='submit' name='delete_order' value='Delete' style='background-color: red; color: white;'>";
                                echo "</form>";
                                echo "</td>";
                                echo "<td>";
                                echo "<form method='GET' action='orderdetail.php'>";
                                echo "<input type='hidden' name='id_transaksi' value='" . $row['id_transaksi'] . "'>";
                                echo "<input type='submit' value='Detail'>";
                                echo "</form>";
                                echo "</td>";
                                echo "</tr>";                                
                            }
                        } else {
                            echo "<tr><td colspan='6'>No orders found.</td></tr>";
                        }
                        ?>
                    </table>
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

<?php
// Pastikan form menggunakan enctype="multipart/form-data" untuk menangani unggahan file

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

// Deklarasi variabel untuk menyimpan pesan kesalahan
$error = "";

// Pastikan semua field telah diisi
if(isset($_POST["product_id"]) && isset($_POST["product_name"]) && isset($_POST["price"]) && isset($_POST["stock"]) && isset($_POST["deskripsi"]) && isset($_FILES["photo"])) {
    // Ambil data dari formulir
    $product_id = $_POST["product_id"];
    $product_name = $_POST["product_name"];
    $price = $_POST["price"];
    $stock = $_POST["stock"];
    $description = $_POST["deskripsi"];
    
    // Lokasi penyimpanan file yang diunggah
    $targetDir = "uploads/";
    
    // Path lengkap file yang akan disimpan
    $targetFile = $targetDir . basename($_FILES["photo"]["name"]);
    
    // Coba pindahkan file ke lokasi yang ditentukan
    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
        echo "File has been uploaded successfully.";
        
        // Simpan nama file ke dalam database
        $filename = basename($_FILES["photo"]["name"]);
        
        // SQL untuk menyimpan data produk ke dalam database
        $sql = "INSERT INTO Barang (id_barang, nama_barang, harga, stok, deskripsi, foto) VALUES ('$product_id', '$product_name', $price, $stock, '$description', '$filename')";
        
        // Eksekusi SQL statement
        if ($conn->query($sql) === TRUE) {
            echo "Product data saved to database successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        $error = "Sorry, there was an error uploading your file.";
    }
} else {
    $error = "All fields are required.";
}

// Jika ada kesalahan, tampilkan pesan kesalahan
if ($error != "") {
    echo $error;
}

// Tutup koneksi ke database
$conn->close();
?>

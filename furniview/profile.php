<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Sertakan file koneksi.php
require('koneksi.php');

// Tangani permintaan pengeditan profil
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari formulir
    $new_nama = $_POST['nama'];
    $new_no_telp = $_POST['no_telp'];
    $new_email = $_POST['email'];
    $new_alamat = $_POST['alamat'];

    // Lakukan query untuk mengupdate informasi profil pengguna
    $stmt_update = $conn->prepare("UPDATE users SET fullname = ?, telepon = ?, email = ?, alamat = ? WHERE iduser = ?");
    
    // Periksa apakah query berhasil dipersiapkan
    if ($stmt_update === false) {
        $_SESSION['notification'] = 'Gagal mempersiapkan query: ' . htmlspecialchars($conn->error);
        header("Location: setting.php");
        exit;
    }

    // Eksekusi query update
    if ($stmt_update->execute([$new_nama, $new_no_telp, $new_email, $new_alamat, $_SESSION['user_id']])) {
        // Periksa apakah pembaruan berhasil dilakukan
        if ($stmt_update->rowCount() > 0) {
            // Simpan pesan notifikasi dalam session
            $_SESSION['notification'] = 'Data profil berhasil diperbarui';
        } else {
            // Jika tidak ada data yang berubah
            $_SESSION['notification'] = 'Tidak ada perubahan pada data profil';
        }
    } else {
        // Jika terjadi kesalahan dalam mengupdate, tampilkan pesan kesalahan
        $_SESSION['notification'] = 'Gagal memperbarui data profil: ' . htmlspecialchars($stmt_update->error);
    }

    // Redirect kembali ke halaman setting.php
    header("Location: setting.php");
    exit;
}
?>

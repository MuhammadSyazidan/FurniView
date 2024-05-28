<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password != $confirm_password) {
        echo "New passwords do not match.";
        exit();
    }

    // Tidak melakukan hashing pada kata sandi baru
    $plain_text_password = $new_password;

    $stmt = $conn->prepare("SELECT * FROM login_pembeli WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE login_pembeli SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $plain_text_password, $email);
        if ($stmt->execute()) {
            echo "Your password has been updated successfully.";
        } else {
            echo "Failed to update password.";
        }
    } else {
        echo "No user found with that email address.";
    }

    $stmt->close();
} else {
    echo "Invalid request method.";
}

$conn->close();
?>

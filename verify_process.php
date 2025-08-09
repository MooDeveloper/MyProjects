<?php
require 'db.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $code = $_POST['code'];

    // Fetch the stored verification code from the database
    $stmt = $conn->prepare("SELECT verification_code FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($stored_code);
    $stmt->fetch();
    $stmt->close();

    // Check if the code entered by the user matches the one stored in the database
    if ($stored_code === $code) {
        // Update user status to verified
        $stmt = $conn->prepare("UPDATE users SET is_verified = 1 WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->close();

        // Display an alert and redirect to index.php
        echo "<script>
                alert('Email verified successfully. Wait for admin approval. We will send you an email when the admin approves you.');
                window.location.href = 'index.php';
              </script>";
    } else {
        // Show an error message if the code is invalid
        echo "<script>
                alert('Invalid verification code.');
                window.location.href = 'verify_code.php';
              </script>";
    }
}
?>


<?php
session_start();
require 'db.php'; // Database connection

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

if (isset($_GET['email'])) {
    $email = $_GET['email'];

    // Update the user status to verified
    $stmt = $conn->prepare("UPDATE users SET is_approved = 1 WHERE email = ?");
    $stmt->bind_param("s", $email);
    if ($stmt->execute()) {
        // After user is verified, call the Python script to send a welcome email
        $escaped_email = escapeshellarg($email); // Safely escape the email to prevent shell injection
        exec("python3 Scripts/send_welcome_email.py $escaped_email"); // Call the Python script

        echo "User has been verified and welcome email sent.";
        header("Location: admin_panel.php");
        exit;
    } else {
        echo "Error verifying user.";
    }

    $stmt->close();
}
?>


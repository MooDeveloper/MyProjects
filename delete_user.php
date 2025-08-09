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

    // Delete the user from the database
    $stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    if ($stmt->execute()) {
        echo "User has been deleted.";
        header("Location: admin_panel.php");
        exit;
    } else {
        echo "Error deleting user.";
    }

    $stmt->close();
}
?>

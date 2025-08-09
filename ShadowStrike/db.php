<?php
$servername = "localhost"; // Update if needed
$username = "phpmyadmin";        // Your database username
$password = "tesla123";    // Your database password
$dbname = "shadowstrike";  // Database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

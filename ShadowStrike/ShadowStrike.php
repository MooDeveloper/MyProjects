<?php
session_start(); // Start the session



// Secure session settings
ini_set('session.cookie_httponly', 1); // Prevent JavaScript from accessing the session cookie
ini_set('session.use_strict_mode', 1); // Use strict session mode to prevent hijacking

// Validate the user's login status
if (!isset($_SESSION['user_name'])) {
    header("Location: index.php"); // Redirect to login page if not logged in
    exit;
}

// Prevent XSS by sanitizing session variables
$user = htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Offensive Security - Penetration Testing</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to an external CSS file -->
</head>
<body>
    <div class="container">
        <h3>Offensive Security</h3>
        <p>Welcome, <?php echo $user; ?>!</p>
        <button onclick="location.href='enumeration_scan.php';">Enumeration & Scanning</button>
        <button onclick="location.href='spoof.php';">Man in the middle Attck</button>
        <button onclick="alert('Functionality Coming Soon');">Exploit & Attack</button>
        <button onclick="alert('Functionality Coming Soon');">Post Exploitation</button>
        <button onclick="alert('Functionality Coming Soon');">Reporting</button>
    </div>
</body>
</html>


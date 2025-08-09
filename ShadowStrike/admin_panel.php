<?php
session_start();
require 'db.php'; // Database connection

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

// Fetch all unverified users
$stmt = $conn->prepare("SELECT * FROM users WHERE is_approved = 0");
$stmt->execute();
$unverified_users = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        .action-btn {
            color: white;
            background-color: #007bff;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
        }
        .action-btn:hover {
            background-color: #0056b3;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Admin Panel</h2>
    <a href="admin_logout.php" class="action-btn">Logout</a>
    <h3>Unverified Users</h3>
    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Telegram Bot</th>
            <th>Actions</th>
        </tr>
        <?php while ($user = $unverified_users->fetch_assoc()) : ?>
        <tr>
            <td><?php echo htmlspecialchars($user['name']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo htmlspecialchars($user['telegram_bot']); ?></td>
            <td>
                <a href="verify_user.php?email=<?php echo urlencode($user['email']); ?>" class="action-btn">Verify</a>
                <a href="delete_user.php?email=<?php echo urlencode($user['email']); ?>" class="action-btn delete-btn">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>

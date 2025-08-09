<?php
require 'db.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Input sanitization to prevent SQL injection
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    // CSRF Token Validation
    session_start();
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo "<script>
                alert('Invalid CSRF token.');
                window.location.href = 'index.php'; // Redirect to login page
              </script>";
        exit;
    }

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT name, id, password, is_verified, is_approved FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_name, $user_id, $stored_password, $is_verified, $is_approved);
    $stmt->fetch();
    $stmt->close();

    // Password verification
    if (password_verify($password, $stored_password)) {
        if ($is_verified && $is_approved) {
            // User is verified and approved, create a secure session
            session_regenerate_id(true); // Prevent session fixation

            // Secure session settings
            ini_set('session.cookie_httponly', 1); // Prevent JavaScript access
            ini_set('session.use_strict_mode', 1); // Enable strict session mode

            // Store user information in the session
            $_SESSION['user_name'] = $user_name;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_email'] = htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); // Prevent XSS
            $_SESSION['is_logged_in'] = true;

            // Regenerate a new CSRF token for the session
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            // Include ShadowStrike.php
            include('ShadowStrike.php');
        } elseif (!$is_verified) {
            echo "<script>
                    
                   window.location.href = 'verify_code.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Your account is awaiting admin approval.');
                    window.location.href = 'index.php'; // Redirect to login page
                  </script>";
        }
    } else {
        echo "<script>
                alert('Invalid email or password.');
                window.location.href = 'index.php'; // Redirect to login page
              </script>";
    }
}
?>


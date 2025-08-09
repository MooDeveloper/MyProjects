<?php
$ip = filter_var($_GET['ip'], FILTER_VALIDATE_IP);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARP Spoofing Tool</title>
    <style>
        body {
            font-family: 'Orbitron', sans-serif;
            background-color: #0A0A0A;
            color: #F00;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        h1 {
            font-size: 40px;
            color: #FF0000;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 20px;
            text-shadow: 0 0 15px #FF0000, 0 0 30px #FF4444, 0 0 45px #FF6666;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        input[type="text"] {
            background-color: #1A1A1A;
            color: #FFF;
            border: 2px solid #F00;
            padding: 8px;
            width: 250px;
            font-size: 14px;
            border-radius: 6px;
            box-shadow: 0 0 8px rgba(255, 0, 0, 0.7);
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        input[type="text"]:focus {
            outline: none;
            border-color: #FF5555;
            box-shadow: 0 0 15px rgba(255, 85, 85, 0.8);
        }
        input[type="submit"] {
            background-color: #F00;
            color: #FFF;
            font-size: 14px;
            padding: 6px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            box-shadow: 0 0 8px rgba(255, 0, 0, 0.7);
        }
        input[type="submit"]:hover {
            background-color: #FF5555;
            transform: scale(1.05);
        }
        input[type="submit"]:active {
            transform: scale(1.02);
        }
        .log-output {
            background-color: #000;
            border: 2px solid #F00;
            color: #0F0;
            padding: 10px;
            width: 90%;
            max-width: 600px;
            height: 200px;
            margin-top: 20px;
            font-size: 12px;
            font-family: 'Courier New', Courier, monospace;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(255, 0, 0, 0.8);
            text-align: left;
            white-space: pre-wrap;
            word-wrap: break-word;
            overflow-y: auto;
        }
        .footer {
            color: #F00;
            font-size: 12px;
            position: fixed;
            bottom: 10px;
            text-align: center;
            width: 100%;
            letter-spacing: 2px;
            animation: fadeInOut 4s infinite;
        }
        /* Keyframes for fade-in and fade-out effect */
        @keyframes fadeInOut {
            0%, 20% { opacity: 0; }
            30%, 70% { opacity: 1; }
            80%, 100% { opacity: 0; }
        }
        .hidden {
            display: none;
        }
    </style>
    <script>
        function toggleStopButton(show) {
            const stopButton = document.getElementById('stopButton');
            stopButton.classList.toggle('hidden', !show);
        }
    </script>
</head>
<body>
    <h1>ARP Spoofing Tool</h1>
    <form method="POST" action="" onsubmit="toggleStopButton(true)">
        
        <input type="text" name="target_ip" id="target_ip" value="<?php echo isset($ip) ? htmlspecialchars($ip) : ''; ?>" readonly>

        <input type="submit" value="Start ARP Spoofing">
    </form>

    <form method="POST" action="" id="stopButton" >
        <input type="submit" name="stop" value="Stop ARP Spoofing">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Start ARP spoofing
        if (isset($_POST['target_ip'])) {
            $target_ip = escapeshellarg($_POST['target_ip']);
            $log_file = '/var/www/html/ShadowStrike/Logs/spoof_log.txt';

            // Command to start ARP spoofing
            $command = "sudo python3 /var/www/html/ShadowStrike/Scripts/arp_spoof.py -t $target_ip > $log_file 2>&1 &";
            shell_exec($command);  // Run the ARP spoofing script

            echo "<div class='log-output' id='logOutput'>";
            
            // Read the log file and display its content in real-time
            while (true) {
                $output = file_get_contents($log_file);
                if ($output) {
                    echo nl2br($output);  // Format the output with line breaks
                    ob_flush();
                    flush();  // Immediately display output
                     // DNS spoofing button
        echo "<div class='dns-spoof-button'>
        <form method='GET' action='dns_spoof.php' target='_blank'>
            <input type='hidden' name='ip' value='$target_ip'>
            <input type='submit' value='Start DNS Spoofing'>
        </form>
      </div>";


                    // Exit the loop if the output contains the stop message
                    if (strpos($output, "ARP spoofing cleanup completed") !== false) {
                        break;
                    }
                }
                sleep(1);  // Check for new content every second
            }
          
        }
        // Stop ARP spoofing
        elseif (isset($_POST['stop'])) {
            $log_file1 = '/var/www/html/ShadowStrike/Logs/kill_spoof_log.txt';
            $command1 = "sudo /bin/bash /var/www/html/ShadowStrike/Scripts/kill_arp_processes.sh > $log_file1 2>&1 &";

            // Execute the shell command to stop ARP spoofing
            shell_exec($command1); 

            echo "<div class='log-output' id='logOutput'>";
            
            // Read the log file and display its content in real-time
            while (true) {
                $output = file_get_contents($log_file1);
                if ($output) {
                    echo nl2br($output);  // Format the output with line breaks
                    ob_flush();
                    flush();  // Immediately display output

                    // Exit the loop if the output contains the stop message
                    if (strpos($output, "ARP spoofing cleanup completed.") !== false) {
                        break;
                    }
                }
                sleep(1);  // Check for new content every second
            }
           
            echo "<p style='color: green;'>Stopping ARP spoofing...</p>";
        }
    }
    ?>

    <div class="footer">
        Warning: Use this tool responsibly. Unauthorized usage may lead to legal consequences.
    </div>
</body>
</html>

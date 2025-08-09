<?php
// Include the database connection
require 'db.php'; // Ensure db.php is in the same directory or adjust the path

// Initialize an empty array to hold hosts
$hosts = [];

// Handle scan execution
if (isset($_POST['execute_scan'])) {
    // Execute the all_network_scan.sh script
    $output = shell_exec('sudo /var/www/html/ShadowStrike/Scripts/all_network_scan.sh intense');
    if ($output === null) {
        die("Error executing scan script.");
    }
    // Refresh the page to load the latest scan results
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


// Handle manual IP addition
$manualIp = '';
if (isset($_POST['manual_ip'])) {
    $manualIp = filter_var($_POST['manual_ip'], FILTER_VALIDATE_IP);
    if (!$manualIp) {
        echo "<p>Invalid IP address entered. Please try again.</p>";
    } else {
        // Add the manually entered IP address to the hosts list
        // Assuming 'Unknown' for name and 'up' as default status
        $hosts[$manualIp] = [
            'name' => 'Unknown',
            'status' => 'up',
            'entry' => "Manually added IP: $manualIp"
        ];
    }
}

// Prevent XSS by sanitizing session variables
try {
    // Query to get the latest execution_time and corresponding output
    $query = "SELECT output, execution_time 
              FROM all_network_results
              ORDER BY execution_time DESC 
              LIMIT 1";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Fetch the result as an associative array
        $row = $result->fetch_assoc();
        $logDir = $row['output']; // Assuming 'output' contains the log directory or data
        $executionTime = $row['execution_time'];
    } else {
        die("No records found in the database.");
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
} finally {
    // Close the database connection
    $conn->close();
}

// Now we assume the output (log) from the database is a large text field that contains the scan result
$logContents = $logDir; // 'output' contains the logs from the database (no need to read from files)

if (!$logContents) {
    die("No log content found in the database.");
}

// Split the log contents into entries based on a delimiter (blank lines here to separate individual logs)
$entries = preg_split('/\n\s*\n/', $logContents);

// Prepare an array to store hosts
foreach ($entries as $entry) {
    // Check for both "Host is up" and "Host is down" statuses
    if (preg_match('/Nmap scan report for (\S+)\s+\((\d+\.\d+\.\d+\.\d+)\)/', $entry, $matches)) {
        $hostName = $matches[1];
        $hostIp = $matches[2];
        $isUp = strpos($entry, 'Host is up') !== false ? 'up' : 'down';
        $hosts[$hostIp] = [
            'name' => $hostName,
            'status' => $isUp,
            'entry' => $entry
        ];
    } elseif (preg_match('/Nmap scan report for (\d+\.\d+\.\d+\.\d+)/', $entry, $matches)) {
        $hostIp = $matches[1];
        $isUp = strpos($entry, 'Host is up') !== false ? 'up' : 'down';
        $hosts[$hostIp] = [
            'name' => 'Unknown',
            'status' => $isUp,
            'entry' => $entry
        ];
    }
}

// Handle IP address click and include start_arp_spoof.php if IP parameter is set
if (isset($_GET['ip'])) {
    $ip = filter_var($_GET['ip'], FILTER_VALIDATE_IP);
    if ($ip ) {
        include 'start_arp_spoof.php';
        exit;
    } else {
        echo "Invalid IP address or IP not found in the current scan.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Network Scan Results</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap" rel="stylesheet">
    <style>
        body {
            background-image: url('Images/mim6.jpeg');
            background-size: cover;
            background-attachment: fixed;
            color: #d4d4d4;
            font-family: 'Orbitron', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            overflow: hidden;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background-color: rgba(45, 45, 45, 0.75); /* Increased transparency */
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(255, 0, 0, 0.4);
            text-align: center;
            transform: translateY(-25%); /* Move box slightly up */
        }
        h1 {
            color: #ff0000;
            margin-bottom: 20px;
            font-size: 2.5rem;
        }
        .date {
            color: #ff4d4d;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        .host-ip {
            display: inline-block;
            margin: 10px;
            padding: 12px 20px;
            background-color: rgba(30, 30, 30, 0.8);
            color: #ff0000;
            border: 1px solid #ff4d4d;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 1.1rem;
            cursor: pointer;
            text-decoration: none;
        }
        .host-ip:hover {
            color: #ffffff;
            background-color: #ff4d4d;
            transform: scale(1.1);
            box-shadow: 0 0 10px rgba(255, 77, 77, 0.8);
        }
        button {
            background-color: #ff4d4d;
            border: none;
            color: white;
            padding: 12px 20px;
            cursor: pointer;
            font-size: 1.2rem;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        button:hover {
            background-color: #ff3333;
        }
        input[type="text"] {
            padding: 10px;
            font-size: 1.1rem;
            width: 70%;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ff4d4d;
            background-color: rgba(30, 30, 30, 0.8);
            color: white;
        }
        input[type="submit"] {
            background-color: #ff4d4d;
            border: none;
            color: white;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 1.1rem;
            border-radius: 8px;
        }
        input[type="submit"]:hover {
            background-color: #ff3333;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Network Scan Options</h1>
    
    <!-- Form to Execute Scan -->

    
    <div class="date">Last scan date: <?php echo $executionTime; ?></div>
    
    <?php
    if (empty($hosts)) {
        echo '<p>No active hosts found in the last scan.</p>';
    } else {
        foreach ($hosts as $hostIp => $hostName) {
            echo '<a href="?ip=' . htmlspecialchars($hostIp) . '" class="host-ip">' . htmlspecialchars($hostIp) . '</a>';
        }
    }
    ?>
     

    <!-- Form to Manually Add IP -->
    <form method="post">
        <input type="text" name="manual_ip" placeholder="Enter IP Address" value="<?php echo htmlspecialchars($manualIp); ?>" />
        <input type="submit" value="Add IP" />
    </form>
       <form method="post">
        <button type="submit" name="execute_scan">Rescan again</button>
    </form>
</div>

</body>
</html>


<?php
// Include the database connection
require 'db.php'; // Ensure db.php is in the same directory or adjust the path

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
$hosts = [];

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Network Scan Results - Hosts Up</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #0A0A0A;
            color: #ff3b3b;
            font-family: 'Orbitron', sans-serif;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: auto;
            padding: 20px;
            background-color: #1a1a1a;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(255, 0, 0, 0.6);
        }
        h1 {
            text-align: center;
            color: #ff3b3b;
            text-shadow: 0 0 10px #ff0000;
            margin-bottom: 30px;
        }
        .entry {
            border: 1px solid #800000;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #1e1e1e;
            border-radius: 8px;
        }
        .host-ip {
            color: #ff3b3b;
            font-weight: bold;
            cursor: pointer;
        }
        .host-ip:hover {
            text-shadow: 0 0 8px #ff0000;
        }
        .status-up {
            color: #ff3b3b;
        }
        .status-down {
            color: #ff0000;
        }
        pre {
            background-color: #1f1f1f;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #800000;
            color: #ffb3b3;
            max-height: 400px;
            overflow-y: scroll;
        }
        .date {
            text-align: right;
            color: #ff3b3b;
            margin-bottom: 10px;
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background-color: #0A0A0A;
        }
        ::-webkit-scrollbar-thumb {
            background-color: #ff3b3b;
            border-radius: 4px;
            box-shadow: 0 0 6px #ff0000;
        }
    </style>
    <script>
        function triggerScan(ip) {
            const scanType = 'intense'; // Set scan type to intense
            const url = `execute_scan.php?ip=${encodeURIComponent(ip)}&type=${encodeURIComponent(scanType)}`;
            // Open the execute_scan.php page in a new tab
            window.open(url, '_blank');
        }
    </script>
</head>
<body>

<div class="container">
    <h1>Network Scan Results - Hosts Up</h1>
    <div class="date">Last scan date: <?php echo htmlspecialchars($executionTime); ?></div>
    <?php
    // Check if there are any hosts found
    if (empty($hosts)) {
        echo '<p>No hosts found in the log.</p>';
    } else {
        foreach ($hosts as $hostIp => $hostData) {
            if ($hostData['status'] === 'up') {
                echo '<div class="entry">';
                echo '<h2>Host Details:</h2>';
                echo '<p><span class="host-ip" data-ip="' . htmlspecialchars($hostIp) . '" onclick="triggerScan(\'' . htmlspecialchars($hostIp) . '\')">' . htmlspecialchars($hostIp) . '</span> (' . htmlspecialchars($hostData['name']) . ')</p>';
                echo '<p>Status: <span class="' . ($hostData['status'] === 'up' ? 'status-up' : 'status-down') . '">' . ucfirst($hostData['status']) . '</span></p>';
                echo '<pre>' . htmlspecialchars($hostData['entry']) . '</pre>';
                echo '</div>';
            }
        }
    }
    ?>
</div>

</body>
</html>


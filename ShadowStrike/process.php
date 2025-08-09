<?php
$timestamp = isset($_GET['timestamp']) ? $_GET['timestamp'] : '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $target_type = filter_var($_POST['target_type'], FILTER_SANITIZE_STRING);
    $target_ip = isset($_POST['target_ip']) ? filter_var($_POST['target_ip'], FILTER_VALIDATE_IP) : '';
    $web_url = isset($_POST['web_url']) ? filter_var($_POST['web_url'], FILTER_SANITIZE_URL) : '';
    $scan_type = filter_var($_POST['scan_type'], FILTER_SANITIZE_STRING);
    $zombie_ip = !empty($_POST['zombie_ip']) ? filter_var($_POST['zombie_ip'], FILTER_VALIDATE_IP) : '';

    // Check if target IP is valid for endpoint type
    if ($target_type === 'endpoint' && !$target_ip) {
        echo "Invalid target IP address.";
        exit;
    }

    // Check if web URL is valid for web application type
    if ($target_type === 'webapp' && !$web_url) {
        echo "Invalid web application URL.";
        exit;
    }

    // Check if zombie IP is provided and valid for 'idle' scan type
    if ($scan_type === 'idle' && !$zombie_ip) {
        echo "Zombie IP is required for idle scan.";
        exit;
    }

    // Prepare the command to execute the appropriate Bash script
    $command = '';
    $command2 = '';
    
    if ($target_type === 'endpoint') {
        $command = escapeshellcmd("sudo /var/www/html/ShadowStrike/Scripts/endpoint_scan.sh $target_ip $scan_type $zombie_ip");
       
    } elseif ($target_type === 'webapp') {
        $command = escapeshellcmd("sudo /var/www/html/ShadowStrike/Scripts/web_scan.sh $web_url $scan_type");
    } elseif ($target_type === 'all_network') {
        // Command for scanning all devices on the network
        $command = escapeshellcmd("sudo /var/www/html/ShadowStrike/Scripts/all_network_scan.sh $scan_type");
    } else {
        echo "Invalid target type. Only 'endpoint', 'webapp', or 'all_network' are supported.";
        exit;
    }

    // Execute the command and capture the output
    $output = shell_exec($command);
    $error = shell_exec("echo $?"); // Capture the exit status

    // Log output and errors for debugging
    $log_file = '/var/www/html/ShadowStrike/Logs/scan_execution.log';
    $log_entry = "Command: $command\nOutput:\n$output\nError Code: $error\n\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);

    // Redirect to result page with timestamp to view the latest scan results
    if ($target_type === 'endpoint') {
        header("Location: result_endpoint.php?timestamp=" . urlencode(date("Ymd_His")));
    } elseif ($target_type === 'webapp') {
        header("Location: result_web.php?timestamp=" . urlencode(date("Ymd_His")));
    } elseif ($target_type === 'all_network') {
        header("Location: result_all_network.php?timestamp=" . urlencode(date("Ymd_His")));
    }
    exit();
} else {
    echo "Invalid request method.";
}
?>


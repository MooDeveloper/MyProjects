<?php
$timestamp = isset($_GET['timestamp']) ? $_GET['timestamp'] : '';

if (isset($_GET['ip']) && isset($_GET['type'])) {
    $ip = escapeshellarg($_GET['ip']); // Escape the IP address for shell usage
    $scanType = escapeshellarg($_GET['type']); // Escape the scan type

    // Prepare a filename for the output
    $outputFile = '/var/www/html/ShadowStrike/results/All/scan_output_' . time() . '.log';

    // Prepare the shell command
    $command = "sudo /var/www/html/ShadowStrike/Scripts/endpoint_scan.sh $ip $scanType > $outputFile 2>&1";

    // Execute the command and capture the exit code
    exec($command, $output, $returnVar);

    if ($returnVar === 0) {
        // Check if the output file was created
        if (file_exists($outputFile)) {
            // Redirect to the result page with the timestamp
            header("Location: result_endpoint.php?timestamp=" . urlencode(date("Ymd_His")));
            exit; // Ensure no further code is executed after the redirect
        } else {
            echo "Error: Scan completed, but no output file was created.";
        }
    } else {
        // Display an error message if the command fails
        echo "Error executing scan. Please check the command and try again.<br>";
        echo "Command: " . htmlspecialchars($command) . "<br>";
        echo "Error Output:<br><pre>" . implode("\n", $output) . "</pre>";
    }
} else {
    echo "Invalid parameters.";
}
?>

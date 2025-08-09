<?php
// Define the path to the endpoint scan files
$output_base_dir = "/var/www/html/ShadowStrike/results/Web/";

// Execute the Bash commands to find the latest directory
$latest_dir = trim(shell_exec("ls -td \"${output_base_dir}\"*/ 2>/dev/null | head -n 1"));

// Check if the latest directory exists
if (!$latest_dir) {
    echo "No directory found in $output_base_dir.";
    exit(1);
}

// Define the Web_Scan.php file path
$web_scan_file = $latest_dir . "Web_Scan.php";
if (!file_exists($web_scan_file)) {
    echo "Error: Web_Scan.php not found in $latest_dir.";
    exit(1);
}

$latest_file = $web_scan_file;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Scan Results</title>
</head>
<body>

    <?php
    if ($latest_file) {
        // Include the contents of the latest Web_Scan.php file
        include($latest_file);
    } else {
        echo "<p>No scan results found.</p>";
    }
    ?>
</body>
</html>


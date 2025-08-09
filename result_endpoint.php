<?php
// Define the base path where timestamped folders are stored
$base_results_path = '/var/www/html/ShadowStrike/results/Endpoint/';

// Function to get the latest folder by timestamp
function get_latest_folder($base_dir) {
    $folders = glob($base_dir . '/*', GLOB_ONLYDIR); // Get all directories in the base path
    if ($folders) {
        // Sort folders by modified time (newest first)
        usort($folders, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        return $folders[0]; // Return the latest folder
    }
    return false; // Return false if no folders found
}

// Get the latest folder by timestamp
$latest_folder = get_latest_folder($base_results_path);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Results and Weakness Analysis with AI</title>
</head>
<body>

<?php
if ($latest_folder) {
    // Define the path to the 'all_endpoint.php' file inside the latest folder
    $latest_file = $latest_folder . '/all_endpoint.php';

    if (file_exists($latest_file)) {
        // Include the contents of the latest 'all_endpoint.php' file
        include($latest_file);

        // Load scan result content
        $scan_result = file_get_contents($latest_file);

        // Assuming you have set up the necessary authentication and API request code:
        $gemini_api_url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . getenv('GEMINI_API_KEY'); // API key in environment variable

        $response = send_to_gemini_api($gemini_api_url, $scan_result); // Send scan to API
        $ai_analysis = json_decode($response, true); // Parse AI response

        if ($ai_analysis && isset($ai_analysis['weaknesses'])) {
            echo "<h2>Weaknesses detected by AI</h2>";
            echo "<ul>";
            foreach ($ai_analysis['weaknesses'] as $weakness) {
                echo "<li>" . htmlspecialchars($weakness) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No weaknesses detected by the AI or invalid response.</p>";
        }
    } else {
        echo "<p>'all_endpoint.php' file not found in the latest folder.</p>";
    }
} else {
    echo "<p>No scan result folders found.</p>";
}

// Function to send scan data to Gemini API (pseudo-code)
function send_to_gemini_api($url, $scan_data) {
    // Example using cURL to send POST request to Gemini API
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['scan_data' => $scan_data]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: ' . 'Bearer ' . getenv('GEMINI_API_KEY') // API key from environment
    ]);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        // Error handling for cURL
        echo "<p>Error sending request to AI API: " . curl_error($ch) . "</p>";
    }
    curl_close($ch);

    return $response;
}

?>
</body>
</html>


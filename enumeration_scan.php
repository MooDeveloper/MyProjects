<?php
session_start(); // Start the session



// Secure session settings
ini_set('session.cookie_httponly', 1); // Prevent JavaScript from accessing the session cookie
ini_set('session.use_strict_mode', 1); // Use strict session mode to prevent hijacking

// Validate the user's login status
if (!isset($_SESSION['user_name'])) {
    header("Location: index.php"); // Redirect to login page if not logged in
    exit;
}

// Prevent XSS by sanitizing session variables
$user = htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offensive Security - Enumeration & Scanning</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Style for loading spinner */
        .loading {
            display: none;
            text-align: center;
            font-size: 24px;
            color: #FF0000;
            margin-top: 20px;
        }
        .loading .spinner {
            border: 8px solid rgba(0, 0, 0, 0.1);
            border-left: 8px solid #FF4500;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        #target_type {
            width: 400px;
            padding: 10px;
            border: none;
            border-radius: 8px;
            background-color: #ff3300;
            color: #000;
            font-size: 14px;
            appearance: none;
            cursor: pointer;
            position: relative;
        }

        #target_type::after {
            content: "▼";
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #FFF000;
        }

        #target_type option {
            background-color: #000;
            color: #fff;
        }

        #target_type option:hover {
            background-color: #000;
        }

        #scan_type {
            width: 400px;
            padding: 10px;
            border: none;
            border-radius: 8px;
            background-color: #ff3300;
            color: #000;
            font-size: 14px;
            appearance: none;
            cursor: pointer;
            position: relative;
        }

        #scan_type::after {
            content: "▼";
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #000;
        }

        #scan_type option {
            background-color: #000;
            color: #fff;
        }

        #scan_type option:hover {
            background-color: #000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Enumeration & Scanning</h2>
        <form id="scan-form" action="process.php" method="POST">
            <label for="target_type">Choose a target type:</label>
            <select name="target_type" id="target_type" required>
                <option value="endpoint">Endpoint</option>
                <option value="webapp">Web Application</option>
                <option value="all_network">All Network</option> <!-- Added new option -->
            </select>
            
            <div id="scan-options">
                <label for="scan_type">Type of Scan:</label>
                <select name="scan_type" id="scan_type">
                    <option value="intense">1. Intense Scan</option>
                    <option value="intense_udp">2. Intense UDP Scan</option>
                    <option value="intense_tcp_all">3. Intense TCP All Ports Scan</option>
                    <option value="no_ping">4. Intense Scan (No Ping)</option>
                    <option value="ping">5. Ping Scan</option>
                    <option value="quick">6. Quick Scan</option>
                    <option value="quick_plus">7. Quick Scan Plus</option>
                    <option value="quick_traceroute">8. Quick Traceroute</option>
                    <option value="regular">9. Regular Scan</option>
                    <option value="slow_comprehensive">10. Slow Comprehensive Scan</option>
                    <option value="idle">11. Idle Scan (Requires Zombie IP)</option>
                </select>

                <div id="zombie-ip" style="display:none;">
                    <label for="zombie_ip">Enter Zombie IP:</label>
                    <input type="text" name="zombie_ip" id="zombie_ip">
                </div>
            </div>

            <div id="web-url" style="display:none;">
                <label for="web_url">Enter Web Application URL:</label>
                <input type="text" name="web_url" id="web_url" placeholder="http://example.com">
            </div>

            <div id="target-ip-container">
                <label for="target_ip">Enter Target IP:</label>
                <input type="text" name="target_ip" id="target_ip" required>
            </div>

            <button type="submit">Start Scan</button>
        </form>
        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Scanning start...</p>
        </div>
    </div>

    <script>
        document.getElementById('target_type').addEventListener('change', function () {
            const scanOptions = document.getElementById('scan-options');
            const webUrl = document.getElementById('web-url');
            const targetIpContainer = document.getElementById('target-ip-container');
            if (this.value === 'endpoint') {
                scanOptions.style.display = 'block';
                webUrl.style.display = 'none';
                targetIpContainer.style.display = 'block';
                document.getElementById('target_ip').required = true;
                document.getElementById('web_url').required = false;
            } else if (this.value === 'webapp') {
                scanOptions.style.display = 'none';
                webUrl.style.display = 'block';
                targetIpContainer.style.display = 'none';
                document.getElementById('target_ip').required = false;
                document.getElementById('web_url').required = true;
            } else if (this.value === 'all_network') { 
                scanOptions.style.display = 'block';
                webUrl.style.display = 'none';
                targetIpContainer.style.display = 'none'; 
                document.getElementById('target_ip').required = false; 
                document.getElementById('web_url').required = false;
            }
        });

        document.getElementById('scan_type').addEventListener('change', function () {
            const zombieIp = document.getElementById('zombie-ip');
            if (this.value === 'idle') {
                zombieIp.style.display = 'block';
            } else {
                zombieIp.style.display = 'none';
            }
        });

        window.onload = function() {
            const targetType = document.getElementById('target_type').value;
            const scanOptions = document.getElementById('scan-options');
            const webUrl = document.getElementById('web-url');
            const targetIpContainer = document.getElementById('target-ip-container');
            if (targetType === 'endpoint') {
                scanOptions.style.display = 'block';
                webUrl.style.display = 'none';
                targetIpContainer.style.display = 'block';
                document.getElementById('target_ip').required = true;
                document.getElementById('web_url').required = false;
            } else if (targetType === 'webapp') {
                scanOptions.style.display = 'none';
                webUrl.style.display = 'block';
                targetIpContainer.style.display = 'none';
                document.getElementById('target_ip').required = false;
                document.getElementById('web_url').required = true;
            } else if (targetType === 'all_network') {
                scanOptions.style.display = 'block';
                webUrl.style.display = 'none';
                targetIpContainer.style.display = 'none';
                document.getElementById('target_ip').required = false;
                document.getElementById('web_url').required = false;
            }
        };

        document.getElementById('scan-form').addEventListener('submit', function () {
            document.getElementById('loading').style.display = 'block';
        });
    </script>
</body>
</html>


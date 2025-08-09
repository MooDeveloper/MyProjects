#!/bin/bash

# Check if the user provided a domain
if [ -z "$1" ]; then
    echo "Usage: $0 <domain>"
    exit 1
fi

DOMAIN=$1
DATE=$(date +"%Y-%m-%d_%H-%M-%S")
OUTPUT_DIR="/var/www/html/ShadowStrike/results/Web/${DOMAIN}_${DATE}"
IMPORTANT_RESULTS_FILE="${OUTPUT_DIR}/Web_Scan.php"
TXT_RESULTS_FILE="${OUTPUT_DIR}/Web_Scan.txt"

# Create output directory if it doesn't exist
mkdir -p "$OUTPUT_DIR"
sudo chmod 777 $OUTPUT_DIR

# Helper function to append data to both the PHP and TXT files
append_result() {
    local section_title="$1"
    local content="$2"
    echo "<h2>${section_title}</h2><pre>${content}</pre>" >> "$IMPORTANT_RESULTS_FILE"
    echo -e "### ${section_title} ###\n${content}\n" >> "$TXT_RESULTS_FILE"
}

# Initialize the PHP results file with HTML header
cat <<EOF > "$IMPORTANT_RESULTS_FILE"
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron&display=swap" rel="stylesheet">
    <style>
        body { 
            background-color: #000000; color: #FF3333; font-family: 'Orbitron', sans-serif;
            margin: 0; padding: 20px; text-align: center;
        }
        h1, h2 { 
            color: #FF3333; text-shadow: 0 0 15px #FF3333, 0 0 30px #FF0000;
            margin-bottom: 15px; text-transform: uppercase;
        }
        pre { 
            background: rgba(255, 0, 0, 0.05); color: #FF6666; border: 2px solid #FF3333;
            padding: 15px; border-radius: 8px; margin: 10px auto; text-align: left;
            box-shadow: 0 0 30px rgba(255, 0, 0, 0.3);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            width: 90%; max-width: 800px;
        }
        pre:hover {
            transform: scale(1.03); box-shadow: 0 0 40px rgba(255, 0, 0, 0.5);
        }
        button { 
            background-color: #990000; color: #FFFFFF; border: 1px solid #FF3333;
            padding: 10px 25px; border-radius: 8px; cursor: pointer; margin-top: 20px;
            transition: all 0.3s ease-in-out;
        }
        button:hover {
            background-color: #FF3333; border-color: #FF6666; box-shadow: 0 0 20px #FF3333;
        }
    </style>
    <title>Scan Results for $DOMAIN</title>
</head>
<body>
<h1>Attack Surface Analysis for $DOMAIN</h1>
EOF

# 1. Web Server Info with Version
server_info=$(curl -sI "$DOMAIN" 2>/dev/null | grep -i "Server:")
append_result "Web Server Info with Version" "${server_info:-No server info found}"

# 2. Subdomains and IP Addresses
subdomains=$(dmitry -s "$DOMAIN" | sort | uniq | sed 's/^/ /')
append_result "Subdomains and IP Addresses" "$subdomains"

# Additional subdomain scanning with subfinder and amass
#subfinder_results=$(subfinder -d "$DOMAIN" -silent)
#amass_results=$(amass enum -d "$DOMAIN")
#append_result "Additional Subdomains (subfinder)" "$subfinder_results"
#append_result "Additional Subdomains (amass)" "$amass_results"

# 3. WHOIS Information
whois_info=$(whois "$DOMAIN")
append_result "WHOIS Information" "$whois_info"

# 4. DNS and Email Servers (MX Records)
mx_records=$(dig MX "$DOMAIN" +short)
append_result "MX Records" "${mx_records:-No Email Servers found}"

# 5. Emails found using theHarvester
email_addresses=$(theHarvester -d "$DOMAIN" -b all | grep -Eo '[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}' | sort -u)
append_result "Emails found" "${email_addresses:-No emails found}"

# 6. Vulnerability Scanning with Nikto and WhatWeb
#nikto_scan=$(nikto -h "$DOMAIN")
#append_result "Nikto Vulnerability Scan" "$nikto_scan"
#whatweb_scan=$(whatweb "$DOMAIN")
#append_result "WhatWeb Scan" "$whatweb_scan"

# 7. Open Ports and Service Version Detection
open_ports=$(nmap -sV "$DOMAIN" | grep -E "^[0-9]+/tcp")
append_result "Open Ports and Services" "$open_ports"

# 8. SSL Certificate Details
ssl_info=$(echo | openssl s_client -connect "$DOMAIN":443 2>/dev/null | openssl x509 -text)
append_result "SSL Certificate Information" "${ssl_info:-No SSL certificate found}"

# 9. Allowed HTTP Methods
http_methods=$(curl -s -X OPTIONS "$DOMAIN" -i | grep -i "allow:" | sed 's/Allow: //')
append_result "Allowed HTTP Methods" "${http_methods:-No methods allowed}"

# 10. SSL Labs Scan
ssl_labs_result=$(ssllabs-scan --quiet "$DOMAIN")
append_result "SSL Labs Security Scan" "$ssl_labs_result"

# 11. Technology Analysis with Wappalyzer
wappalyzer_result=$(wappalyzer "$DOMAIN")
append_result "Technology Analysis (Wappalyzer)" "${wappalyzer_result:-No technologies detected}"

# 12. Content Discovery with Gobuster
gobuster_scan=$(gobuster dir -u "$DOMAIN" -w /usr/share/wordlists/dirb/common.txt -q)
append_result "Content Discovery (Gobuster)" "${gobuster_scan:-No hidden content found}"

# 13. HTTP Header Analysis
headers=$(curl -sI "$DOMAIN")
append_result "HTTP Headers" "$headers"

# 14. Content Security Policy (CSP) Inspection
csp_info=$(curl -s -I "$DOMAIN" | grep -i "Content-Security-Policy")
append_result "Content Security Policy" "${csp_info:-No CSP found}"

# 15. SSL Expiration Date Check
ssl_expiration=$(echo | openssl s_client -servername "$DOMAIN" -connect "$DOMAIN":443 2>/dev/null | openssl x509 -noout -dates | grep -i "notAfter=")
append_result "SSL Certificate Expiration Date" "${ssl_expiration:-No SSL certificate found}"

# Close the HTML tags in the PHP file
cat <<EOF >> "$IMPORTANT_RESULTS_FILE"
<button onclick="window.location.href='.'">Return to Scan</button>
</body>
</html>
EOF

# List results and display permissions
ls -l "$OUTPUT_DIR"
echo "Scan complete for $DOMAIN. Results saved in $OUTPUT_DIR."


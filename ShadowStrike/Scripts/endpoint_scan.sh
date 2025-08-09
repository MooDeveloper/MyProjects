#!/bin/bash 

# Function to perform the scan
perform_scan() {
    local target_ip=$1
    local scan_type=$2
    local zombie_ip=$3
    local timestamp=$(date +"%Y%m%d_%H%M%S")
    local output_dir="/var/www/html/ShadowStrike/results/Endpoint/${timestamp}"  # Updated output directory path

    
    # Define output file for topology with PHP extension
    local output_file="${output_dir}/all_endpoint.php"  # Updated PHP output file path
    local txt_file="${output_dir}/all_endpoint.txt"  # New TXT output file
    local xml_file="${output_dir}/nmap_output.xml"  # Updated XML output file path
    local traceroute_file="${output_dir}/traceroute_output.txt"  # Updated Traceroute output file path
   

    # Ensure output directory exists
    mkdir -p "$output_dir"
    sudo chmod 777 $output_dir

    case $scan_type in
        intense)
            nmap_command="sudo nmap -T4 -A -v -oA ${output_dir}/nmap_output_$timestamp $target_ip"
            ;;
        intense_udp)
            nmap_command="sudo nmap -sS -sU -T4 -A -v -oA ${output_dir}/nmap_output_$timestamp $target_ip"
            ;;
        intense_tcp_all)
            nmap_command="sudo nmap -p 1-65535 -T4 -A -v -oA ${output_dir}/nmap_output_$timestamp $target_ip"
            ;;
        no_ping)
            nmap_command="sudo nmap -T4 -A -v -Pn -oA ${output_dir}/nmap_output_$timestamp $target_ip"
            ;;
        ping)
            nmap_command="sudo nmap -sn -oA ${output_dir}/nmap_output_$timestamp $target_ip"
            ;;
        quick)
            nmap_command="sudo nmap -T4 -F -oA ${output_dir}/nmap_output_$timestamp $target_ip"
            ;;
        quick_plus)
            nmap_command="sudo nmap -sV -T4 -O -F --version-light -oA ${output_dir}/nmap_output_$timestamp $target_ip"
            ;;
        quick_traceroute)
            nmap_command="sudo nmap -sn --traceroute -oA ${output_dir}/nmap_output_$timestamp $target_ip"
            ;;
        regular)
            nmap_command="sudo nmap -oA ${output_dir}/nmap_output_$timestamp $target_ip"
            ;;
        slow_comprehensive)
            nmap_command="sudo nmap -sS -sU -T4 -A -v -PE -PP -PS80,443 -PA3389 -PU40125 -PY -g 53 --script 'default or (discovery and safe)' -oA ${output_dir}/nmap_output_$timestamp $target_ip"
            ;;
        idle)
            if [ -z "$zombie_ip" ]; then
                echo "Zombie IP is required for idle scan."
                exit 1
            fi
            nmap_command="sudo nmap -sI $zombie_ip -p- -oA ${output_dir}/nmap_output_$timestamp $target_ip"
            ;;
        *)
            echo "Invalid scan type selected."
            exit 1
            ;;
    esac

    echo "Selected Scan Type: $scan_type"
    echo "Scanning $target_ip with the following command:"
    echo "$nmap_command" 
    

    # Perform the scan and save the XML results to the file
    eval $nmap_command
    eval $nmap_command >> $txt_file

   # Perform the traceroute command
    echo "Performing traceroute to $target_ip..."
    traceroute -n $target_ip > "$traceroute_file"

    # Extract hops and format them for the JavaScript
    hops=()
    while IFS= read -r line; do
        hop_number=$(echo "$line" | awk '{print $1}')
        hop_ip=$(echo "$line" | awk '{print $2}')
        hop_rtt=$(echo "$line" | awk '{print $4}') # Assuming RTT is in the fourth column
        if [[ $hop_ip =~ [0-9]+\.[0-9]+\.[0-9]+\.[0-9]+ ]]; then
            hops+=("{\"hop\": $hop_number, \"ip\": \"$hop_ip\", \"rtt\": \"$hop_rtt\"}")
        fi
    done < "$traceroute_file"

    # Create a JSON string for the hops array
    hops_json=$(printf ',%s' "${hops[@]}")
    hops_json="[${hops_json:1}]"

    # Extract and save topology from Nmap XML output
    if [ -f "${output_dir}/nmap_output_${timestamp}.xml" ]; then
        xsltproc /usr/share/nmap/nmap.xsl "${output_dir}/nmap_output_${timestamp}.xml" > /tmp/nmap_topology.html
        format_php_output "$output_file" "$target_ip" "$traceroute_file" "$hops_json"
        echo "Topology saved to $output_file"
    else
        echo "Nmap XML output file is missing."
        exit 1
    fi
}

# Function to format the PHP file with extracted details
format_php_output() {
    local output_file=$1
    local target_ip=$2
    local traceroute_file=$3
    local hops_json=$4

    # Determine the OS image based on scan results
    local os_image="unknown.png" # Default image
    local os_name="Unknown OS"
    if grep -iq "Linux" /tmp/nmap_topology.html; then
        os_image="linux.png"
        os_name="Linux"
    elif grep -iq "Windows" /tmp/nmap_topology.html; then
        os_image="windows.png"
        os_name="Windows"
    elif grep -iq "macOS" /tmp/nmap_topology.html; then
        os_image="macos.png"
        os_name="macOS"
    elif grep -iq "iOS" /tmp/nmap_topology.html; then
        os_image="ios.png"
        os_name="iOS"
    elif grep -iq "Android" /tmp/nmap_topology.html; then
        os_image="android.png"
        os_name="Android"
    fi

    # Extract MAC address and hostname from the XML file
    local xml_file="/var/www/html/ShadowStrike/results/Endpoint/nmap_output_${timestamp}.xml"

    # Extract MAC address from XML
    local mac_address=$(grep -oP '(?<=addrtype="mac" addr=")[^"]*' "$xml_file")
    
    # Extract hostname from XML
    local host_name=$(grep -oP '(?<=hostname name=").+?(?=")' "$xml_file")

    # If hostname or MAC is not found, use default values
    host_name="${host_name:-Unknown Host}"
    mac_address="${mac_address:-Unknown MAC}"

    # Print MAC and Hostname to debug
    echo "Extracted MAC: $mac_address"
    echo "Extracted Hostname: $host_name"

    # Create the PHP file with a polished HTML structure
    cat <<EOF > "$output_file"
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nmap Scan Results for $target_ip</title>
   <style>
        body {
            background-color: #0A0A0A;
            color: #FF0000;
            font-family: 'Orbitron', sans-serif;
            margin: 0;
            padding: 20px;
        }
        h1, h2 {
            text-shadow: 0 0 10px #FF0000;
        }
        .container {
            margin: 0 auto;
            padding: 20px;
            max-width: 800px;
            background-color: #1e1e1e;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ff0000;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #ff0000;
            color: black;
        }
        tr:nth-child(even) {
            background-color: #2c2c2c;
        }
        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        .os-image {
            max-width: 150px;
            height: auto;
            display: block;
            margin: 20px auto; /* Center the image */
        }
        #traceroute {
            height: 400px;
            margin-top: 20px;
            border: 1px solid #ff0000;
        }
        .button {
            background-color: #ff0000;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 5px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #d70000;
        }
         .loading {
            display: none;
            text-align: center;
            font-size: 24px;
            color: #FF0000; /* Red text for offensive security theme */
            margin-top: 20px;
        }
        .loading .spinner {
            border: 8px solid rgba(0, 0, 0, 0.1);
            border-left: 8px solid #FF4500; /* Orange border for loading spinner */
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
        pre {
            background: rgba(255, 0, 0, 0.1);
            border: 1px solid #FF0000;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
        button {
            background-color: #FF0000;
            color: #FFFFFF;
            border: none;
            padding: 10px 20px;
            text-transform: uppercase;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #CC0000;
        }
    </style>
<script src="https://d3js.org/d3.v6.min.js"></script>
<script>
    function drawTopology() {
        const data = $hops_json;

        const svg = d3.select('#traceroute')
            .append('svg')
            .attr('width', '100%')
            .attr('height', '100%')
            .style('background', 'linear-gradient(to bottom, #3a3a3a, #0d0d0d)'); // Background gradient

        const g = svg.append('g')
            .attr('transform', 'translate(100,100)');

        // Draw lines between nodes (circles)
        const lines = g.selectAll('line')
            .data(data.slice(1))  // Skip the first node
            .enter()
            .append('line')
            .attr('x1', (d, i) => i * 200)
            .attr('y1', 100)
            .attr('x2', (d, i) => (i + 1) * 200)
            .attr('y2', 100)
            .attr('stroke', 'white')
            .attr('stroke-width', 2);

        // Draw circles for each hop with a 3D-like gradient and shadow
        const circles = g.selectAll('circle')
            .data(data)
            .enter()
            .append('circle')
            .attr('cx', (d, i) => i * 200)  // Adjust spacing as needed
            .attr('cy', 100)
            .attr('r', 50)  // Larger circle radius
            .attr('fill', 'url(#gradient)')
            .attr('stroke', '#1a1a1a')
            .attr('stroke-width', 4)
            .style('filter', 'drop-shadow(5px 5px 10px rgba(0, 0, 0, 0.5))')  // 3D shadow effect
            .on('click', function(event, d) {
                // Display information about the node when clicked
                alert(`Node Info:\nIP: ${d.ip}\nOther Info: ${d.info}`);
            })
            .on('mouseover', function() {
                d3.select(this).attr('stroke', 'yellow').attr('stroke-width', 6);  // Highlight on hover
            })
            .on('mouseout', function() {
                d3.select(this).attr('stroke', '#1a1a1a').attr('stroke-width', 4);  // Revert on mouse out
            });

        // Define a gradient for a 3D effect
        svg.append("defs")
            .append("radialGradient")
            .attr("id", "gradient")
            .attr("cx", "50%")
            .attr("cy", "50%")
            .attr("r", "50%")
            .selectAll("stop")
            .data([
                { offset: "0%", color: "#FF0000" },  // Lighter color
                { offset: "100%", color: "#FF0000" }  // Darker color
            ])
            .enter().append("stop")
            .attr("offset", d => d.offset)
            .attr("stop-color", d => d.color);

        // Add IP address text inside the circles
        g.selectAll('text')
            .data(data)
            .enter()
            .append('text')
            .attr('x', (d, i) => i * 200)
            .attr('y', 105)
            .attr('text-anchor', 'middle')
            .attr('fill', 'white')  // Text color
            .attr('font-size', '16px')  // Larger font size
            .attr('font-family', 'Arial, sans-serif')
            .attr('font-weight', 'bold')  // Make text bold
            .style('text-shadow', '2px 2px 5px rgba(0, 0, 0, 0.7)')  // Text shadow for better visibility
            .text(d => d.ip);
    }

    window.onload = drawTopology;
</script>


</head>
<body>
    <div class="container">
        <h1>Nmap Scan Results for $target_ip</h1>

        <img src="Images/$os_image" alt="Operating System Image" class="os-image">

        <div class="info">
            <p style="text-align:center;"><strong>Target Name:</strong> $host_name</p>
            <p style="text-align:center;"><strong>IP Address:</strong> $target_ip</p>
            <p style="text-align:center;"><strong>Operating System:</strong> $os_name</p>
        </div>

        <h2>Scan Information</h2>
        <table>
            <tr>
                <th>Scan Type</th>
                <td>$scan_type</td>
            </tr>
            <tr>
                <th>Target IP</th>
                <td>$target_ip</td>
            </tr>
            <tr>
                <th>Date</th>
                <td>$(date)</td>
            </tr>
        </table>

        <!-- Insert Nmap topology here -->
        <div>
            $(cat /tmp/nmap_topology.html)
        </div>
        <h2>Traceroute Topology</h2>
        <div id="traceroute"></div>


        <!-- Traceroute Table -->
        <h2>Traceroute Topology</h2>
        <table id="traceroute-table">
            <thead>
                <tr>
                    <th>Hop</th>
                    <th>IP Address</th>
                    
                </tr>
            </thead>
            <tbody>
EOF

    # Populate the traceroute table with hops data
    for hop in "${hops[@]}"; do
        hop_number=$(echo "$hop" | jq -r '.hop')
        hop_ip=$(echo "$hop" | jq -r '.ip')
        hop_rtt=$(echo "$hop" | jq -r '.rtt')
        echo "                <tr>" >> "$output_file"
        echo "                    <td>$hop_number</td>" >> "$output_file"
        echo "                    <td>$hop_ip</td>" >> "$output_file"
        echo "                </tr>" >> "$output_file"
    done

    cat <<EOF >> "$output_file"
            </tbody>
        </table>

        <footer>
            <p>Scan performed by Network Scan Tool</p>
        </footer>
    </div>
    <button onclick="window.location.href='.'">Return to Scan</button>
</body>
</html>
EOF
}

# Read input from the PHP script
target_ip=$1
scan_type=$2
zombie_ip=$3

# Perform the scan
perform_scan "$target_ip" "$scan_type" "$zombie_ip"  
sudo bash  /var/www/html/ShadowStrike/Scripts/AI_E.sh 7277838270:AAFCc6hY1XJwJgkwt3w43NylRl4rayiNPCE 7049994970


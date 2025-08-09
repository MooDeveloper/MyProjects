<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nmap Scan Results for 192.168.1.67</title>
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
        const data = [{"hop": 1, "ip": "192.168.1.67", "rtt": "ms"}];

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
                alert();
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
        <h1>Nmap Scan Results for 192.168.1.67</h1>

        <img src="Images/linux.png" alt="Operating System Image" class="os-image">

        <div class="info">
            <p style="text-align:center;"><strong>Target Name:</strong> Unknown Host</p>
            <p style="text-align:center;"><strong>IP Address:</strong> 192.168.1.67</p>
            <p style="text-align:center;"><strong>Operating System:</strong> Linux</p>
        </div>

        <h2>Scan Information</h2>
        <table>
            <tr>
                <th>Scan Type</th>
                <td>intense</td>
            </tr>
            <tr>
                <th>Target IP</th>
                <td>192.168.1.67</td>
            </tr>
            <tr>
                <th>Date</th>
                <td>Tue Dec 17 17:14:56 EST 2024</td>
            </tr>
        </table>

        <!-- Insert Nmap topology here -->
        <div>
            <html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Nmap Scan Report</title>
<style>
               
                
                body {
                    font-family: 'Orbitron', sans-serif;
                    background-color: #0A0A0A;
                    color: #EAEAEA;
                    margin: 20px;
                }
                h1, h2 {
                    color: #FF0000;
                    text-shadow: 0px 0px 8px #FF0000;
                }
                h1 {
                    font-size: 2.5em;
                    text-align: center;
                }
                h2 {
                    font-size: 1.8em;
                    margin-top: 20px;
                    text-transform: uppercase;
                    border-bottom: 2px solid #FF0000;
                    padding-bottom: 10px;
                }
                table {
                    border-collapse: collapse;
                    width: 100%;
                    margin-bottom: 20px;
                    background-color: #1A1A1A;
                    box-shadow: 0px 2px 10px rgba(255, 0, 0, 0.6);
                }
                th, td {
                    border: 1px solid #FF0000;
                    padding: 12px;
                    text-align: left;
                    color: #FFDDDD;
                }
                th {
                    background-color: #FF0000;
                    color: white;
                    font-weight: bold;
                    text-transform: uppercase;
                }
                td {
                    color: #FFFFFF;
                }
                tr:nth-child(even) {
                    background-color: #1F1F1F;
                }
                button {
                    padding: 12px 18px;
                    background-color: #FF0000;
                    color: #0A0A0A;
                    border: none;
                    border-radius: 5px;
                    font-size: 1.2em;
                    cursor: pointer;
                    transition: background-color 0.3s ease, box-shadow 0.3s ease;
                    margin-top: 10px;
                    display: block;
                    margin: 0 auto;
                }
                button:hover {
                    background-color: #FF4444;
                    box-shadow: 0px 0px 12px #FF0000;
                }
                #topology {
                    margin-top: 30px;
                    width: 100%;
                    height: 600px;
                    background-color: #0D0D0D;
                    border: 1px solid #FF0000;
                }
                #detailed-info {
                    display: none;
                    margin-top: 20px;
                    padding: 15px;
                    background-color: #1A1A1A;
                    border: 1px solid #FF0000;
                    color: #FFDDDD;
                }
                ul {
                    list-style-type: none;
                    padding-left: 0;
                }
                ul li {
                    padding: 8px;
                    background-color: #1A1A1A;
                    border: 1px solid #FF0000;
                    margin-bottom: 8px;
                    color: #FFDDDD;
                    font-size: 1.1em;
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
            </style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.js"></script><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.css">
</head>
<body>
<h1>Nmap Scan Report</h1>
<h2>Security Recommendations</h2>
<p>Host 192.168.1.67 has open ports:</p>
<ul><li>Port 80 (Service: http)</li></ul>
<p><strong>Recommendation:</strong> Review firewall configurations for these ports.</p>
<h2>Operating System Details</h2>
<p>Detected OS: Linux 2.6.32 (Accuracy: 96%)</p>
<p>Detected OS: Linux 3.7 - 3.10 (Accuracy: 96%)</p>
<p>Detected OS: Linux 3.11 - 3.14 (Accuracy: 95%)</p>
<p>Detected OS: Linux 3.8 (Accuracy: 95%)</p>
<p>Detected OS: Linux 3.8 - 4.14 (Accuracy: 95%)</p>
<p>Detected OS: Linux 3.16 (Accuracy: 95%)</p>
<p>Detected OS: Linux 3.1 (Accuracy: 95%)</p>
<p>Detected OS: Linux 3.2 (Accuracy: 95%)</p>
<p>Detected OS: AXIS 210A or 211 Network Camera (Linux 2.6.17) (Accuracy: 95%)</p>
<p>Detected OS: Linux 3.19 (Accuracy: 94%)</p>
<h2>Port and Service Details</h2>
<table>
<tr>
<th>Port</th>
<th>Protocol</th>
<th>Service</th>
<th>Version</th>
<th>Extra Info</th>
</tr>
<tr>
<td>80</td>
<td>tcp</td>
<td>http</td>
<td>2.4.62</td>
<td>Apache httpd</td>
</tr>
</table>
<button onclick="showDetails()">Show More Details</button><div id="detailed-info"><table>
<tr><th>HTTP LS Output</th></tr>
<tr><td>
                                        No HTTP LS output available.
                                    </td></tr>
</table></div>
<h2>Open Ports Topology</h2>
<div id="topology"></div>
<script>
                var nodes = new vis.DataSet([]);
                var edges = new vis.DataSet([]);

                var hostCount = 0;

                
                    var hostAddress = "192.168.1.67"; 
                    var hostId = hostCount++;
                    nodes.add({id: hostId, label: hostAddress, shape: 'circle', title: hostAddress, x: 0, y: 0});

                    
                        var portId = "80"; 
                        var serviceName = "http"; 
                        var portDetails = "Port: " + portId + ", Service: " + serviceName;

                        var angle = Math.random() * 2 * Math.PI; 
                        var distance = 150; 
                        var portIdFull = hostId + "-" + portId; 

                        nodes.add({id: portIdFull, label: "Port " + portId, title: portDetails, shape: 'box', x: Math.cos(angle) * distance, y: Math.sin(angle) * distance});
                        edges.add({from: hostId, to: portIdFull, label: portDetails, arrows: 'to'});
                    

                var container = document.getElementById('topology');
                var data = {nodes: nodes, edges: edges};
                var options = {
                    layout: {
                        hierarchical: {
                            direction: "UD"
                        }
                    },
                    physics: {
                        enabled: true
                    },
                    nodes: {
                        color: {
                            background: '#FF0000',
                            border: '#FF0000'
                        }
                    },
                    edges: {
                        color: '#FF0000',
                        arrows: {to: true}
                    }
                };
                var network = new vis.Network(container, data, options);

                network.on("select", function(params) {
                    if (params.nodes.length > 0) {
                        var selectedNode = params.nodes[0];
                        var selectedNodeLabel = nodes.get(selectedNode).label;
                        alert("Selected Node: " + selectedNodeLabel);
                    }
                    if (params.edges.length > 0) {
                        var selectedEdge = params.edges[0];
                        var selectedEdgeLabel = edges.get(selectedEdge).label;
                        alert("Port Details: " + selectedEdgeLabel);
                    }
                });

                function showDetails() {
                    var detailsDiv = document.getElementById('detailed-info');
                    detailsDiv.style.display = 'block';
                }
            </script>
</body>
</html>
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
                <tr>
                    <td>1</td>
                    <td>192.168.1.67</td>
                </tr>
            </tbody>
        </table>

        <footer>
            <p>Scan performed by Network Scan Tool</p>
        </footer>
    </div>
    <button onclick="window.location.href='.'">Return to Scan</button>
</body>
</html>

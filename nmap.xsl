<?xml version="1.0" encoding="UTF-8"?>  
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

    <xsl:output method="html" encoding="UTF-8"/>

    <xsl:template match="/">
        <html>
        <head>
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
            <script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.js"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.css"/>
        </head>
        <body>
            <h1>Nmap Scan Report</h1>

            <h2>Security Recommendations</h2>
            <xsl:for-each select="nmaprun/host">
                <xsl:variable name="hostAddress" select="address/@addr"/>
                <xsl:variable name="openPorts" select="ports/port[state/@state='open']"/>
                
                <xsl:choose>
                    <xsl:when test="count($openPorts) > 0">
                        <p>Host <xsl:value-of select="$hostAddress"/> has open ports:</p>
                        <ul>
                            <xsl:for-each select="$openPorts">
                                <li>Port <xsl:value-of select="@portid"/> (Service: <xsl:value-of select="service/@name"/>)</li>
                            </xsl:for-each>
                        </ul>
                        <p><strong>Recommendation:</strong> Review firewall configurations for these ports.</p>
                    </xsl:when>
                    <xsl:otherwise>
                        <p>Host <xsl:value-of select="$hostAddress"/> has no open ports. Ensure that the host is correctly configured.</p>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>

            <h2>Operating System Details</h2>
            <xsl:for-each select="nmaprun/host/os/osmatch">
                <p>Detected OS: <xsl:value-of select="@name"/> (Accuracy: <xsl:value-of select="@accuracy"/>%)</p>
            </xsl:for-each>

            <h2>Port and Service Details</h2>
            <table>
                <tr>
                    <th>Port</th>
                    <th>Protocol</th>
                    <th>Service</th>
                    <th>Version</th>
                    <th>Extra Info</th>
                </tr>
                <xsl:for-each select="nmaprun/host/ports/port">
                    <tr>
                        <td><xsl:value-of select="@portid"/></td>
                        <td><xsl:value-of select="@protocol"/></td>
                        <td><xsl:value-of select="service/@name"/></td>
                        <td><xsl:value-of select="service/@version"/></td>
                        <td><xsl:value-of select="service/@product"/></td>
                    </tr>
                </xsl:for-each>
            </table>

            <button onclick="showDetails()">Show More Details</button>

            <div id="detailed-info">
                <xsl:for-each select="nmaprun/host">
                    <xsl:variable name="hostAddress" select="address/@addr"/>
                    <table>
                        <tr>
                            <th>HTTP LS Output</th>
                        </tr>
                        <tr>
                            <td>
                                <xsl:choose>
                                    <xsl:when test="ports/port/script[@id='http-ls']">
                                        <xsl:value-of select="ports/port/script[@id='http-ls']/@output"/>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        No HTTP LS output available.
                                    </xsl:otherwise>
                                </xsl:choose>
                            </td>
                        </tr>
                    </table>
                </xsl:for-each>
            </div>

            <h2>Open Ports Topology</h2>
            <div id="topology"></div>
            <script>
                var nodes = new vis.DataSet([]);
                var edges = new vis.DataSet([]);

                var hostCount = 0;

                <xsl:for-each select="nmaprun/host">
                    var hostAddress = "<xsl:value-of select="address/@addr"/>"; 
                    var hostId = hostCount++;
                    nodes.add({id: hostId, label: hostAddress, shape: 'circle', title: hostAddress, x: 0, y: 0});

                    <xsl:for-each select="ports/port[state/@state='open']">
                        var portId = "<xsl:value-of select="@portid"/>"; 
                        var serviceName = "<xsl:value-of select='service/@name'/>"; 
                        var portDetails = "Port: " + portId + ", Service: " + serviceName;

                        var angle = Math.random() * 2 * Math.PI; 
                        var distance = 150; 
                        var portIdFull = hostId + "-" + portId; 

                        nodes.add({id: portIdFull, label: "Port " + portId, title: portDetails, shape: 'box', x: Math.cos(angle) * distance, y: Math.sin(angle) * distance});
                        edges.add({from: hostId, to: portIdFull, label: portDetails, arrows: 'to'});
                    </xsl:for-each>
                </xsl:for-each>

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
    </xsl:template>

</xsl:stylesheet>

#!/bin/bash

# MySQL Credentials
DB_HOST="localhost"
DB_USER="phpmyadmin"
DB_PASS="tesla123"
DB_NAME="shadowstrike"

# Check if the scan type is provided
if [ $# -lt 1 ]; then
    echo "Usage: $0 <scan_type>"
    exit 1
fi

# Get the scan type from the arguments
SCAN_TYPE=$1

# Get the default gateway IP address
GATEWAY_IP=$(ip route | awk '/default/ {print $3}')

# Check if GATEWAY_IP was found
if [ -z "$GATEWAY_IP" ]; then
    echo "No default gateway found. Please check your network configuration."
    exit 1
fi

# Get the network interface associated with the gateway
INTERFACE=$(ip route | awk '/default/ {print $5}')

# Get the subnet mask for the default gateway interface
SUBNET_MASK=$(ip addr show $INTERFACE | awk '/inet / {print $2}' | cut -d'/' -f2)

# Calculate the network range based on the gateway IP and subnet mask
if [ -z "$SUBNET_MASK" ]; then
    echo "Unable to retrieve subnet mask for interface $INTERFACE."
    exit 1
fi

# Calculate the network range using the gateway IP and subnet mask
NETWORK_RANGE=$(ipcalc -n $GATEWAY_IP/$SUBNET_MASK | awk '/Network/ {print $2}')

# Prepare the Nmap command based on the scan type
case $SCAN_TYPE in
    "intense")
        NMAP_COMMAND="nmap -sS -sV -O $NETWORK_RANGE"
        ;;
    "intense_udp")
        NMAP_COMMAND="nmap -sS -sU -sV -O $NETWORK_RANGE"
        ;;
    "intense_tcp_all")
        NMAP_COMMAND="nmap -p- -sS -sV -O $NETWORK_RANGE"
        ;;
    "no_ping")
        NMAP_COMMAND="nmap -Pn -sS -sV -O $NETWORK_RANGE"
        ;;
    "ping")
        NMAP_COMMAND="nmap -sn $NETWORK_RANGE"
        ;;
    "quick")
        NMAP_COMMAND="nmap -F $NETWORK_RANGE"
        ;;
    "quick_plus")
        NMAP_COMMAND="nmap -F -sV $NETWORK_RANGE"
        ;;
    "quick_traceroute")
        NMAP_COMMAND="nmap --traceroute -F $NETWORK_RANGE"
        ;;
    "regular")
        NMAP_COMMAND="nmap $NETWORK_RANGE"
        ;;
    "slow_comprehensive")
        NMAP_COMMAND="nmap -p- -A $NETWORK_RANGE"
        ;;
    "idle")
        echo "Idle scan requires a zombie IP address to be provided."
        exit 1
        ;;
    *)
        echo "Invalid scan type. Please choose a valid scan type."
        exit 1
        ;;
esac

# Execute the Nmap command and check for errors
echo "Executing command: $NMAP_COMMAND"
OUTPUT=$($NMAP_COMMAND 2>&1)
if [ $? -ne 0 ]; then
    echo "Error executing Nmap command: $OUTPUT"
    exit 1
fi

# Get the current date in a specific format
CURRENT_DATE=$(date '+%Y-%m-%d %H:%M:%S')

# Save the result in the MySQL database
MYSQL_QUERY="INSERT INTO all_network_results (scan_type, execution_time, output) VALUES ('$SCAN_TYPE', '$CURRENT_DATE', '$(echo "$OUTPUT" | sed 's/\\/\\\\/g; s/'\''/\\'\''/g')');"

# Execute the MySQL query
echo "Saving scan results to the database..."
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -D "$DB_NAME" -e "$MYSQL_QUERY"
if [ $? -ne 0 ]; then
    echo "Failed to save results to the database."
    exit 1
fi

echo "Scan results successfully saved to the database."


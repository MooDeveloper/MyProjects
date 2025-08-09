#!/bin/bash

# Define the log file for process killing actions
cd /var/www/html/ShadowStrike

LOG_FILE="/var/www/html/ShadowStrike/Logs/kill_spoof_log.txt"

# Create the log file if it doesn't exist (will not clear if already exists)
touch "$LOG_FILE"

# Clear the log file before writing to it (ensure fresh content)
> "$LOG_FILE"

# Log the start of the script
echo "[$(date)] Starting ARP spoofing cleanup..." >> "$LOG_FILE"

# Find all processes containing "start_arp_spoof.php" or "arp_spoof.py" (excluding the grep process itself)
process_list=$(ps aux | grep -E "start_arp_spoof.php|arp_spoof.py" | grep -v "grep" | awk '{print $2}')

# Check if any processes were found
if [ -z "$process_list" ]; then
    echo "[$(date)] No processes containing 'start_arp_spoof.php' or 'arp_spoof.py' found." >> "$LOG_FILE"
else
    echo "[$(date)] Killing the following processes:" >> "$LOG_FILE"
    echo "$process_list" >> "$LOG_FILE"
    
    # Loop through each process ID and attempt to kill it
    for pid in $process_list; do
        if kill -9 "$pid" 2>/dev/null; then
            echo "[$(date)] Successfully killed process with PID $pid" >> "$LOG_FILE"
        else
            echo "[$(date)] Failed to kill process with PID $pid" >> "$LOG_FILE"
        fi
    done
fi

# Log the end of the script
echo "ARP spoofing cleanup completed." >> "$LOG_FILE"


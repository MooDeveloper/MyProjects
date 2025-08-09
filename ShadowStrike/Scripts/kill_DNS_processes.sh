#!/bin/bash

# Define the log file for process killing actions
cd /var/www/html/ShadowStrike
LOG_FILE="/var/www/html/ShadowStrike/Logs/kill_DNS_log.txt"

# Ensure log file is writable
if ! touch "$LOG_FILE" 2>/dev/null; then
    echo "Error: Unable to write to $LOG_FILE. Exiting."
    exit 1
fi

# Clear the log file before writing to it (ensure fresh content)
> "$LOG_FILE"

# Log the start of the script
echo "[$(date)] Starting DNS spoofing cleanup..." >> "$LOG_FILE"
echo "[$(date)] Script executed by: $(whoami) on $(hostname)" >> "$LOG_FILE"

# Find all processes containing "dns_spoof.py" (excluding the grep process itself)
process_list=$(ps aux | grep -E "dns_spoof.py" | grep -v "grep" | awk '{print $2}')

# Check if any processes were found
if [ -z "$process_list" ]; then
    echo "[$(date)] No processes containing 'dns_spoof.py' found." >> "$LOG_FILE"
else
    echo "[$(date)] Killing the following processes:" >> "$LOG_FILE"
    echo "$process_list" >> "$LOG_FILE"
    
    # Kill each process
    for pid in $process_list; do
        if kill -9 "$pid" 2>/dev/null; then
            echo "[$(date)] Successfully killed process with PID $pid" >> "$LOG_FILE"
        else
            echo "[$(date)] Failed to kill process with PID $pid" >> "$LOG_FILE"
        fi
    done
    
    # Log summary
    killed_count=$(echo "$process_list" | wc -l)
    echo "[$(date)] Total processes killed: $killed_count" >> "$LOG_FILE"
fi

# Log the end of the script
echo "[$(date)] DNS spoofing cleanup completed." >> "$LOG_FILE"

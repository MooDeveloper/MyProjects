#!/bin/bash 

# Check if the correct number of arguments is provided
if [ "$#" -ne 2 ]; then
    echo "Usage: $0 <TELEGRAM_BOT_TOKEN> <TELEGRAM_CHAT_ID>"
    exit 1
fi

# Assign the arguments to variables
TELEGRAM_BOT_TOKEN="$1"
TELEGRAM_CHAT_ID="$2"

# Define the output base directory
OUTPUT_BASE_DIR="/var/www/html/ShadowStrike/results/Web/"

# Find the latest directory based on the date pattern only (without DOMAIN)
LATEST_DIR=$(ls -td "${OUTPUT_BASE_DIR}"*/ 2>/dev/null | head -n 1)

# Check if the latest directory exists
if [ -z "$LATEST_DIR" ]; then
    echo "No directory found in $OUTPUT_BASE_DIR."
    exit 1
fi

# Navigate to the latest directory
cd "$LATEST_DIR" || { echo "Error: Unable to enter directory $LATEST_DIR"; exit 1; }

# Check if the Web_Scan.txt file exists
WEB_SCAN_FILE="Web_Scan.txt"
if [ ! -f "$WEB_SCAN_FILE" ]; then
    echo "Error: $WEB_SCAN_FILE not found in $LATEST_DIR."
    exit 1
fi

# Check if tgpt is installed
if ! command -v tgpt &> /dev/null; then
    echo "Error: tgpt is not installed. Please install it to proceed."
    exit 1
fi

# Determine if file splitting is necessary
FILE_SIZE=$(wc -c < "$WEB_SCAN_FILE")
CHUNK_DIR=""

if (( FILE_SIZE > 4000 )); then
    # Split file into chunks of less than 4000 characters
    split_file() {
        local file="$1"
        local chunk_size=4000
        local split_dir=$(mktemp -d)
        split -C "$chunk_size" "$file" "$split_dir/chunk_"
        echo "$split_dir"
    }
    CHUNK_DIR=$(split_file "$WEB_SCAN_FILE")
else
    CHUNK_DIR=$(mktemp -d)
    cp "$WEB_SCAN_FILE" "$CHUNK_DIR/chunk_0"
fi

# Process each chunk using tgpt
process_chunks() {
    local chunk_dir="$1"
    local query="$2"
    local output=""

    for chunk in "$chunk_dir"/*; do
        chunk_output=$(cat "$chunk" | tgpt "$query")
        output="$output$chunk_output\n"
    done

    rm -rf "$chunk_dir"  # Cleanup temporary directory
    
    echo -e "$output"
}

# Process chunks
REPORT=$(process_chunks "$CHUNK_DIR" "Generate a detailed human-readable report from these web scan results containing all scan info.")
ATTACK_COMMANDS=$(process_chunks "$CHUNK_DIR" "Based on these web scan results, generate commands to test possible attacks for open vulnerabilities and misconfigurations.")
SOLUTION_VULNERABILITIES=$(process_chunks "$CHUNK_DIR" "Provide solutions and preventive measures to resolve vulnerabilities in these web scan results.")

# Validate tgpt output
if [ -z "$REPORT" ]; then
    echo "Error: Failed to generate report using tgpt."
    exit 1
fi
if [ -z "$ATTACK_COMMANDS" ]; then
    echo "Error: Failed to generate attack commands using tgpt."
    exit 1
fi
if [ -z "$SOLUTION_VULNERABILITIES" ]; then
    echo "Error: Failed to generate solutions using tgpt."
    exit 1
fi

# Write content to separate text files
REPORT_FILE="report.txt"
ATTACK_COMMANDS_FILE="attack_commands.txt"
SOLUTION_FILE="solutions.txt"

echo -e "$REPORT" > "$REPORT_FILE"
echo -e "$ATTACK_COMMANDS" > "$ATTACK_COMMANDS_FILE"
echo -e "$SOLUTION_VULNERABILITIES" > "$SOLUTION_FILE"

# Function to send files to Telegram
send_to_telegram() {
    local file_path="$1"
    local response=$(curl -s -X POST "https://api.telegram.org/bot$TELEGRAM_BOT_TOKEN/sendDocument" \
        -F chat_id="$TELEGRAM_CHAT_ID" \
        -F document=@"$file_path")

    if echo "$response" | grep -q '"ok":true'; then
        echo "Sent $file_path to Telegram successfully."
    else
        echo "Failed to send $file_path to Telegram."
        echo "Response: $response"
    fi
}

# Send the individual result files to Telegram
send_to_telegram "$REPORT_FILE"
send_to_telegram "$ATTACK_COMMANDS_FILE"
send_to_telegram "$SOLUTION_FILE"

# Output final success message
echo "All reports processed and sent to Telegram."


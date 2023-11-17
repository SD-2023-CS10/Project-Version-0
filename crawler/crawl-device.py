'''
Network Crawling
'''
from nmap3 import Nmap
import json
import ipaddress
import socket
import platform
import requests
import netifaces
from scapy.all import ARP, Ether, srp
from pysnmp.hlapi import * # pip install pysnmp

def get_cur_device_info():
    device_hostname = socket.gethostname()
    device_ip = socket.gethostbyname(device_hostname)
    device_type = platform.machine()
    os_name = platform.system()
    os_version = platform.version()
    return device_hostname, device_ip, device_type, os_name, os_version

def get_default_gateway():
    gateways = netifaces.gateways()
    if 'default' in gateways and netifaces.AF_INET in gateways['default']:
        return gateways['default'][netifaces.AF_INET][0]
    return None

def get_network_subnet(gateway_ip):
    if gateway_ip:
        gateway_network = ipaddress.ip_interface(f"{gateway_ip}/24")
        return str(gateway_network.network)
    return None    

def get_hosts_up(subnet):
    # Use ARP request packet to ping hosts
    arp = ARP(pdst=subnet)
    ether = Ether(dst="ff:ff:ff:ff:ff:ff")  # cover all IP range
    packet = ether/arp
    result = srp(packet, timeout=3, verbose=False)[0] # send packets out
    # Record hosts that respond to ARP requests
    hosts_up = [res[1].psrc for res in result]
    return hosts_up

def get_OS(nmap, cur_ip):
    scan_dict = nmap.nmap_os_detection(str(cur_ip))
    json_results = json.dumps(scan_dict, indent=4) # returns type string
    # Convert string-results into parsed object
    parsed_obj = json.loads(json_results) # returns a json-object
    try:
        stats = parsed_obj[str(cur_ip)]["osmatch"] if "osmatch" in parsed_obj[str(cur_ip)] else 0
    except:
        print("No known OS information.")
    return json_results, parsed_obj, stats
    
def get_hostname(cur_ip):
    # Perform a host discovery scan
    try:
        hostname, _, _ = socket.gethostbyaddr(str(cur_ip))
        return hostname
    except Exception as e:
        return f"Error during hostname scan: {e}"

def get_location(server_ip):
    access_key = 'eceb792fd161f563384fbf3a1733ceda'
    url = f"http://api.ipstack.com/{server_ip}?access_key={access_key}"
    response = requests.get(url)
    
    if response.status_code == 200:
        data = response.json()
        print(data)
        city = data.get("city")
        country = data.get("country_name")
        if city and country:
            return f"The server is located in {city}, {country}"
        else:
            return "Location information not found."
    else:
        return "Failed to retrieve location information."
    
def scan_running_devices():
    # Instantiate nmap object
    nmap = Nmap()
    
    # Crawl device running the script
    device_hostname, device_ip, device_type, os_name, os_version = get_cur_device_info()
    gateway_ip = get_default_gateway()
    server_name = get_hostname(gateway_ip)
    subnet = get_network_subnet(gateway_ip)
    up_hosts = get_hosts_up(subnet)
    location = get_location(gateway_ip)
    
    print(f"Device Hostname: {device_hostname}")
    print(f"Device IP Address: {device_ip}")
    print(f"Device Type: {device_type}")
    print(f"Operating System: {os_name} ")
    print(f"OS Version: {os_version}")
    print(f"Server Gateway IP: {gateway_ip}")
    print(f"Device Location: {location}")
    print(f"Network Subnet: {subnet}")
    
    if not server_name.startswith("Error"):
        print(f"The server name associated with {gateway_ip} is: {server_name}")
    else:
        print(f"No hostname found on gateway: {gateway_ip}")

    # Now crawl devices connected to the subnet
    if up_hosts:
        print(f"\nConnections to {subnet}:")
        for host in up_hosts:
            print("Started scanning " + str(host) + "...")
            print(f"Software IP: {host}")
            host = ipaddress.IPv4Address(host)
            cur_server_name = get_hostname(host)
            if not cur_server_name.startswith("Error"):
                print(f"Server Name: {cur_server_name}")
            else:
                print(f"No hostname found on IP: {host}")
            # Conduct OS detection scan
            json_results, parsed_obj, stats = get_OS(nmap, host)
            if stats != 0:
                os_name = stats[0]["name"] if "name" in stats[0] else "N/A"
                os_gen = stats[0]["osclass"]["osgen"] if "osgen" in stats[0]["osclass"] else "N/A"
                os_family = stats[0]["osclass"]["osfamily"] if "osfamily" in stats[0]["osclass"] else "N/A"
                
                print("OS Name: " + os_name)
                print("OS Gen: " + os_gen)
                print("OS Family: " + os_family)
                print(stats)
    else:
        print(f"No hosts found on subnet: {subnet}")
        
    return 0
        
    
if __name__ == "__main__":
    scan_running_devices()

    # dest_filename = "test_results.json" # "scan_results.json"
    # scan_device(nmap, start_ip, end_ip, dest_filename)
    
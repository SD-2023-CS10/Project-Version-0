'''
Network Crawling
'''
from nmap3 import Nmap
import json
import ipaddress
import requests
import netifaces
import subprocess
import re
import socket
from scapy.all import ARP, Ether, srp
from pysnmp.hlapi import * # pip install pysnmp
import threading
from threading import Thread
import ssl
import time

'''
ServiceThread class runs get_os_and_open_ports() and retrieves result with multithreading.
'''
class ServiceThread(Thread):
    def __init__(self, argument):
        Thread.__init__(self)
        self.argument = argument
        self.result = None
    def run(self):
        self.result = get_os_and_open_ports(self.argument)

'''
Function: get_default_gateway()
Args: None
Parses the netifaces.gateways() output to retrieve the default gateway IP.
Returns IP address if applicable, else None.
'''  
def get_default_gateway():
    gateways = netifaces.gateways()
    if 'default' in gateways and netifaces.AF_INET in gateways['default']: # check attributes exist
        return gateways['default'][netifaces.AF_INET][0]
    return None

'''
Function: get_network_subnet()
Args: gateway_ip
Gets CIDR /24 Notation of gateway IP
Returns gateway IP's subnet mask.
'''
def get_network_subnet(gateway_ip):
    if gateway_ip:
        gateway_network = ipaddress.ip_interface(f"{gateway_ip}/24")
        return str(gateway_network.network)
    return None    

'''
Function: get_hosts_up()
Args: subnet
Constructs and sends packet to get MAC addresses from connected subnet
Retuns list of hosts currently up and list of MAC addresses
'''
def get_hosts_up(subnet):
    macs_lst = []
    arp = ARP(pdst=subnet) # use ARP request packet to ping/communicate with hosts
    ether = Ether(dst="ff:ff:ff:ff:ff:ff")  # cover all IP range values
    packet = ether/arp
    result = srp(packet, timeout=3, verbose=False)[0] # send packets out
    for sent, received in result:
        mac = received.hwsrc # attribute holds parsed MAC address
        if mac != "":
            macs_lst.append(mac)
        else:
            macs_lst.append("N/A")
    # Record hosts that respond to ARP requests
    hosts_up = [res[1].psrc for res in result]
    return hosts_up, macs_lst

'''
Function: get_OS()
Args: nmap, cur_ip
Requests information from nmap library nmap_os_detection
Returns parsed json_results, parsed_obj, and stats objects if applicable, else None
'''
def get_OS(nmap, cur_ip):
    scan_dict = nmap.nmap_os_detection(str(cur_ip))
    json_results = json.dumps(scan_dict, indent=4) # returns type string
    parsed_obj = json.loads(json_results) # returns a json-object
    stats = []
    try: # based on .json output in stats
        stats = parsed_obj[str(cur_ip)]["osmatch"] if "osmatch" in parsed_obj[str(cur_ip)] else []
        return json_results, parsed_obj, stats
    except:
        print("No known OS information.")
    return None

'''
Function: parse_OS_output()
Args: stats
Delves into stats json object for values
Returns os_name, os_gen, os_family, device_type for given IP's stats object
'''
def parse_OS_output(stats):
    if stats != {} and stats != [] and isinstance(stats, list)==True:
        first_item = stats[0]
        if isinstance(first_item, dict) and "osclass" in first_item and "name" in first_item:
            os_name = stats[0]["name"] if "name" in stats[0] else "N/A"
            os_gen = stats[0]["osclass"]["osgen"] if "osgen" in stats[0]["osclass"] else "N/A"
            os_family = stats[0]["osclass"]["osfamily"] if "osfamily" in stats[0]["osclass"] else "N/A"
            device_type = stats[0]["osclass"]["type"] if "type" in stats[0]["osclass"] else "N/A"
            return os_name, os_gen, os_family, device_type
    return "N/A", "N/A", "N/A", "N/A"

'''
Function: get_hostname()
Args: cur_ip
Retrieves hostname of given IP
Returns hostname if applicable, else N/A
'''
def get_hostname(cur_ip):
    # Perform a host discovery scan
    try:
        hostname = str(socket.gethostbyaddr(cur_ip)[0])
        return hostname
    except Exception as e:
        hostname = "N/A"
        return f"Error during hostname scan: {e}"

'''
Function: get_public_ip()
Args: None
Pings website to get device's public IP
Returns public IP of device running program.
'''
def get_public_ip():
    response = requests.get('https://api64.ipify.org?format=json').json()
    public_ip = response["ip"]
    return public_ip

'''
Function: get_location()
Args: server_ip
Grabs city, region, and country_name after sending request
Returns device's location attributes
'''
def get_location(server_ip):
    response = requests.get(f'https://ipapi.co/{server_ip}/json/').json()
    city = response.get("city")
    region = response.get("region")
    country = response.get("country_name")
    return city, region, country

'''
Function: server_encryption_type()
Args: hostname
Connect through HTTPS on device or server 
Return the version of the encryption using ssl if applicable, else Error.
'''
def get_server_encryption_type(hostname):
    try:
        context = ssl.create_default_context()
        with context.wrap_socket(socket.socket(), server_hostname=hostname) as s:
            s.connect((hostname, 443))  # Port for HTTPS connections
            return s.version()
    except Exception as e:
        return f"Error: {str(e)}"

'''
Function: append_NA()
Args: ports, statuses, services, service_versions
Adds N/A to lists provided
Returns modified lists from input
'''
def append_NA(ports, protocols, statuses, services, service_versions):
    ports.append("N/A")
    protocols.append("N/A")
    statuses.append("N/A")
    services.append("N/A")
    service_versions.append("N/A")
    return ports, protocols, statuses, services, service_versions

'''
Function: get_os_and_open_ports()
Args: host IP
Performs subprocess nmap scan for TCP fingerprinting of an OS. 
Creates lists that collect attributes and 
'''
def get_os_and_open_ports(host):
    device_types = []; os_details = []; ports = []; protocols = []; statuses = []; services = []; service_versions = []
    try:
        print("\n" + host)
        result = subprocess.run(['nmap', '-O', '-T4', '-sV', '-v', host], capture_output=True, text=True, timeout=190)
        output = result.stdout
        print(output)
        os_info_pattern = r"OS details: (.+)"
        os_match = re.search(os_info_pattern, output)
        os_info = os_match.group(1) if os_match else "N/A"
        os_details.append(os_info)
        
        device_pattern = r"Device type: (.+)" # Search for keywords in output
        device_match = re.search(device_pattern, output)
        device_info = device_match.group(1) if device_match else "N/A"
        device_types.append(device_info)
        port_info_pattern = r"(\d+)/(\w+)\s+(\w+)\s+(.+?)\s*(?:\n|$)" # Parse nmap output to extract open ports and service versions
        port_matches = re.findall(port_info_pattern, output)
        if (len(port_matches) == 0):
            append_NA(ports, protocols, statuses, services, service_versions)
        for match in port_matches:
            port = match[0] if match[0] != [] and match[0] != "" else "N/A"
            ports.append(port)
            protocol = match[1] if match[1] != [] and match[1] != "" else "N/A"
            protocols.append(protocol)
            status = match[2] if match[2] != [] and match[2] != "" else "N/A"
            statuses.append(status)
            split_string = match[3].split()

            if len(split_string) >= 2:
                split_string = [elem for elem in split_string if elem.strip()]
                service = split_string[0]
                version = ' '.join(split_string[1:])
                print(f"Service: {service}, Version: {version}")
                services.append(service.strip()) if service.strip() != "" else "N/A"
                service_versions.append(version.strip()) if version.strip() != "" else service_versions.append("N/A")
                print(f"Port: {port}, Protocol: {protocol}, Status: {status}, Service: {service.strip()}, Version: {version.strip()}")
            else:
                services.append("N/A")
                service_versions.append("N/A")
    except subprocess.TimeoutExpired:
        print(f"Timeout occurred scanning {host}")
        device_types.append("N/A")
        os_details.append("N/A")
        append_NA(ports, protocols, statuses, services, service_versions)
    except Exception as e:
        print(f"Error retrieving OS information and open ports: {e}")
        device_types.append("N/A")
        os_details.append("N/A")
        append_NA(ports, protocols, statuses, services, service_versions)
    return device_types, os_details, ports, protocols, statuses, services, service_versions

'''
Main Program Driver
'''
if __name__ == "__main__":
    print("Started Crawler...")
    nmap = Nmap() # Instantiate nmap object
    # Ensure Internet Connectivity
    try:
        target_host = socket.gethostname()
        print("Target Hostname: " + target_host)
        gateway_ip = get_default_gateway()
        print(f"\nServer Gateway IP: {gateway_ip}")
        subnet = get_network_subnet(gateway_ip)
        print(f"Network Subnet: {subnet}")
        up_hosts, macs_lst = get_hosts_up(subnet)
        print(f"Devices: {up_hosts}")
        num_devices = len(up_hosts)
        print(f"Number of Connections: {num_devices}")
        public_ip = get_public_ip()
        city, region, country = get_location(public_ip)
        encryption = get_server_encryption_type(gateway_ip)
        if country != None: 
            print(f"Server Location: {city}, {region} in {country}")
        else:
            print("No server location found.")
        if not encryption.startswith("Error"):
            print(f"Encryption Type: {encryption}")
        else:
            encryption = "N/A"
            print("No server encryption type found.")
    except Exception as e:
        print("Check network connection to retrieve statistics.")
    
    # os_names, os_gens, os_families, device_types = fetch_host_stats(up_hosts) # scan device and software details
    # port_ids_lst, services_lst, service_products_lst, service_versions_lst = fetch_ports_stats(up_hosts) # scan ports on hosts

    device_types = []; os_lst = []; port_ids_lst = []; protocols_lst = []; status_lst = []; services_lst = []; services_versions_lst = []
    for host in up_hosts:
        start_time = time.time()
        device_type, os_details, ports, protocols, statuses, services, service_versions = get_os_and_open_ports(host)
        device_types.append(device_type)
        os_lst.append(os_details)
        port_ids_lst.append(ports)
        protocols_lst.append(protocols)
        status_lst.append(statuses)
        services_lst.append(services)
        services_versions_lst.append(service_versions)
        end_time = time.time()
        print(f"Executed in {end_time - start_time} seconds.")
    print(port_ids_lst); print(services_lst); print(device_types); print(os_lst); print(port_ids_lst)
    print(protocols_lst); print(status_lst); print(services_lst); print(services_versions_lst)
    print("\nSummary:"); 
    for i in range(len(up_hosts)):
        print("Host: " + up_hosts[i])
        print("MAC Address: " + macs_lst[i])
        print("Device Type: " + ", ".join(device_types[i]))
        print("Operating System Name: " + ", ".join(os_lst[i]))
        print("Port ID: " + ", ".join(port_ids_lst[i]))
        print("Protocol: " + ", ".join(protocols_lst[i]))
        print("Status: " + ", ".join(status_lst[i]))
        print("Service: " + ", ".join(services_lst[i]))
        print("Service Version: " + ", ".join(services_versions_lst[i]))
        print()
    
'''
Add DB <-> Crawler Python API
'''
from database.dbapi import DBAPI
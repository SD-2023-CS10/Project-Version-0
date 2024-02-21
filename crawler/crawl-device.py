'''
Network Crawling
To run from Project-Version-0: python3 crawler/crawl-device.py <username>
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
from pysnmp.hlapi import *
import ssl
import time
import sys
import os

'''
Function: get_default_gateway()
Args: N/A
Parses the netifaces.gateways() output to retrieve the default gateway IP.
Returns IP address if applicable, else N/A.
'''  
def get_default_gateway():
    gateways = netifaces.gateways()
    if 'default' in gateways and netifaces.AF_INET in gateways['default']: # check attributes exist
        return gateways['default'][netifaces.AF_INET][0]
    return "N/A"

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
    return "N/A"    

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
            mac_int = mac_to_int(mac)
            macs_lst.append(mac_int)
        else:
            macs_lst.append("N/A")
    # Record hosts that respond to ARP requests
    hosts_up = [res[1].psrc for res in result]
    return hosts_up, macs_lst

'''
Function: mac_to_int()
Args: mac_address
Converts MAC address to integer
Returns MAC address as an integer
'''
def mac_to_int(mac_address):
    # Remove colons or dashes from the MAC address
    cleaned_mac = ''.join(c for c in mac_address if c.isalnum())
    mac_integer = int(cleaned_mac, 16)
    return mac_integer

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
Args: N/A
Pings website to get device's public IP
Returns public IP of device running program.
'''
def get_public_ip():
    try:
        response = requests.get('https://api64.ipify.org?format=json', verify=True).json()
        public_ip = response["ip"]
    except Exception as e:
        print(f"Ensure administrator privileges and online connectivity. {e}")
        public_ip = "N/A"
    return public_ip

'''
Function: get_location()
Args: server_ip
Grabs city, region, and country_name after sending request
Returns device's location attributes
'''
def get_location(server_ip):
    try:
        response = requests.get(f'https://ipapi.co/{server_ip}/json/', verify=True).json()
        city = response.get("city")
        region = response.get("region")
        country = response.get("country_name")
    except Exception as e:
        print(f"Ensure administrator privileges and online connectivity. {e}")
        city = region = country = "N/A"
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
Function: split_os_details()
Args: os_details
Splits OS details into its corresponding name and version
Returns parsed name and version
'''
def split_os_details(os_details):
    # OS details is an re.Match object
    pattern = r"([a-zA-Z]+(?:\s+[a-zA-Z]+)*)\s*([\d. -]+)"
    
    if os_details:
        os_str = str(os_details.group(1))
        match = re.match(pattern, os_str)
        os_name = match.group(1).strip()
        os_version = match.group(2).strip()
        return os_name, os_version
    else:
        return "N/A", "N/A"

'''
Function: find_ports()
Args: nmap output, ports, protocols, statuses, services, service_versions
Creates regex pattern to find ports in nmap output
Returns: ports, protocols, statuses, services, service_versions
'''
def find_ports(output, ports, protocols, statuses, services, service_versions):
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
            print(f"Service: {service}\nService Version: {version}")
            services.append(service.strip()) if service.strip() != "" else "N/A"
            service_versions.append(version.strip()) if version.strip() != "" else service_versions.append("N/A")
            print(f"Port: {port}, Protocol: {protocol}, Status: {status}, Service: {service.strip()}, Version: {version.strip()}")
        else:
            services.append("N/A")
            service_versions.append("N/A")
    return ports, protocols, statuses, services, service_versions

'''
Function: get_os_and_open_ports()
Args: host IP
Performs subprocess nmap scan for TCP fingerprinting of an OS
Returns device_types, os_details, ports, protocols, statuses, services, service_versions
'''
def get_os_and_open_ports(host):
    device_types = []; os_versions = []; os_names = []; ports = []; protocols = []; statuses = []; services = []; service_versions = []
    try:
        print("\n" + host)
        result = subprocess.run(['nmap', '-O', '-T4', '-sV', '-v', host], capture_output=True, text=True, timeout=190) # Run subprocess scan of nmap
        output = result.stdout
        os_info_pattern = r"OS details: (.+)"
        os_match = re.search(os_info_pattern, output)
        os_name, os_version = split_os_details(os_match)
        os_names.append(os_name)
        os_versions.append(os_version)
        
        print(f"OS Name: {os_name}")
        print(f"OS Version: {os_version}")
        
        device_pattern = r"Device type: (.+)" # Search for keywords in output
        device_match = re.search(device_pattern, output)
        device_info = device_match.group(1) if device_match else "N/A"
        device_types.append(device_info)
        ports, protocols, statuses, services, service_versions = find_ports(output, ports, protocols, statuses, services, service_versions)
    except subprocess.TimeoutExpired:
        print(f"Timeout occurred scanning {host}")
        device_types.append("N/A")
        os_names.append("N/A")
        os_versions.append("N/A")
        append_NA(ports, protocols, statuses, services, service_versions)
    except Exception as e:
        print(f"Error retrieving OS information and open ports: {e}")
        device_types.append("N/A")
        os_names.append("N/A")
        os_versions.append("N/A")
        append_NA(ports, protocols, statuses, services, service_versions)
    return device_types, os_names, os_versions, ports, protocols, statuses, services, service_versions

'''
Function: get_server_name()
Args: host IP
Performs subprocess nslookup comand, given the crawled device on the network
Returns server name
'''
def get_server_name(host):
    result = subprocess.run(['nslookup', host], capture_output=True, text=True, timeout=190)
    if result.returncode == 0:
        output_lines = result.stdout.splitlines()
        # Search for the server name in the output
        for line in output_lines:
            if "Name:" in line:
                # Extract the server name using regex
                server_name = re.search(r'Name:\s+(.*)', line)
                if server_name:
                    return server_name.group(1).strip()  # Return the server name without extra spaces
        else:
            return "N/A"

'''
Function: device_stats()
Args: N/A
Performs system calls to provide the basic device information on the network
Returns target_host, gateway_ip, subnet, up_hosts, macs_lst, num_devices, city, region, country, encryption
'''
def device_stats():
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
        server_name = get_server_name(public_ip)
        encryption = get_server_encryption_type(gateway_ip)
        if country != "N/A": 
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
    return server_name, target_host, gateway_ip, subnet, up_hosts, macs_lst, num_devices, city, region, country, encryption

'''
Function: print_summary()
Args: up_hosts, macs_lst, device_types, os_list, port_ids_lst, protocols_lst, status_lst, services_lst, services_versions_lst
Prints lists to console
No return value
'''
def print_summary(city, region, country, up_hosts, macs_lst, device_types, os_names, os_versions, port_ids_lst, protocols_lst, status_lst, services_lst, services_versions_lst):
    print("\nSummary:")
    print(f"Location: {city}, {region}, {country}")
    for i in range(len(up_hosts)):
        print("Host: " + up_hosts[i])
        print("MAC Address: " + str(macs_lst[i]))
        print("Device Type: " + ", ".join(device_types[i]))
        print("Operating System Name: " + ", ".join(os_names[i]))
        print("Operating System Version: " + ", ".join(os_versions[i]))
        print("Port ID: " + ", ".join(port_ids_lst[i]))
        print("Protocol: " + ", ".join(protocols_lst[i]))
        print("Status: " + ", ".join(status_lst[i]))
        print("Service: " + ", ".join(services_lst[i]))
        print("Service Version: " + ", ".join(services_versions_lst[i]))
        print()

'''
Function: database_push()
Args: up_hosts, device_types, os_lst, city, region, country, encryption, gateway_ip, server_name
Pushes crawled data to remote AWS server
Returns no value
'''
def database_push(username, up_hosts, device_types, os_names, os_versions, city, region, country, encryption, gateway_ip, server_name, services_versions_lst):
    # Add Project parent dir to path
    current_dir = os.path.dirname(os.path.abspath(__file__))
    parent_dir = os.path.dirname(current_dir)
    sys.path.append(parent_dir)

    # Create an instance of DBAPI class 
    from database.dbapi import DBAPI
    database = DBAPI(username)

    with database as db:
        print("Pushing statistics to database...")
        try:
            location = f"{city}, {region}, {country}"
            print(f"Location is {location}")
            # Capture location ID in case we need it for the server ID entry
            lid = db.check_location_exists("On-Premise", location, encryption)
            location_id = -1
            if (lid is None):
                print("Location doesn't exist")
                location_id = db.create_locataion("On-Premise", location, encryption)
            else:
                print(f"Location exists as ID {lid}")
                location_id = lid
            print(location_id)
            # Check if server exists with DB API
            ip_obj = ipaddress.ip_address(gateway_ip)
            ip_int = int(ip_obj)
            sid = db.check_server_exists(server_name, ip_int, None)
            server_id = -1
            if (sid is None):
                server_id = db.create_server(server_name, ip_int, None, location_id)
            else:
                server_id = sid
                
            for i in range(len(up_hosts)):
                # Check if item exists with DB API
                dup = db.check_item_exist(name=up_hosts[i], type_=", ".join(device_types[i]), version=None,
                                 os=", ".join(os_names[i]), os_version=", ".join(os_versions[i]), 
                                 mac=int(macs_lst[i]), ports=", ".join(port_ids_lst[i]),
                                 protocols=", ".join(protocols_lst[i]), statuses=", ".join(status_lst[i]), 
                                 services=", ".join(services_lst[i]), services_versions=", ".join(services_versions_lst[i]), vender=None,
                                 auto_log_off_freq=None, server=server_id, ephi=None,
                                 ephi_encrypted=None, ephi_encr_method=None,
                                 ephi_encr_tested=None, interfaces_with=None,
                                 user_auth_method=None, app_auth_method=None,
                                 psw_min_len=None, psw_change_freq=None, dept=None,
                                 space=None, date_last_ordered=None,
                                 purchase_price=None, warranty_expires=None,
                                 item_condition=None, quantity=None,
                                 assset_value=None, model_num=None, notes=None,
                                 link=None)
                if not dup:
                    item_id = db.create_item()  # Inserting an item into the Inv_Item table
                    print("Adding Information for ID:", item_id)
                    db.set_name(up_hosts[i], item_id)  # hostname
                    db.set_type(", ".join(device_types[i]), item_id)  # device type
                    db.set_os(", ".join(os_names[i]), item_id)  # os name
                    db.set_os_version(", ".join(os_versions[i]), item_id)  # os version
                    db.set_server(server_id, item_id)
                    db.set_mac(int(macs_lst[i]), item_id)
                    db.set_ports(", ".join(port_ids_lst[i]), item_id)
                    db.set_protocols(", ".join(protocols_lst[i]), item_id)
                    db.set_statuses(", ".join(status_lst[i]), item_id)
                    db.set_services(", ".join(services_lst[i]), item_id)
                    db.set_services_versions(", ".join(services_versions_lst[i]), item_id)
            
        except Exception as e:
            print("Error:", e)

'''
Function: extract_cmdline_username()
Args: None
Checks arguments provided for a username
Returns username field from command-line arguments provided from the UI
'''
def extract_cmdline_username():
    if (len(sys.argv) != 2):
        print("Usage: python3 crawl-device.py <username>")
        raise ValueError("Exactly one argument is required.")
    username = sys.argv[1]
    print(f"Username: {username}")
    return username


'''
Main Program Driver
'''
if __name__ == "__main__":
    print("Started Crawler...")
    # Username is a cmd-line argument passed from UI to script
    # Require a cmd-line argument to create DB API with proper scope
    username = extract_cmdline_username()
    
    nmap = Nmap() # Instantiate nmap object
    device_types = []; os_names = []; os_versions = []; port_ids_lst = []; protocols_lst = []; status_lst = []; services_lst = []; services_versions_lst = []
    server_name, target_host, gateway_ip, subnet, up_hosts, macs_lst, num_devices, city, region, country, encryption = device_stats()
    for host in up_hosts:
        start_time = time.time()
        device_type, os_name, os_version, ports, protocols, statuses, services, service_versions = get_os_and_open_ports(host)
        device_types.append(device_type)
        os_names.append(os_name)
        os_versions.append(os_version)
        port_ids_lst.append(ports)
        protocols_lst.append(protocols)
        status_lst.append(statuses)
        services_lst.append(services)
        services_versions_lst.append(service_versions)
        end_time = time.time()
        print(f"Executed in {end_time - start_time} seconds.")
        
    print_summary(city, region, country, up_hosts, macs_lst, device_types, os_names, os_versions, port_ids_lst, protocols_lst, status_lst, services_lst, services_versions_lst)
    database_push(username, up_hosts, device_types, os_names, os_versions, city, region, country, encryption, gateway_ip, server_name, services_versions_lst)
    
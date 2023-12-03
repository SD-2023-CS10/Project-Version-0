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

'''
ScannerThread class runs scan_uphosts() and retrieves result with multithreading.
'''
class ScannerThread(Thread):
        def __init__(self, argument):
            Thread.__init__(self)
            self.argument = argument
            self.result = None
        def run(self):
            self.result = scan_uphosts(self.argument) # store output from function

'''
ServiceThread class runs get_services() and retrieves result with multithreading.
'''
class ServiceThread(Thread):
    def __init__(self, argument):
        Thread.__init__(self)
        self.argument = argument
        self.result = None
    def run(self):
        self.result = get_services(self.argument)

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
Function: get_services
Args: ip_address
Finds ports that are discoverable / exploitable
Returns ports and service details using nmap_version_detection()
'''
def get_services(ip_address):
    nmap = Nmap()
    try:
        results = nmap.nmap_version_detection(ip_address)
        port_ids = []
        services = []
        service_products = []
        service_versions = []
        
        if ip_address in results:
            ports = results[ip_address]['ports']
            for port in ports:
                service = port['service']
                port_id = port['portid']
                port_ids.append(port_id)
                service_name = service.get('name', 'N/A')
                services.append(service_name)
                service_product = service.get('product', 'N/A')
                service_products.append(service_product)
                service_version = service.get('version', 'N/A') # grab version if applicable, else N/A
                service_versions.append(service_version)
            return port_ids, services, service_products, service_versions
        else:
            print("No information found for the given IP address.")
    except Exception as e:
        print(f"Error: {e}")
    return [], [], [], []

'''
Function: scan_hosts()
Args: host address
Fetches cur_device_name, os_name, os_gen, os_family, device_type for host using helper functions.
'''
def scan_uphosts(host):
    json_results, parsed_obj, stats = get_OS(nmap, host) # conduct OS detection scan
    os_name, os_gen, os_family, device_type = parse_OS_output(stats)
    print(f"\nDevice IP: {host}")
    print("Operating System Name: " + os_name)
    print("Operating System Generation: " + os_gen)
    print("Operating System Family: " + os_family)
    print("Device Type: " + device_type)  
    return os_name, os_gen, os_family, device_type

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
Function: fetch_host_stats()
Args: up_hosts
Create a ScannerThread to execute tasks
Returns list of device_names, os_names, os_families, and device_types.
'''
def fetch_host_stats(up_hosts):
    # Scan device and software details
    threads = []; os_names = []; os_gens = []; os_families = []; device_types = []
    for host in up_hosts:
        thread = ScannerThread(host)
        thread.start()
        print("Started scanning " + host + "...")
        threads.append(thread)
    for thread in threads:
        thread.join()
    try:
        for thread in threads:
            os_name, os_gen, os_family, device_type = thread.result
            os_names.append(os_name)
            os_gens.append(os_gen)
            os_families.append(os_family)
            device_types.append(device_type)
    except Exception as e:
        print("Unable to find OS.")
    return os_names, os_gens, os_families, device_types

'''
Function: fetch_ports_stats()
Args: up_hosts
Create a ServiceThread to execute tasks
Returns list of port_ids, services, service products, and service versions.
'''
def fetch_ports_stats(up_hosts):
    # Port scan and services on device
    port_ids_lst = []; services_lst = []; service_products_lst = []; service_versions_lst = []
    threads = []
    print("Started port scan...")
    for host in up_hosts:
        thread = ServiceThread(host)
        thread.start()
        threads.append(thread)
    for thread in threads:
        thread.join()
    try:
        for thread in threads:
            port_ids, services, service_products, service_versions = thread.result 
            port_ids_lst.append(port_ids)
            services_lst.append(services)
            service_products_lst.append(service_products)
            service_versions_lst.append(service_versions)
            
            print(f"\nPort Scan on {host}:")
            for i in range(len(port_ids)):
                print(f"\t{i}. Port: {port_ids[i]}\n\t   Service Name: {services[i]}\n\t   Product: {service_products[i]}\n\t   Version: {service_versions[i]}")
    except Exception as e:
        print("Unable to get discoverable ports")
    return port_ids_lst, services_lst, service_products_lst, service_versions_lst

def get_os_and_open_ports(up_hosts):
    try:
        os_details = []; ports = []; protocols = []; statuses = []; services = []
        print("\n",host)
        result = subprocess.run(['nmap', '-O', '-T4', host], capture_output=True, text=True, timeout=20) 
        output = result.stdout
        # Parse nmap output to extract OS information and open ports
        os_info_pattern = r"OS details: (.+)"
        os_match = re.search(os_info_pattern, output)
        os_info = os_match.group(1) if os_match else "N/A"
        print(os_info)
        os_details.append(os_info)
        # Parse nmap output to extract open ports
        port_info_pattern = r"(\d+)/(\w+)\s+(\w+)\s+(\w+)"
        port_matches = re.findall(port_info_pattern, output)
        
        for match in port_matches:
            port = match[0]
            ports.append(port)
            protocol = match[1]
            protocols.append(protocol)
            status = match[2]
            statuses.append(status)
            service = match[3]
            services.append(service)
            print(f"Port: {port}, Protocol: {protocol}, Status: {status}, Service: {service}")
        
    except subprocess.TimeoutExpired:
        print(f"Timeout occurred scanning {host}")
        os_details.append("N/A")
        ports.append("N/A")
        protocols.append("N/A")
        statuses.append("N/A")
        services.append("N/A")
    except Exception as e:
        print(f"Error retrieving OS information and open ports: {e}")
        os_details.append("N/A")
        ports.append("N/A")
        protocols.append("N/A")
        statuses.append("N/A")
        services.append("N/A")
    return os_details, ports, protocols, statuses, services
    
'''
Main Program Driver
'''
if __name__ == "__main__":
    print("Started Crawler...")
    nmap = Nmap() # Instantiate nmap object
    # Ensure Internet Connectivity
    try:
        target_host = socket.gethostname()
        print(target_host)
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
    os_lst = []; port_ids_lst = []; protocols_lst = []; status_lst = []; services_lst = []
    for host in up_hosts:
        os_details, ports, protocols, statuses, services = get_os_and_open_ports(host)
        os_lst.append(os_details)
        port_ids_lst.append(ports)
        protocols_lst.append(protocols)
        status_lst.append(statuses)
        services_lst.append(services)
        print()
    print(os_lst)
    print(port_ids_lst)
    print(protocols_lst)
    print(status_lst)
    print(services_lst)
    print("\nSummary:"); # print(up_hosts); print(macs_lst); print(os_names); print(os_families)
    # print(device_types); print(port_ids_lst); print(services_lst); print(service_products_lst)
    # print(service_versions_lst);
    
    for i in range(len(up_hosts)):
        print("Host: " + up_hosts[i])
        print("MAC Address: " + macs_lst[i])
        
        for j in range(len(port_ids_lst)):
            print("Operating System Name: " + ", ".join(os_lst[i]))
            print("Port ID: " + ", ".join(port_ids_lst[j]))
            print("Protocol: " + ", ".join(protocols_lst[j]))
            print("Status: " + ", ".join(status_lst[j]))
            print("Service: " + ", ".join(services_lst[j]))
        print()
        
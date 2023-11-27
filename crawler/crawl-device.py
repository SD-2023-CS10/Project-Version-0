'''
Network Crawling
'''
from nmap3 import Nmap
import json
import ipaddress
import requests
import netifaces
import socket
from scapy.all import ARP, Ether, srp
from pysnmp.hlapi import * # pip install pysnmp
import threading
from threading import Thread

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
    macs_lst = []
    arp = ARP(pdst=subnet)
    ether = Ether(dst="ff:ff:ff:ff:ff:ff")  # cover all IP range
    packet = ether/arp
    result = srp(packet, timeout=3, verbose=False)[0] # send packets out
    for sent, received in result:
        mac = received.hwsrc
        if mac != "":
            macs_lst.append(mac)
        else:
            macs_lst.append("N/A")
    # Record hosts that respond to ARP requests
    hosts_up = [res[1].psrc for res in result]
    return hosts_up, macs_lst

def get_OS(nmap, cur_ip):
    scan_dict = nmap.nmap_os_detection(str(cur_ip))
    json_results = json.dumps(scan_dict, indent=4) # returns type string
    # Convert string-results into parsed object
    parsed_obj = json.loads(json_results) # returns a json-object
    stats = []
    try:
        stats = parsed_obj[str(cur_ip)]["osmatch"] if "osmatch" in parsed_obj[str(cur_ip)] else []
        return json_results, parsed_obj, stats
    except:
        print("No known OS information.")
    return None

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

def get_hostname(cur_ip):
    # Perform a host discovery scan
    try:
        hostname = str(socket.gethostbyaddr(cur_ip)[0])
        return hostname
    except Exception as e:
        hostname = "N/A"
        return f"Error during hostname scan: {e}"
    
def get_public_ip():
    response = requests.get('https://api64.ipify.org?format=json').json()
    public_ip = response["ip"]
    return public_ip

def get_location(server_ip):
    response = requests.get(f'https://ipapi.co/{server_ip}/json/').json()
    print(response)
    city = response.get("city")
    region = response.get("region")
    country = response.get("country_name")
    return city, region, country

def get_services(nmap, ip_address):
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
                service_version = service.get('version', 'N/A')
                service_versions.append(service_version)
            return port_ids, services, service_products, service_versions
        else:
            print("No information found for the given IP address.")
    except Exception as e:
        print(f"Error: {e}")
    return [], [], [], []

def scan_uphosts(host):
    # Now crawl devices connected to the subnet
    cur_device_name = get_hostname(host)
    
    if cur_device_name.startswith("Error"):
        cur_device_name = "N/A"
        
    # Conduct OS detection scan
    json_results, parsed_obj, stats = get_OS(nmap, host)
    os_name, os_gen, os_family, device_type = parse_OS_output(stats)
    print(f"\nDevice IP: {host}")
    print("Hostname: " + cur_device_name)
    print("Operating System Name: " + os_name)
    print("Operating System Generation: " + os_gen)
    print("Operating System Family: " + os_family)
    print("Device Type: " + device_type)  
    
    return cur_device_name, os_name, os_gen, os_family, device_type
   

if __name__ == "__main__":
    # Instantiate nmap object
    print("Started Crawler...")
    nmap = Nmap()
    
    public_ip = get_public_ip()
    city, region, country = get_location(public_ip)
    gateway_ip = get_default_gateway()
    server_name = get_hostname(gateway_ip)
    subnet = get_network_subnet(gateway_ip)
    up_hosts, macs_lst = get_hosts_up(subnet)
    num_devices = len(up_hosts)
    
    print(f"\nServer Gateway IP: {gateway_ip}")
    print(f"Network Subnet: {subnet}")
    print(f"Number of Connections: {num_devices}")
    
    if not server_name.startswith("Error"):
        print(f"Server name for {gateway_ip} is: {server_name}")
    else:
        print(f"No hostname found on gateway: {gateway_ip}")
    if country != "": 
        print(f"Server Location: {city}, {region} in {country}\n")
    
    class ScannerThread(Thread):
        def __init__(self, argument):
            Thread.__init__(self)
            self.argument = argument
            self.result = None
        def run(self):
            self.result = scan_uphosts(self.argument)

    # Scan device and software details
    threads = []; device_names = []; os_names = []; os_gens = []; os_families = []; device_types = []
    for host in up_hosts:
        thread = ScannerThread(host)
        thread.start()
        print("Started scanning " + host + "...")
        threads.append(thread)
    for thread in threads:
            thread.join()
    try:
        for thread in threads:
            device_name, os_name, os_gen, os_family, device_type = thread.result
            device_names.append(device_name)
            os_names.append(os_name)
            os_gens.append(os_gen)
            os_families.append(os_family)
            device_types.append(device_type)
            
            print(device_name, os_name, os_gen, os_family, device_type)
    except Exception as e:
        print("Unable to find OS.")
    
    print("\nSummary:")
    print(up_hosts)
    print(device_names)
    print(os_names)
    print(os_families)
    print(device_types)
    print(macs_lst)
      
    # Port scan and services on device
    port_ids, services, service_products, service_versions = get_services(nmap, host) 
    print("\nPort Scan:")
    for i in range(len(port_ids)):
        print(f"\t{i}. Port: {port_ids[i]}\n\t   Service Name: {services[i]}\n\t   Product: {service_products[i]}\n\t   Version: {service_versions[i]}")


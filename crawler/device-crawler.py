import socket
import platform
import netifaces
import ipaddress
from scapy.all import ARP, Ether, srp
from pysnmp.hlapi import * # pip install pysnmp

def get_device_info():
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

def get_server_name(gateway_ip):
    try:
        server_name = socket.gethostbyaddr(gateway_ip)[0]
        return server_name
    except socket.herror as e:
        return f"Error: {e}"


if __name__ == "__main__":
    device_hostname, device_ip, device_type, os_name, os_version = get_device_info()
    gateway_ip = get_default_gateway()
    server_name = get_server_name(gateway_ip)
    subnet = get_network_subnet(gateway_ip)
    up_hosts = get_hosts_up(subnet)
    
    print(f"Device Hostname: {device_hostname}")
    print(f"Device IP Address: {device_ip}")
    print(f"Device Type: {device_type}")
    print(f"Operating System: {os_name} ")
    print(f"OS Version: {os_version}")
    print(f"Gateway: {gateway_ip}")
    print(f"Network Subnet: {subnet}")
    if not server_name.startswith("Error"):
        print(f"The router name associated with {gateway_ip} is: {server_name}")

    
    if up_hosts:
        for host in up_hosts:
            print(host)
    else:
        print(f"No hosts found on subnet: {subnet}")

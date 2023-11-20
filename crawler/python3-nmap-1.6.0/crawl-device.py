'''
Network Crawling
'''
from nmap3 import Nmap
import json
import ipaddress
import socket

def scan_device(nmap, start_ip, end_ip, dest_filename):
    cur_ip = start_ip
    
    # Iterate through the list of target IP addresses
    while cur_ip <= end_ip:
        print("\nStarted scanning " + str(cur_ip) + "...")
        
        # Conduct OS detection scan
        json_results, parsed_obj, stats = get_OS(nmap, cur_ip)
        print("OS Name: " + stats[0]["name"])
        print("OS Gen: " + stats[0]["osclass"]["osgen"])
        print(stats)
        
        # Write string-results to .json file
        with open(dest_filename,'w') as f:
            f.write(json_results)
        
        # Crawl for Hostname
        hostname = get_hostname(nmap, cur_ip)
        if not hostname.startswith("Error"):
            print(f"The hostname of the server with IP {cur_ip} is: {hostname}")
        else:
            print(f"Failed to retrieve the hostname. {hostname}")
        
        cur_ip += 1
    
    f.close()
    print("\nProcess finished.\n")
    
    
def get_OS(nmap, cur_ip):
    scan_dict = nmap.nmap_os_detection(str(cur_ip))
    json_results = json.dumps(scan_dict, indent=4) # returns type string
    # Convert string-results into parsed object
    parsed_obj = json.loads(json_results) # returns a json-object
    try:
        stats = parsed_obj[str(cur_ip)]["osmatch"]
    except:
        print("No known OS information.")
    return json_results, parsed_obj, stats
    
    
def get_hostname(nmap, cur_ip):
    # Perform a host discovery scan
    try:
        hostname, _, _ = socket.gethostbyaddr(str(cur_ip))
        return hostname
    except Exception as e:
        return f"Error during hostname scan: {e}"
        
    
if __name__ == "__main__":
    # Network Investigation
    nmap = Nmap()
    
    dest_filename = "test_results.json" # "scan_results.json"
    start_ip = "10.0.2.15"
    end_ip = "10.0.2.15"
    
    start_ip = ipaddress.IPv4Address(start_ip)
    end_ip = ipaddress.IPv4Address(end_ip)

    scan_device(nmap, start_ip, end_ip, dest_filename)
    

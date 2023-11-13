'''
Colleen Lemak
491 Scripting
Network Crawling
'''
import nmap3
from nmap3 import Nmap
import json
import ipaddress

def scan_OS(start_ip, end_ip, dest_filename):
    # Instantiate object
    nmap = Nmap()
    cur_ip = start_ip
    
    # Iterate through the list of target IP addresses
    while cur_ip <= end_ip:
        # Conduct OS detection scan
        print("\nStarted scanning " + str(cur_ip) + "...")
        scan_dict = nmap.nmap_os_detection(str(cur_ip))
        json_results = json.dumps(scan_dict, indent=4) # returns type string
        
        # Convert string-results into parsed object
        parsed_obj = parsed_obj.update(json.loads(json_results)) # returns a json-object
        try:
            stats = parsed_obj[str(cur_ip)]["osmatch"]
            print("OS Name: " + stats[0]["name"])
            print("OS Gen: " + stats[0]["osclass"]["osgen"])
            print(stats)
            # Write string-results to .json file
            with open(dest_filename,'w') as f:
                f.write(json_results)
        except:
            print("No known OS information.")
        
        cur_ip += 1
    
    f.close()
    print("\nProcess finished.\n")
    
    
if __name__ == "__main__":
    # Network Investigation
    dest_filename = "test_results.json" # "scan_results.json"
    
    # start_ip = "172.23.96.51"
    # end_ip = "172.23.96.51"
    
    # start_ip = "172.23.96.1"
    # end_ip = "172.23.96.1"
    
    start_ip = "10.0.0.0"
    end_ip = "10.0.0.19"
    
    start_ip = ipaddress.IPv4Address(start_ip)
    end_ip = ipaddress.IPv4Address(end_ip)
    with open(dest_filename,'w') as f:
        f.write("")
    f.close()
    scan_OS(start_ip, end_ip, dest_filename)
   
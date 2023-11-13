import nmap3
import pprint
nmap = nmap3.Nmap()
results = nmap.scan_top_ports("lUbuntu-vm")
pp = pprint.PrettyPrinter(indent=4)
pp.pprint(results)

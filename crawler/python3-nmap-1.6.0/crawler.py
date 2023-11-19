import nmap3
import pprint
nmap = nmap3.Nmap
nmapScanTechniques = nmap3.NmapScanTechniques()
pp = pprint.PrettyPrinter(indent=4)

'''
#get Nmap version details
result1 = nmap.nmap_version()
pp.pprint(result1)
'''

'''
#Nmap top port scan
result2 = nmap.scan_top_ports("myqwest5005")
pp.pprint(result2)
'''

'''
#Nmap Dns-brute-script( to get subdomains )
#results = nmap.nmap_dns_brute_script("domain")
'''

'''  
#Nmap list scan
result3 = nmap.nmap_list_scan("raspberrypi")
pp.pprint(result3)
'''

'''  
#Nmap Os detection
result4 = nmap.nmap_os_detection("raspberrypi")
pp.pprint(result4)
'''

'''
#Nmap subnet scan
result5 = nmap.nmap_subnet_scan("raspberrypi") #Must be root
pp.pprint(result5)
'''

'''
#Nmap version detection
result6 = nmap.nmap_version_detection("raspberrypi") # Must be root
pp.pprint(result6)
'''
   
#Nmap Scanning Techniques
#The script offers nmap scan techniques also as python function/methods

'''
#nmap_fin_scan
result7 = nmapScanTechniques.nmap_fin_scan("192.168.0.1")
pp.pprint(result7)
'''


'''
#nmap_idle_scan
result8 = nmapScanTechniques.nmap_idle_scan("192.168.0.1")
pp.pprint(result8)
'''

'''
#nmap_ping_scan
result9 = nmapScanTechniques.nmap_ping_scan("192.168.0.1")
pp.pprint(result9)
'''

'''
#nmap_syn_scan
result10 = nmapScanTechniques.nmap_syn_scan("192.168.0.1")
pp.pprint(result10)
'''


'''
#nmap_tcp_scan
result11 = nmapScanTechniques.nmap_tcp_scan("192.168.0.1")
pp.pprint(results11)
'''


'''
#nmap_udp_scan
result12 = nmapScanTechniques.nmap_udp_scan("192.168.0.1")
pp.pprint(results12)
'''

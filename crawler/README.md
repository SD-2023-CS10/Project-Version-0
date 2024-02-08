# Crawler Documentation

## About

The crawler agent is designed to automate network data collection and return as many helpful attributes to be assessed by our sponsor, Medcurity.  The goal of the crawl is to scan all devices connected to the host machine's network, collect as much information as possible, and securely push this data to the connected database.  

## Testing

When retrieving sensitive and oftentimes vulnerable data, it is crucial to note that *testing must be conducted in an isolated and controlled environment with proper authorization.*

## Dependencies

The network crawler agent is defined by the python script `crawl-device.py`.  To run the crawler, ensure the following been successfully installed and/or imported on the host machine. 
* Python3
* Nmap
```
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
```

## Run the Crawler

Compile and run crawler script with `python3` configured on the host machine.
```
python3 crawl-device.py
```

## Expected Output

When the script is executed , the terminal will output 

`Started Crawler...` .

Next, information will be displayed for the Target Hostname, Server Gateway IP, Network Subnet, Devices, Number of Connections, Server Location, and available Server Encryption.

As the script progresses, it will output feedback messages to the terminal indicating the ongoing operations.  Note that port-scanning typically consumes the majority of the scan time.


## Handling Duplicate Items
In conjunction with the DB API located in ../database directory, duplicate items are handled by checking the existence of the item before creating a new item.

If the current entry (regardless of the associated database table) matches an existing entry on every attribute, the crawler will not override the existing entry. If a user adds data to row entries (via UI manual input), the crawler clearly cannot detect this data without pulling from the database.  Thus, in the case a user re-reuns the scan on the same server, items will be added only if it does not match the crawled attributes.  By the API construction, only crawled attributes will be part of the select-from-where query, thus manual input will not be overridden. 


## Troubleshooting

1.  Ensure host machine running script is connected to an online network
2.  Check devices are active and online such that they are detectable
3.  Verify up-to-date and correct dependencies are installed prior to the run
4.  Test the environment is capable of running `.py` files
5.  Add `config.ini` credentials file for remote database connection and review database documentation for further details

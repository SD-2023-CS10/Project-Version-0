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

## Troubleshooting

1.  Ensure host machine running script is connected to an online network
2.  Check devices are active and online such that they are detectable
3.  Verify up-to-date and correct dependencies are installed prior to the run
4.  Test the environment is capable of running `.py` files
5.  Add `config.ini` credentials file for remote database connection and review database documentation for further details

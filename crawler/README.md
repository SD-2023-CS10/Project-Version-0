# Crawler Documentation

## About

The crawler agent is designed to automate network data collection and return as many helpful attributes to be assessed by our sponsor, Medcurity.  The goal of the crawl is to scan all devices connected to the host machine's network, collect as much information as possible, and securely push this data to the connected database.  

## Testing

When retrieving sensitive and oftentimes vulnerable data, it is crucial to note that *testing must be conducted in an isolated and controlled environment with proper authorization.* 

The testing environment requires secure internet connection for crawling and pushing to the remote database. 

## Dependencies

The network crawler agent is defined by the python script `crawl-device.py`.  To run the crawler, ensure the following been successfully installed and imported on the host machine. 

To automate the process, the `dependency-checks.php` script has been created to handle these packages. Note that this helper file leverages `pip3` and `python3` to check packages and install them as necessary.

Run the dependency installation file from Project-Version-0
```
php /crawler/dependency-cheks.php
```

If you run into issues, ensure that each dependency is installed and configured properly on your machine.

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

Compile and run the crawler script with `python3` configured on the host machine.
```
python3 crawl-device.py
```

## Expected Output

When the script is executed , the terminal will output 

`Started Crawler...` .

Next, information will be displayed for the Target Hostname, Server Gateway IP, Network Subnet, Devices, Number of Connections, Server Location, and available Server Encryption.

As the script progresses, it will output feedback messages to the terminal indicating the ongoing operations.  Note that port-scanning typically consumes the majority of the scan time.

## Integration Process
Integration of the crawler Python file with other components posed several challenges. The crawler solution consists of helper files to download dependencies, pass information from file-to-file, and pipe feedback messages to the terminal in order to confirm integrity of important data (i.e. the username of the client currently logged in).

1. Upon the repository tool's bootup, `login.html` prompts the user for their credentials.  This information logs the user in and confirms with the database that the credentials are valid. 

2. `index.php` is then launched, showing the user the Network Inventory Tool's homepage. The left panel contains various buttons with functionality, including a `RUN SCAN` button. 

3. Clicking this button triggers the helper script, `runScan.php`, which is configured to run relavent commands for data collection and connection.  The script runs the `dependency-checks.php` file located in `/crawler`, and subsequently also runs `crawl-device.py` autonomously.  

*In theory,* clients using the Network Inventory Tool should only have to interact with the user-interface, rather than running the crawler script itself with `python3 crawl-device.py`.


## Handling Duplicate Items
In conjunction with the DB API located in `../database` directory, duplicate items are handled by checking the existence of the item before creating a new item.

If the current entry (regardless of the associated database table) matches an existing entry on every attribute, the crawler will not override the existing entry. If a user adds data to row entries (via UI manual input), the crawler clearly cannot detect this data without pulling from the database.  Thus, in the case a user re-reuns the scan on the same server, items will be added only if it does not match the crawled attributes.  By the API construction, only crawled attributes will be part of the select-from-where query, thus manual input will not be overridden. 

This approach has been implemented with the `DB API` on `Location`, `Server`, and `Inventory Item` tables.


## Test Network Components
During the testing process, the following components have been used:  
- Raspberry Pi Kit (with Sense Hat)
- Century Link Wireless Modem
- Arris Surf Board Modem
- Devices belonging to Windows, Mac, and Linux systems
- Internet-Service-Provider network services


## Troubleshooting

1.  Ensure host machine running script is connected to an online network
2.  Check devices are active and online such that they are detectable
3.  Verify up-to-date and correct dependencies are installed prior to the run
4.  Test the environment is capable of running `.py` files
5.  Add `config.ini` credentials file for remote database connection and review database documentation for further details

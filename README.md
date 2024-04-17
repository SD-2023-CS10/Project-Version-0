# Project-Version-0

Our Network Inventory Tool is a comprehensive solution for managing and monitoring network devices. It allows you to scan devices on your network, push the collected data remotely to a MySQL database, and reflect changes in a user-friendly PHP interface.


## Features

- **Device Scanning:** The tool scans devices on your network to gather information such as hostname, IP address, MAC address, open ports, and more.
  
- **Remote Database:** Collected device information is securely pushed to a remote MySQL database, providing centralized storage and easy access to network data.

- **PHP User Interface:** The PHP-based user interface offers a visually appealing and intuitive way to view and manage network device information. Users can search, filter, and analyze device data conveniently.

- **Real-time Updates:** Changes made to the network inventory, either through scanning or manual updates, are immediately reflected in the PHP user interface, ensuring that the displayed information is always up-to-date.


## Installation

**Clone the Repository:** Clone the Network Inventory Tool repository to your local machine:

```bash
git clone https://github.com/SD-2023-CS10/Project-Version-0.git
```

Beyond cloning the repository to access, as needed, every functional part of the tool is accessable from the UI. Create a local PHP server using the `php -S localhost:8000` command in a bash terminal from the repository directory and navigate to `localhost:8000/UI/login.html`. From that page, you can log in as either a normal user or an admin user, as well as access admin functionality. After logging in, you are navigated to the home page, `localhost:8000/UI/index.php`, where you can see the inventory and launch the two scripts. You can launch the crawler with the "Run Scan" button and launch the export script with the "Download" button.

For instructions on further aspects of the project, such as user log-in and creation, as well as more specific details, such as the intricacies with admin users, how the database is set up, information on the crawler, and more, please refer to the other README documentation files.

## Maintenance

- A final release of the Network Inventory Tool repository will be released to Medcurity for future maintenance and implementation.
- Contributions are welcome! Feel free to submit bug reports, feature requests, or pull requests to help improve the tool for everyone.

## Future Development

To improve the user experience, the following future development ideas have been discussed:
1. Each client has a manager for user-account management
2. Afford editable cells on the user-interface's Server page
3. Implement user-interface filtering
4. Add a confguration network page to the user-interface
5. Add status messages and a progress bar based on crawl-status
6. Allow user to enter a range of IPs to scan in interface
7. Implement settings-accessibility functional page
8. Allow admin to add and set their client through the interface
9. Allow admin to run scan for a set client through the interface
10. Create a script to execute the tool and all components

## Professional Developers

Brandon Huyck, Colleen Lemak, Artis Nateephaisan, Jack Nealon

# Program Instructions

## Add Above Directions WIP
After a successful login, the user will be redirected to the system inventory home page. 



## System Inventory Homepage

### Sidebar Navigation
![Alt text](/UI/resources/sidebar.png)


This is where the sidebar/menu will appear on the webpage.
It has interactive functionality and animation style (like collapsing and expanding).

### Logo and Title ---

* At the top of the sidebar, there's a logo and the title.
The image is the Medcurity Network Inventory Team logo, and the title says "Network Inventory".


### Navigation Links ---
Below the logo and title, there are buttons for navigating around the website.
Each button has an icon and text indicating where it will take you. There are 4 buttons that function as follows:
* HOME - The homepage displaying the current system inventory that is controlled through each categories respective tab button. 
* SETTINGS - User is able to view profile information and the current network information. The user can also change accessibility settings and access help manuals with program instructions and help contact information. 
* DOWNLOAD - Download client data into a csv document in the csv project folder.
* RUN SCAN - Starts up the crawler to scan the network and record the resulting devices to the system inventory.

---
### Client Name and Page Titles 
![Alt text](/UI/resources/titles.png)

Displays the client's name and title of the page. 

---
### System Information Navigation
![Alt text](/UI/resources/tabs-nav.png)

This table is where all of the current discovered devices along with it's repective information. Each tab's information can be viewed as followed:

* System/Devices
    * Item ID
    * Name
    * Type of Application/Device
    * Application Version in Place
    * Operating System
    * OS Version
    * Automatic Log-off Frequency
    * Delete Device

* Server
    * Item ID
    * Name
    * Type of Application/Device
    * Server Name
    * Server IP Address
    * Cloud or On Premise?
    * Location

* Electronic Personal Health Information (ePHI)
    * Item ID
    * Name
    * Type of Application/Device
    * ePHI YES/NO
    * Encrypted? YES/NO
    * If Yes, Encryption Method
    * If Yes, Encryption Tested?
    * Applications Interfaced with

* Authentication
    * Item ID
    * Name
    * Type of Application/Device
    * User Authentication Method
    * Application Authentication Method
    * Minimum Password Length (as applicable)
    * Password Change Frequency (as applicable)

---
### System Information Content Table
![Alt text](/UI/resources/system-content.png)
* This is where all of the system information will be stored and shown, of the text fields shown, all but the title cells and the id column can be modified with realtime updates to the database. Each cell is able to be filled with information either through the crawler or through manual input with the changes being reflected in the database. 
---

### Delete Devices from Inventory
![Alt text](/UI/resources/del-btn.png)

* Here you are able to delete a device from the system inventory. Before deletion, the page asks a confirmation of deletion before the delete action is taken and applied to the database. These actions cannot be undone and are permanent.
---

### Add a New Device Manually to Inventory
![Alt text](/UI/resources/add-dev.png)

* This input box provides the user with the ability to add a device starting with the devices name. After adding the device by clicking the 'Add' button, the table is reloaded to display the new device and the database is updated with the new device and a respective item id. The cells for that device can then be modified in the table as stated before in the 'System Information Content Table' description. These devices can be removed using the delete button and empty device fields are accepted. 

## Settings Page

#### Logo and Title ---

* Displays the client's name and title of the page. 

#### Navigation Menu ---

* This page is where the user can access their profile information, change Accessibility settings (work in progress), view Network settings and information, and access program help manuals and the Medcurity contact information. 
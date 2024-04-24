# Program Instructions

## Login

### Users
The user interface is designed to support two group of users: administrative users from Medcurity and Medcurity's client users. Administrative users from Medcurity are able to create new users and delete pre-existing users, as well as view all data from every client. Client users are able to log in to their account and see data from their respective client network. They are also able to change their password as frequently as needed.

---
### Main Login Page
![Alt text](/UI/resources/MainLoginPage.png)

In this page, both client and admin users can login to access the System Inventory Homepage. Client users are only able to access the network information from their respective client, whereas admin users can access the network information from every client. Here, they are also able to access the "Change Password" page, the "Forgot Password" page, and the "Admin Login" page. Details for each specific page will be written in detail below.

---
### The Change Password Page
![Alt text](/UI/resources/ChangePasswordPage.png)

In this page, both client and admin users are able to change their passwords. By entering their username, old password they wish to change, and the new password they wish to change it to, the database will replace their old hashed and salted password with a new hashed and salted password, equally as secure as before.

This page is primarily meant for client users, since Medcurity admins are to send their client users a username and default password, where the client users are prompted to change their default password immediately so that Medcurity does not have access to their login information, ensuring security. In case the client users feel their password is compromised or want to change their passwords frequently to prevent security risks, they are meant to do so on this page.

---
### The Forgot Password Page
![Alt text](/UI/resources/ForgotPasswordPage.png)

This simple page is meant to show the contact information for Medcurity, which includes their email and phone number.

---
### The Admin Login Page
![Alt text](/UI/resources/AdminLoginPage.png)

Similar to the main login page, this page is meant only for verified admin users to login, where they are then able to access the "Admin Actions" page, which grants them the ability to create and delete users in the Admin Actions page.

---
### The Admin Actions Page
![Alt text](/UI/resources/AdminActionsPage.png)

#### Account Creation
Verified admin users are able to create new user accounts, either client users or other admin users. By entering their username (which must be unique), default password (which is meant to be changed later by the user in the Change Password page), and the client company (if set to "admin", the user account will have administrative permissions). Clicking the "Create Client Account" button (which also has the power to create admin users) will create the user account, given the username is unique, and a success message will pop up. 

###### NOTE:
Usernames must be unique, even upon different if the accounts belong to different clients. This means that there cannot be a "johndoe1" user in the "MedCorp" client and another "johndoe1" user in the "MedINC" client.

#### Account Deletion
Verified admin users are able to delete pre-existing user accounts, whether it be a client or admin user. They can do this by entering a username and the client company that is attached to that user account (if the user is an admin, their client is "admin"). Clicking the "Delete Client Account" button (which also has the power to delete admin users) will delete the user account, given the username and client fields match the database, and a success message will pop up. 


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

This table is where all of the current discovered devices along with its respective information. Each tab's information can be viewed as followed:

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
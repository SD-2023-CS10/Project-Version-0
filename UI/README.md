# User Interface Documentation

## Design Requirements
As a primary component of the program’s front end, users will interact with an intuitive and clear user interface for the means to view, analyze, and control the functionality of the tool.
It should support users directly from Medcurity as well as their clients, the interface should be easy for new users to interact with and absent of any visual clutter that could potentially confuse the user
while maintaining access to all relevant features of the Inventory Tool. 

## Anticipated Users
The user interface is designed to support two group of users: administrative users from Medcurity and Medcurity's client users. Administrative users from Medcurity are able to create new users and delete pre-existing users, as well as view all data from every client. Client users are able to log in to their account and see data from their respective client network. They are also able to change their password as frequently as needed.

## Design Overview
Upon access of the application, the login page is the first interface the user will encounter. Here there are four options: inputting valid username and password credentials and logging in, going to the "Change Password" section, going to the "Forgot Password" section, and going to the admin login page. 

With a successful login with valid credentials, the client user will be brought to the home page, where they can access the Medcurity Inventory Tool and view, scan, and edit their respective company's network information. 

The "Change Password" page allows user to change their password using their "Old Password" provided by Medcurity and changing it to a "New Password". It is important to note that upon password change, Medcurity has no access to the client users' passwords, since they are stored in the database as a salted and hashed value.

The "Admin Login" page is similar to the normal login page, but it is meant for admin access. Upon successful login with valid credentials, the admin will be able to create new user accounts for clients, allowing them to set up a 
username and password, where their clients can then change the password information in the "Change Password" page. They are also able to delete pre-existing clients for reasons such as the user leaving the client company.


## Login for Administrators

In the main login menu one of the options "administrator login" allows for Medcurity admins to login with their credentials. After they login, they have access to the "Create Client Account" page, where they can add new client accounts into the database.

The purpose of this is to set up a default username and password for their clients, which will be sent to their clients (through any secure means) where the client will be prompted to change their Medcurity-provided password to one they desire so that Medcurity does not know their client's password, maxizing the safety and security of their clients. Since Medcurity does not have access to the user's new password, they cannot change the password should the user forget it. 

Please note that usernames must be unique for each user and upon account creation they cannot be changed. 

## Login for Clients

In the login menu, client users are prompted for a username and password. Once the user provides valid credentials, they will be able taken to the Medcurity Inventory Tool application, where they will have access to all of its features. 

The "Forgot Password" option in the login menu takes the user to a page that contains Medcurity's contact information, which includes their support email and phone number. This allows the user to contact them should they face any issues with logging in. While Medcurity cannot send the user their password since they do not have access to it, they could delete the old user account from the database entirely and create a new account with the same old username and a default password, sending that information to the client user so they have access to their old username and can reset their password.

Another option in the login menu intended primarily for client users is the "Change Password" page. Since Medcurity admins provide the clients with default login information, it is recommended that they change their password
immediately through this page to ensure Medcurity does not know their password. This page also allows for clients to change their password as frequently as desired, preventing constant access from attackers should the login information be breached and also prevents the use of saved password. Overall, it is a good security practice to change the account password regularly.

## Development Set-Up

For development setup, please refer to the readme in Project-Version-0/database/README.md


## Sidebar Navigation

The Left Menu Area:
This is where the sidebar/menu will appear on the webpage.
It has interactive functionality and animation style (like collapsing and expanding).
The size and position of the sidebar are defined here:

Logo and Title:
At the top of the sidebar, there's a logo and the title.
The image is the Medcurity Network Inventory Team logo, and the title says "Network Inventory".

Navigation Links:
Below the logo and title, there are buttons for navigating around the website.
Each button has an icon and text indicating where it will take you.
When you click these buttons, the sidebar will close automatically.
There's also a button for downloading client data into a csv document in the csv project folder.
Lastly, there's a button labeled "Run Scan" which starts up the crawler to scan a network and record the resulting devices


## Main Homepage

Tabs for Organization:
Imagine the tabs like labeled sections in a big binder. Each one represents a specific area you can manage, like different sections of a book.
For example, there's a tab for "System/Devices," one for "Server," another for "ePHI," "Authentication," and "Asset Information" each with
a table displaying it's respective information.

Filter Search Box:
Just like when you search for something on the internet, these search boxes help you find specific information quickly within each section.
You type in a keyword related to what you're looking for, and the system shows you only the relevant information.

Tables for Data:
Inside each section, there's a table, which is like a grid of information, neatly organized into rows and columns.
These tables display important details about the items or aspects of the system you're managing.
For example, in the "System/Devices" section, you might see details like device names, types, versions, and other relevant information.

Editing and Adding:
You can interact with the information directly on the page. If you need to make a change, like fixing a typo or updating a detail, you can click on the item's cell and type in the new information.
There are also forms provided where you can add completely new items to the system. It's like filling out a digital form with the necessary details.

Keeping Things Safe:
The system has built-in safeguards to ensure that any changes you make are safe and won't accidentally cause problems.
For example, before you delete something, it'll ask you to confirm to make sure you're not deleting it by mistake.

Easy to Use:
Everything is designed to be user-friendly, so you don't need any special technical knowledge to navigate and manage the system.
It's meant to be intuitive and straightforward, making it easy for anyone to use without feeling overwhelmed.

This page provides organized sections of information, search functionality, interactive tables, and user-friendly forms, all aimed at simplifying the process of monitoring, editing, and adding information to provide a comprehensive list of the network.

## Settings Page

### Profile
This page displays the current information associated with their account including their Name, client, date, and baa. This information is unable to be edited by anyone other than a Medcurity representative or developer. 

### Accessibility
In Development

### Network
In Development

### Contact
Provides the user with help manuals along with a Medcurity representative contact abd/or messaging ticket service to help the user with understanding the system, troubleshooting, and development setup.

Currently displays the information contained in the UI README.md

## Encryption for User Login Information
The username and password of all users are stored in a database. The username is stored as is, but the passwords are stored salted and hashed using the bcrypt algorithm.

The bcrypt algorithm is among the most secure methods of encryption of data due to the following features: 


#### Salt Generation: 
Bcrypt generates a random salt value. A salt is a random piece of data that is added to the password before hashing. This makes it harder for attackers to use precomputed tables, like rainbow tables, to crack passwords.


#### Key Expansion: 
Bcrypt expands the password and salt into an initial state that will be used in the hashing process.


#### Iterative Hashing:
Bcrypt iteratively applies a cryptographic hash function (Blowfish cipher) multiple times to the initial state. The number of iterations is determined by a cost parameter, which can be adjusted to make the hashing process slower and more computationally intensive. This helps to thwart brute-force attacks.


#### Output: 
The final hashed password, along with the salt and cost parameter, is stored in a database. When a user tries to log in, the same process is applied to the provided password, and the resulting hash is compared with the stored hash. If they match, the password is verified.

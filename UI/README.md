# User Interface Documentation

## Design Requirements
As a primary component of the program’s front end, users will interact with an intuitive and clear user interface for the means to view, analyze, and control the functionality of the tool.
It should support users directly from Medcurity as well as their clients, the interface should be easy for new users to interact with and absent of any visual clutter that could potentially confuse the user
while maintaining access to all relevant features of the Inventory Tool. 

## Anticipated Users
The user interface is designed to support two group of users: administrative users from Medcurity, who can create new client users, and Medcurity clients, who can view their network data from their respective company.

## Design Overview
Upon access of the application, the login page is the first interface the user will encounter. Here there are three options: inputting valid username and password credentials and logging in, going to the change password and new user page, 
and going to the administrator login page. 

Upon successful login with valid credentials, the client user will be brought to the home page, where they can access the Medcurity Inventory Tool and view, scan, and edit their respective company's network information. 

The "change password or new user" page allows user to change their password.

The "administrator login" page is similar to the normal login page, but it is meant for admin access. Upon successful login with valid credentials, the admin will be able to create new user accounts for clients, allowing them to set up a 
username and password, where their clients can then change the password information.


## Login for Administrators

In the main login menu one of the options "administrator login" allows for Medcurity admins to login with their credentials. After they login, they have access to the "Create Client Account" page, where they can add new client accounts into the database.
The purpose of this is to set up a default username and password for their clients, which will be sent to their clients (through any secure means) where the client will be prompted to change their Medcurity-provided password to one they desire so that 
Medcurity does not know their client's password, maxizing the safety and security of their clients. Please note that usernames must be unique for each user and upon account creation they cannot be changed.

## Login for Clients

In the login menu, client users are prompted for a username and password. Once the user provides valid credentials, they will be able taken to the Medcurity Inventory Tool application, where they will have access to all of its features. 
The other option in the login menu for intended primarily for client users is the "change password or new user" page. Since Medcurity admins provide the clients with default login information, it is recommended that they change their password
immediately through this page to ensure Medcurity does not know their password. This page also allows for clients to change their password as frequently as desired, preventing constant access should the login information be breached and also prevents 
the use of saved password. Overall, it is good practice to change the account password regularly.

## Home Page



## Settings Page



## Development Set-Up

To use the database APIs during development, create a config file of the appropriate format in the appropriate directory. Get the values from the Google Doc Kraig shared with us.

For the crawler, this would be database/config.py. In this file, create a dictionary defined as below. All keys and values should be strings.

```
config = {
    'host' : <host>,
    'user' : <username>,
    'pass' : <password>,
    'db' : <databasename>
}
```

For the UI, this would be UI/config.ini. In this file, add the code defined as below. Note that only the username and password should be in quotes.

```
[database]
servername = <host>
username = <username>
password = <password>
```

With this, the API should work. To access the Database through the command line, use the linux command ```mysql -h <database> -u <username> -p``` and enter the password when prompted. This can be done for testing.


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

# Database Documentation

## Design Requirements

The database was designed based off documentation from and meetings with our sponser, Medcurity. Medcurity has all of their current databases on AWS using MariaDB and MySQL, and so we used those, as well. The database on AWS was set up by a company Medcurity contracts with, ZipLine; they provided us with the host, port, users, and passwords.

## Users

There are two users for the database: an admin user with full priviledges, and an access user that has insertion priviledges. The admin user is used for development to create the schema and relations. The access user is used in deployment to allows Medcurity's clients to view and update their subset of the database.

## Design Overview

Medcurity provided us with a spreadsheet that contained a list of information that they ask their clients to provide them as part of their device inventory. We took this list, made each item a column in our database, and then normalized the it. We also added a User relation that will store authorized usernames, the hash of their salted passwords, and the client that they are associated with (and therefore have access to view and update).

A quick overview of the schema:
- Client is its own relation since it is a foreign key for many other relations but has no additional, determined attributes of its own.
- User requires unique user names for ease of user creation. Users are tied to a specific client, which determines what information they can access. Users have a password that they use to login to the system; the password is salted and hashed before being stored; the PHP function doing that returns a 60 character string, but noted that number could be expanded in the future, up to 255 characters, hence why we chose the type of the field to be VARCHAR(255).
- Vendors are mostly defined by their email address, as theoretically many venders may have the same name, but they cannot have the same email. However, a single vendor could be the vendor for multiple clients but have different BAAs and dates with each, requiring the relation to have a composite key of (email, client).
- Location has a surrogate key, as there is no easy way to define it through its fields, and is a foreign key that is referenced by Server.
- Server doesn't require any fields be NOT NULL, so it also has a surrogate key. It references Location as a foreign key as previously mentioned. It stores the IP address as a BIGINT, as that is required for IPv6 addresses. Since an IPv4 and IPv6 address can look the same in decimal notation but be different in practice, we also store the IP version as an attribute. To ensure data integrity, we place appropriate checks and uniqueness requirements on IP addresses and versions.
- Inv_Item is the main relation, storing the devices and software that are the main focus of this project. It uses a surrogate key, since none of the fields are NOT NULL, and what may be filled in or blank is not a given. Of note, it places a upper-bounds check on MAC addresses; MAC addresses need to be stored as a BIGINT due to their size, but cannot be the full size permitted by BIGINT. Also, fields that should represent monetary values are stored as DECIMAL(13,4) to follow the Generally Accepted Accounting Principles (GAAP).

## Python API for Crawler

To use the API in the Crawler script, do the following.

1. Ensure you are running the script from the parent directory, project-version-0
2. Import the Database API with `from database.dbapi import DBAPI`
3. When needing to use the API, use the block `with DBAPI() as <varname>:`
4. Alternatively, create an instance of the class and then use that in the `with` block as such:
```
<var-foo> = DBAPI()
with <var-foo> as <var-bar>:
    pass
```
5. Within the block, execute statements using method calls on the reference, such as `<varname>.create_item()`

Note: In general, methods do not return anything. The exception to this are when the object being created has an surrogate key. In this case, the auto-incremented ID value is returned for future reference.

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

## Notes

Two typos that are present in the database are "assset_value" with three (3) 's's, and "Vender" with an 'e' instead of an 'o'. Be mindful of these with further development.
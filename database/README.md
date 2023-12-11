# Database Documentation

## Design Requirements

The database was designed based off documentation from and meetings with our sponser, Medcurity. Medcurity has all of their current databases on AWS using MariaDB and MySQL, and so we used those, as well. The database on AWS was set up by a company Medcurity contracts with, ZipLine; they provided us with the host, port, users, and passwords.

## Users

There are two users for the database: an admin user with full priviledges, and an access user that has insertion priviledges. The admin user is used for development to create the schema and relations. The access user is used in deployment to allows Medcurity's clients to view and update their subset of the database.

## Design Overview

Medcurity provided us with a spreadsheet that contained a list of information that they ask their clients to provide them as part of their device inventory. We took this list, made each item a column in our database, and then normalized the it. We also added a User relation that will store authorized usernames, the hash of their salted passwords, and the client that they are associated with (and therefore have access to view and update).

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

- Decimal (13, 4) allows for monetary values to follow the Gene3rally Accepted Accounting Principles
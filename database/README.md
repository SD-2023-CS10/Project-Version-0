# Database Documentation

## Design Requirements

The database was designed based off documentation from and meetings with our sponser, Medcurity. Medcurity has all of their current databases on AWS using MariaDB and MySQL, and so we used those, as well. The database on AWS was set up by a company Medcurity contracts with, ZipLine; they provided us with the host, port, users, and passwords.

## Users

There are two users for the database: an admin user with full priviledges, and an access user that has insertion priviledges. The admin user is used for development to create the schema and relations. The access user is used in deployment to allows Medcurity's clients to view and update their subset of the database.

## Design Overview

Medcurity provided us with a spreadsheet that contained a list of information that they ask their clients to provide them as part of their device inventory. We took this list, made each item a column in our database, and then normalized the it. We also added a User relation that will store authorized usernames, the hash of their salted passwords, and the client that they are associated with (and therefore have access to view and update).

## Notes

- Decimal (13, 4) allows for monetary values to follow the Gene3rally Accepted Accounting Principles
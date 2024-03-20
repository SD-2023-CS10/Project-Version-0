# CSV Script Documentation

## Design Requirements

The data collected with the crawler and stored in the database is typically displayed in the UI. However, it was an important design requirement from the beginning of the project to let the data be accessable, shareable, and readable through a traditional spreadsheet format. As such, we have created a script that exports all the data from the database for logged in client to a csv file.

## Design Overview

The script uses Python's CSV module to write to a csv file. It also uses our DB API for Python. The script starts by creating a file, output.csv, if one does not already exist. Next, it connects to the database, based on the logged in user. Then, it connects the csv writer to the outfile, and it writes a header row, describing what data is being put in each column. Finally, it calls the export method from the DB API, which returns a list of all inventory items and writes them to the csv file with the writer, before closing all objects safely.

There is also an admin-export script. This is for when a Medcurity admin wants to export all devices in the database for all of their clients. It does first confirm whether or not they are an admin before letting this operation occur.

## Notes

Having the DB API export method use a generator with yield statements was considered. The potential problem with this is data integrity of the results. If a user calls the export method to start the generator, but then changes what the result set is pointing to with another method before returning to the generator and querying for the next result the two result sets would be different and the user would be getting bad data for the next export request. As such, and since we anticipate to only ever be handling a small number of inventory items at a time, storing all items in a list and returning that is not prohibative on memory. If, in the future, a large amount of data is expected to be exported, then implementing the export method with a generator may be advisable.

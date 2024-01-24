# CSV Script Documentation

## Design Requirements

The data collected with the crawler and stored in the database is typically displayed in the UI. However, it was an important design requirement from the beginning of the project to let the data be accessable, shareable, and readable through a traditional spreadsheet format. As such, we have created a script that exports all the data from the database for logged in client to a csv file.

## Design Overview

The script uses Python's CSV module to write to a csv file. It also uses our DB API for Python. The script starts by creating a file, output.csv, if one does not already exist. Nesxt, it connects to the database, based on the logged in user. Then, it connects the csv writer to the outfile, and it writes a header row, describing what data is being put in each column. Finally, it calls the export method from the DB API, returning a generator that continuously yields each row for the csv writer, before closing all objects safely.
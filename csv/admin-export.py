'''
 * File Name: admin-export.py
 * 
 * Description:
 * This is the export script for Medcurity admins. It connects to the database, and then writes each
 * row into a csv file for all of Medcurity's clients, attaching the client name to the row.
 * Refer to /csv/README.md for more detailed information.
 * 
 * @package MedcurityNetworkScanner
 * @authors Brandon Huyck (bhuyck@zagmail.gonzaga.edu)
 * @license 
 * @version 1.0.0
 * @link 
 * @since 
 * 
 * Usage:
 * This file should be placed in the csv subdirectory of the application. It
 * can be launched from its parent directory by invoking it with python. No
 * modifications are necessary for basic operation, but customization can be
 * done by editing the file paths.
 * 
 * Notes:
 * - There are checks to ensure the person launching this is an admin; the UI checks when launhing there,
 *   and the DBAPI checks that the username of the user it is signed in as is an admin.
 * 
 * TODO:
 * - Update the header row writing to be done dynamially through the dbapi.py
 *   admin_export() method.
 * 
'''

import os, sys
current_dir = os.path.dirname(os.path.abspath(__file__))
parent_dir = os.path.dirname(current_dir)
sys.path.append(parent_dir)

import csv
from database.dbapi import DBAPI
from sys import argv

if __name__=='__main__':
    with open('../csv/outfile.csv', 'w', newline='') as outfile:
        with DBAPI(argv[1]) as dbapi:
            writer = csv.writer(outfile)
            writer.writerow(("client", "name", "type", "version", "os", "os_version",
                                "mac", "ports", "protocols", "statuses",
                                "services", "services_versions", "poc", "email",
                                "baa", "date", "auto_log_off_freq", "name",
                                "ip_address", "ip_version", "cloud_prem", "details",
                                "protection", "ephi", "ephi_encrypted",
                                "ephi_encr_method", "ephi_encr_tested",
                                "interfaces_with", "user_auth_method",
                                "app_auth_method", "psw_min_len", "psw_change_freq",
                                "dept", "space", "date_last_ordered", "purchase_price",
                                "warranty_expires", "item_condition", "quantity",
                                "assset_value", "model_num", "notes", "link"))
            writer.writerows(dbapi.admin_export())
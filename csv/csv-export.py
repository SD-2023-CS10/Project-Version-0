'''
 * File Name: csv-export.py
 * 
 * Description:
 * This is the export script. It connects to the database, and then writes each
 * row into a csv file.
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
 * - Additional notes or special instructions can be added here.
 * 
 * TODO:
 * - Update the header row writing to be done dynamially through the dbapi.py
 *   export() method.
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
            writer.writerow(("name", "type", "version", "os", "os_version",
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
            writer.writerows(dbapi.export())
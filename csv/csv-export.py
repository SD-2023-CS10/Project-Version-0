import os, sys
current_dir = os.path.dirname(os.path.abspath(__file__))
parent_dir = os.path.dirname(current_dir)
sys.path.append(parent_dir)

import csv
from database.dbapi import DBAPI

if __name__=='__main__':
    with open('./csv/outfile.csv', 'w', newline='') as outfile:
        with DBAPI() as dbapi:
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
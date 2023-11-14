'''
main()
'''

import mysql.connector as mc
from config import config

'''
Reads config info from imported config dict. Returns a tuple of relavent info.
'''
def _read_config_info(config_dict):
    try: 
        usr = config_dict['user']
        pwd = config_dict['pass']
        hst = config_dict['host']
        dab = config_dict['db']
        return usr, pwd, hst, dab
    except Exception as e:
        print(e)
        exit()

'''
Establishes the connection to the database given the config information.
Returns a reference to the connection and cursor/result set.
'''
def _establish_connection(usr, pwd, hst, dab):
    try:
        con = mc.connect(user=usr,password=pwd, host=hst, database=dab)
    except mc.Error as err:
        print(err)
        con.close()
        exit()
    try:
        rs = con.cursor()
    except mc.Error as err:
        print(err)
        rs.close()
        con.close()
        exit()
    return con, rs

def main():
    config_info = _read_config_info(config)
    usr, pwd, hst, dab = config_info
    con, rs = _establish_connection(usr, pwd, hst, dab)

    done = False
    while not done:
        done = True
    rs.close()
    con.close()

if __name__ == '__main__':
    main()
'''
main()
'''

import mysql.connector as mc
from mysql.connector import Error as mysql_connector_Error
from mysql.connector import errorcode
from database.config import config

class DBAPI:
    
    def __enter__(self):

        class Connector:

            def __init__(self):
                usr, pwd, hst, dab = self._read_config_info(config)
                self._establish_connection(usr, pwd, hst, dab)
                self.client = self._validate_varchar("MedCorp") # TODO: fix client -- fetch based on logged in user

            '''
            Reads config info from imported config dict. Returns a tuple of relavent info.
            '''
            def _read_config_info(self, config_dict):
                # try: 
                usr = config_dict['user']
                pwd = config_dict['pass']
                hst = config_dict['host']
                dab = config_dict['db']
                return usr, pwd, hst, dab
            
            '''
            Establishes the connection to the database given the config information.
            Returns a reference to the connection and cursor/result set.
            '''
            def _establish_connection(self, usr, pwd, hst, dab):
                self.con = mc.connect(user=usr,password=pwd, host=hst, database=dab)
                try:
                    self.rs = self.con.cursor()
                except mc.Error as err:
                    self.con.close()
                    raise err

            def _validate_varchar(self, s, l=255):
                s = str(s)
                if len(s) > l:
                    raise ValueError("String \"" + s + "\" too large for VARCHAR size " + l)
                return s

            def _validate_int(self, i, signed=False, size="INT"):
                try:
                    i = int(i)
                except ValueError:
                    raise ValueError(i + " cannot be cast to int")
                if not signed and size=="INT":
                    if 0 <= i and i < 2**32:
                        return i
                    else:
                        raise ValueError("Int " + i + " too large for type UNSIGNED INT")
                if not signed and size=="BIGINT":
                    if 0 <= i and i < 2**64:
                        return i
                    else:
                        raise ValueError("Int " + i + " too large for type UNSIGNED BIGINT")
                else:
                    raise NotImplementedError("_validate_int does not support params, signed=" + signed + ", size=\"" + size + "\"")

            def _validate_bool(self, b):
                return bool(b)

            def _validate_date(self, d):
                try:
                    d = str(d)
                except:
                    raise TypeError("Date, " + d + ", should be castable to string")

                try:
                    arr = [int(n) for n in d.split('-')]
                except:
                    raise TypeError("Date, " + d + ", must be in format 'YYYY-MM-DD'")
                
                if arr[0] < 1000 or arr[0] > 9999:
                    raise ValueError("Date, " + d + ", must have year 1000 <= y <= 9999")
                if arr[1] < 1 or arr[1] > 12:
                    raise ValueError("Date, " + d + ", must have month 01 <= y <= 12")
                if arr[2] < 1 or arr[2] > 31:
                    raise ValueError("Date, " + d + ", must have day 01 <= y <= 31")

                if arr[1] in {4, 6, 9, 11} and arr[2] == 31:
                    raise ValueError("Date, " + d + ", does not allow day " + arr[2] + " for month " + arr[1])
                elif arr[1] == 2 and not (arr[2] <= 28 or arr[0] % 4 == 0 and arr[2] == 29):
                    raise ValueError("Date, " + d + ", does not allow day " + arr[2] + " for month " + arr[1])
                    
                return d

            def _validate_cloud_prem(self, v):
                v = str(v)
                if v != "Cloud" or v != "On-Premise":
                    raise TypeError("cloud_prem needs to be of value \"Cloud\" or \"On-Premise\"")
                return v

            def _validate_text(self, t, size="MEDIUMTEXT"):
                t = str(t)
                if size=="MEDIUMTEXT":
                    if len(t) < 2**24:
                        return t
                    else:
                        raise ValueError("TEXT " + t + " too large for type MEDIUMTEXT")
                else:
                    raise NotImplementedError("_validate_text does not support params, size=\"" + size + "\"")

            def _validate_ip_address(self, addr, version): # TODO
                version = str(version)
                if version != "IPv4" or version != "IPv6":
                    raise TypeError("ip_version requires value of \"IPv4\" or \"IPv6\"")
                if version == "IPv4":
                    try:
                        addr = self._validate_int(addr)
                    except ValueError:
                        raise ValueError("IPv4 requires unsigned address < 2^32 (" + addr + " given)")
                else: # version == "IPv6"
                    try:
                        addr = self._validate_int(addr, size="BIGINT")
                    except ValueError:
                        raise ValueError("IPv6 requires unsigned address < 2^64 (" + addr + " given)")
                return addr, version

            def _validate_decimal(self, d, m=13, n=4):
                d = str(d)
                arr = d.split('.')
                if len(arr) > 2:
                    raise ValueError("Decimal can take at most 1 '.' char")
                if len(d) > m + 1:
                    raise ValueError("Unsupported amount of precision for decimal, " + d + ", with precision, " + m)
                if len(arr[1]) > 4:
                    raise ValueError("Decimal only supports at most " + n + " digits past the decimal based on passed _validate_decimal params")
                return d

            def create_user(self, usr, psw):
                query = "INSERT INTO User VALUES (%s, %s, %s);"
                params = ["MedCorp", usr, psw]
                params[0] = self._validate_varchar(params[0])
                params[1] = self._validate_varchar(params[1])
                params[2] = self._validate_int(params[2])
                try:
                    self.rs.execute(query, tuple(params))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def update_user(self, usr, new_usr=None, new_psw=None):
                new_usr = new_usr if new_usr is not None else usr

                usr = self._validate_varchar(usr)
                new_usr = self._validate_varchar(new_usr)
                new_psw = self._validate_varchar(new_psw) if new_psw is not None else None

                try:
                    if new_psw is not None:
                        query = "UPDATE User SET user_name = %s, psw_hash_salted = %s " \
                                "WHERE client = %s AND user_name = %s;"
                        self.rs.execute(query, (new_usr, new_psw, "MedCorp", usr))
                    else:
                        query = "UPDATE User SET user_name = %s WHERE client = %s AND user_name = %s;"
                        self.rs.execute(query, (new_usr, "MedCorp", usr))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            # Creates an item and returns its ID (Primary Key identifier)
            def create_item(self):
                query = "INSERT INTO Inv_Item (client) VALUES (%s);"
                try:
                    self.rs.execute(query, ("MedCorp",))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

                query = "SELECT MAX(item_id) FROM Inv_Item;"
                try:
                    self.rs.execute(query)
                    for (m) in self.rs:
                        item_id = m
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

                return item_id[0]

            # Creates an server and returns its ID (Primary Key identifier)
            def create_server(self, name=None, ip_addr=None, ip_v=None, lid=None):

                name = self._validate_varchar(name) if name is not None else None
                # TODO Validate IP Address
                lid = self._validate_int(lid) if lid is not None else None

                query = "INSERT INTO Server () VALUES ();"
                try:
                    self.rs.execute(query)
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

                query = "SELECT MAX(id) FROM Server;"
                try:
                    self.rs.execute(query)
                    for (m) in rs:
                        server_id = m
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

                self.update_server(server_id, name=name, ip_addr=ip_addr, ip_v=ip_v, lid=lid)

                return server_id

            def update_server(self, sid, name=None, ip_addr=None, ip_v=None, lid=None):
                params = []
                query = "UPDATE Server SET "
                if name is not None:
                    name = self._validate_varchar(name)
                    query += "name = %s, "
                    params.append(name)
                if ip_addr is not None:
                    # TODO: Validate IP Addr
                    query += "ip_address = %s, "
                    params.append(ip_addr)
                if ip_v is not None:
                    # TODO: Validate IP Addr
                    query += "ip_version = %s, "
                    params.append(ip_v)
                if lid is not None:
                    lid = self._validate_int(lid)

                    query = "SELECT id FROM Location WHERE id=%s;"
                    try:
                        self.rs.execute(query, tuple(lid))
                        for (m) in rs:
                            server_id = m
                        self.rs.reset()
                    except mysql_connector_Error as err:
                        self.close()
                        raise err

                    if lid != m:
                        raise ValueError("Attempting to set location id field of server to nonexistent location id value")

                    query += "location_id = %s, "
                    params.append(lid)
                    
                query = query[:-2] + ' '
                query += "WHERE sid = %s;"
                params.append(sid)

                try:
                    self.rs.execute(query, tuple(params))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def create_vender(self, email, poc=None, baa=None, date=None):
                email = self._validate_varchar(email)
                poc = self._validate_varchar(poc) if poc is not None else None
                baa = self._validate_bool(baa) if baa is not None else None
                date = self._validate_date(date) if date is not None else None

                query = "INSERT INTO Vender (email) VALUES (%s);"
                try:
                    self.rs.execute(query, tuple(email))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err
                self.update_vender(email, poc=poc, baa=baa)

            def update_vender(self, email, new_email=None, poc=None, baa=None):
                email = self._validate_varchar(email)
                params = []
                query = "UPDATE Vender SET "
                if new_email is not None:
                    new_email = self._validate_varchar(new_email)
                    query += "email = %s, "
                    params.append(new_email)
                if poc is not None:
                    poc = self._validate_varchar(poc)
                    query += "poc = %s, "
                    params.append(poc)
                if baa is not None:
                    baa = self._validate_bool(baa)
                    query += "baa = %s, "
                    params.append(baa)
                query = query[:-2] + ' '
                query += "WHERE email = %s;"
                params.append(email)

                try:
                    self.rs.execute(query, tuple(params))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            # Creates an location and returns its ID (Primary Key identifier)
            def create_locataion(self, cloud=None, details=None, protection=None):
                cloud = self._validate_cloud_prem(cloud) if cloud is not None else None
                details = self._validate_varchar(details) if details is not None else None
                protection = self._validate_text(protection) if protection is not None else None

                query = "INSERT INTO Location () VALUES ();"
                try:
                    self.rs.execute(query)
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

                query = "SELECT MAX(id) FROM Location;"
                try:
                    self.rs.execute(query)
                    for (m) in rs:
                        lid = m
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

                self.update_location(lid, cloud=cloud, details=details, protection=protection)

                return lid

            def update_location(self, lid, cloud=None, details=None, protection=None):
                params = []
                query = "UPDATE Location SET "
                if cloud is not None:
                    cloud = self._validate_cloud_prem(cloud)
                    query += "cloud_prem = %s, "
                    params.append(cloud)
                if details is not None:
                    details = self._validate_varchar(details)
                    query += "details = %s, "
                    params.append(details)
                if protection is not None:
                    protection = self._validate_text(protection)
                    query += "protection = %s, "
                    params.append(protection)
                query = query[:-2] + ' '
                query += "WHERE lid = %s;"
                params.append(lid)

                try:
                    self.rs.execute(query, tuple(params))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_name(self, n, iid):
                n = self._validate_varchar(n)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET name = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (n, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_type(self, t, iid):
                t = self._validate_varchar(t)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET type = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (t, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_version(self, v, iid):
                v = self._validate_varchar(v)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET version = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (v, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_os(self, os, iid):
                os = self._validate_varchar(os)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET os = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (os, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_os_version(self, v, iid):
                v = self._validate_varchar(v)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET os_version = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (v, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_vender(self, e, iid):
                e = self._validate_varchar(e)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET vender = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (v, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_auto_log_off_freq(self, f, iid):
                ifid = self._validate_int(f)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET auto_log_off_freq = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (f, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_server(self, sid, iid):
                sid = self._validate_int(sid)

                query = "SELECT id FROM Server WHERE id=%s;"
                try:
                    self.rs.execute(query, tuple(sid))
                    for (m) in rs:
                        server_id = m
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

                if sid != m:
                    raise ValueError("Attempting to set server id field of item to nonexistent server id value")

                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET server = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (s, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_ephi(self, ephi, iid):
                ephi = self._validate_bool(ephi)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET ephi = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (ephi, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_ephi_encrypted(self, e, iid):
                e = self._validate_bool(e)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET ephi_encrypted = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (e, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_ephi_encr_method(self, m, iid):
                m = self._validate_varchar(m)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET ephi_encr_method = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (m, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_ephi_encr_tested(self, t, iid):
                t = self._validate_bool(t)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET ephi_encr_tested = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (t, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_interfaces_with(self, i, iid):
                t = self._validate_text(t)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET interfaces_with = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (i, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_user_auth_method(self, m, iid):
                m = self._validate_varchar(m)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET user_auth_method = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (m, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_app_auth_method(self, m, iid):
                m = self._validate_varchar(m)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET app_auth_method = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (m, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_psw_min_length(self, l, iid):
                l = self._validate_int(l)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET psw_min_len = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (l, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_psw_change_freq(self, f, iid):
                f = self._validate_int(f)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET psw_change_freq = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (f, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_dept(self, d, iid):
                d = self._validate_varchar(d)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET dept = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (d, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_space(self, s, iid):
                s = self._validate_varchar(s)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET space = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (s, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_date_last_ordered(self, d, iid):
                d = self._validate_date(d)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET date_last_ordered = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (d, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_purchase_price(self, p, iid):
                p = self._validate_decimal(p)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET purchase_price = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (p, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_warranty_expires(self, d, iid):
                d = self._validate_date(d)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET warranty_expires = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (d, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_item_condition(self, c, iid):
                c = self._validate_varchar(c)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET item_condition = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (c, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_quantity(self, n, iid):
                n = self._validate_int(n)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET quantity = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (n, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_asset_value(self, v, iid):
                v = self._validate_decimal(v)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET assset_value = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (v, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_model_num(self, n, iid):
                n = self._validate_varchar(n)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET model_num = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (n, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_notes(self, n, iid):
                n = self._validate_text(n)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET notes = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (n, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_link(self, l, iid):
                l = self._validate_varchar(l)
                iid = self._validate_int(iid)

                query = "UPDATE Inv_Item SET link = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (l, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def close(self):
                self.rs.close()
                self.con.close()

        self.api_obj = Connector()
        return self.api_obj

    def __exit__(self, exc_type, exc_val, exc_tb):
        self.api_obj.close()

if __name__ == '__main__':
    with DBAPI() as c:
        print(c.create_item())
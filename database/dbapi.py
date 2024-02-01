'''
'''

import mysql.connector as mc
from mysql.connector import Error as mysql_connector_Error
from mysql.connector import errorcode
from database.config import config
# from config import config # for in-file testing

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
                        raise ValueError("Int " + str(i) + " too large for type UNSIGNED INT")
                if not signed and size=="BIGINT":
                    if 0 <= i and i < 2**64:
                        return i
                    else:
                        raise ValueError("Int " + str(i) + " too large for type UNSIGNED BIGINT")
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
                    raise ValueError("Date, " + d + ", does not allow day " + str(arr[2]) + " for month " + str(arr[1]))
                elif arr[1] == 2 and not (arr[2] <= 28 or arr[0] % 4 == 0 and arr[2] == 29):
                    raise ValueError("Date, " + d + ", does not allow day " + str(arr[2]) + " for month " + str(arr[1]) + " year " + str(arr[0]))
                    
                return d

            def _validate_cloud_prem(self, v):
                v = str(v)

                if v == "Cloud" or v == "On-Premise":
                    return v
                raise TypeError("cloud_prem needs to be of value \"Cloud\" or \"On-Premise\"")
            
            def _validate_text(self, t, size="MEDIUMTEXT"):
                t = str(t)
                if size=="MEDIUMTEXT":
                    if len(t) < 2**24:
                        return t
                    else:
                        raise ValueError("TEXT " + t + " too large for type MEDIUMTEXT")
                else:
                    raise NotImplementedError("_validate_text does not support params, size=\"" + size + "\"")

            def _validate_ip_address(self, addr, version):
                version = str(version) if version is not None else None
                if (version != "IPv4" and version != "IPv6") and version is not None:
                    raise TypeError("ip_version requires value of \"IPv4\" or \"IPv6\"")
                if version == "IPv4":
                    try:
                        addr = self._validate_int(addr) if addr is not None else None
                    except ValueError:
                        raise ValueError("IPv4 requires unsigned address < 2^32 (" + addr + " given)")
                else: # version == "IPv6" or version is None
                    try:
                        addr = self._validate_int(addr, size="BIGINT") if addr is not None else None
                    except ValueError:
                        if version == "IPv6":
                            raise ValueError("IPv6 requires unsigned address < 2^64 (" + addr + " given)")
                        else:
                            raise ValueError("IP address requires unsigned address < 2^64 (" + addr + " given)")
                return addr, version

            def _validate_decimal(self, d, m=13, n=4):
                d = str(d)
                arr = d.split('.')
                if len(arr) > 2:
                    raise ValueError("Decimal can take at most 1 '.' char")
                if len(d) > m + 1:
                    raise ValueError("Unsupported amount of precision for decimal, " + d + ", with precision, " + m)
                if len(arr) > 1 and len(arr[1]) > 4:
                    raise ValueError("Decimal only supports at most " + n + " digits past the decimal based on passed _validate_decimal params")
                return d

            # validates int within this method, too
            def _validate_item(self, i):
                i = self._validate_int(i)
                query = "SELECT MAX(item_id) FROM Inv_Item;"
                m = None
                try:
                    self.rs.execute(query)
                    for (m) in self.rs:
                        m = m[0]
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err
                if m is None or i > m:
                    raise ValueError("Attempting to update nonexistant item id " + str(i) + " (max item_id is " + str(m) + ")")
                return i

            # TODO: consider removing
            def create_user(self, usr, psw):
                query = "INSERT INTO User VALUES (%s, %s, %s);"
                params = [self.client, usr, psw]
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

            # TODO: consider limiting to curr user
            def update_user(self, usr, new_usr=None, new_psw=None):
                new_usr = new_usr if new_usr is not None else usr

                usr = self._validate_varchar(usr)
                new_usr = self._validate_varchar(new_usr)
                new_psw = self._validate_varchar(new_psw) if new_psw is not None else None

                query = "SELECT user_name FROM User WHERE user_name = %s AND client = %s;"
                m = None
                try:
                    self.rs.execute(query, (usr, self.client))
                    for (m) in self.rs:
                        m = m[0]
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err
                if m is None:
                    raise ValueError(usr + " not in User table; cannot update none existent user")

                try:
                    if new_psw is not None:
                        query = "UPDATE User SET user_name = %s, psw_hash_salted = %s " \
                                "WHERE client = %s AND user_name = %s;"
                        self.rs.execute(query, (new_usr, new_psw, self.client, usr))
                    else:
                        query = "UPDATE User SET user_name = %s WHERE client = %s AND user_name = %s;"
                        self.rs.execute(query, (new_usr, self.client, usr))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            # Creates an item and returns its ID (Primary Key identifier)
            def create_item(self):
                query = "INSERT INTO Inv_Item (client) VALUES (%s);"
                try:
                    self.rs.execute(query, (self.client,))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

                query = "SELECT MAX(item_id) FROM Inv_Item;"
                try:
                    self.rs.execute(query)
                    for (m) in self.rs:
                        item_id = m[0]
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

                return item_id

            # Creates an server and returns its ID (Primary Key identifier)
            def create_server(self, name=None, ip_addr=None, ip_v=None, lid=None):

                name = self._validate_varchar(name) if name is not None else None
                ip_addr, ip_v = self._validate_ip_address(ip_addr, ip_v)
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
                    for (m) in self.rs:
                        server_id = m[0]
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

                self.update_server(server_id, name=name, ip_addr=ip_addr, ip_v=ip_v, lid=lid)

                return server_id

            def update_server(self, sid, name=None, ip_addr=None, ip_v=None, lid=None):
                sid = self._validate_int(sid)

                query = "SELECT id FROM Server WHERE id = %s;"
                m = None
                try:
                    self.rs.execute(query, (sid,))
                    for (m) in self.rs:
                        m = m[0]
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err
                if m is None:
                    raise ValueError("Attempting to update nonexistant server sid " + str(sid) + " (max sid is " + str(m) + ")")

                params = []
                query = "UPDATE Server SET id = %s, "
                params.append(sid)
                if name is not None:
                    name = self._validate_varchar(name)
                    query += "name = %s, "
                    params.append(name)
                if ip_addr is not None:
                    ip_addr, _ = self._validate_ip_address(ip_addr, ip_v)
                    query += "ip_address = %s, "
                    params.append(ip_addr)
                if ip_v is not None:
                    _, ip_v = self._validate_ip_address(ip_addr, ip_v)
                    query += "ip_version = %s, "
                    params.append(ip_v)
                if lid is not None:
                    lid = self._validate_int(lid)

                    query_ = "SELECT id FROM Location WHERE id=%s;"
                    try:
                        self.rs.execute(query_, (lid, ))
                        for (m) in self.rs:
                            server_id = m[0]
                        self.rs.reset()
                    except mysql_connector_Error as err:
                        self.close()
                        raise err

                    if lid > server_id:
                        raise ValueError("Attempting to set location id field of server to nonexistent location id value")

                    query += "location_id = %s, "
                    params.append(lid)
                    
                query = query[:-2] + ' '
                query += "WHERE id = %s;"
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

                query = "SELECT email FROM Vender WHERE email = %s;"
                m = None
                try:
                    self.rs.execute(query, (email,))
                    for (m) in self.rs:
                        m = m[0]
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err
                if m is not None:
                    raise ValueError(email + " already in table")

                query = "INSERT INTO Vender (email) VALUES (%s);"
                try:
                    self.rs.execute(query, (email,))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err
                self.update_vender(email, poc=poc, baa=baa, date=date)

            def update_vender(self, email, new_email=None, poc=None, baa=None, date=None):
                email = self._validate_varchar(email)

                query = "SELECT email FROM Vender WHERE email = %s;"
                m = None
                try:
                    self.rs.execute(query, (email,))
                    for (m) in self.rs:
                        m = m[0]
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err
                if m is None:
                    raise ValueError(email + " not in table")

                params = []
                query = "UPDATE Vender SET "
                if new_email is not None:
                    new_email = self._validate_varchar(new_email)
                    query += "email = %s, "
                    params.append(new_email)
                else:
                    query += "email = %s, "
                    params.append(email)
                if poc is not None:
                    poc = self._validate_varchar(poc)
                    query += "poc = %s, "
                    params.append(poc)
                if baa is not None:
                    baa = self._validate_bool(baa)
                    query += "baa = %s, "
                    params.append(baa)
                if date is not None:
                    date = self._validate_date(date)
                    query += "date = %s, "
                    params.append(date)
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
                    for (m) in self.rs:
                        lid = m[0]
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err
                self.update_location(lid, cloud=cloud, details=details, protection=protection)

                return lid

            def update_location(self, lid, cloud=None, details=None, protection=None):
                lid = self._validate_int(lid)
                
                query = "SELECT id FROM Location WHERE id = %s;"
                m = None
                try:
                    self.rs.execute(query, (lid,))
                    for (m) in self.rs:
                        m = m[0]
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err
                if m is None:
                    raise ValueError("Attempting to update nonexistant location lid " + str(lid) + " (max lid is " + str(m) + ")")
               
                params = []
                query = "UPDATE Location SET id = %s, "
                params.append(lid)
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
                query += "WHERE id = %s;"
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
                iid = self._validate_item(iid)

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
                iid = self._validate_item(iid)

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
                iid = self._validate_item(iid)

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
                iid = self._validate_item(iid)

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
                iid = self._validate_item(iid)

                query = "UPDATE Inv_Item SET os_version = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (v, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_mac(self, m, iid):
                m = self._validate_int(m, size="BIGINT")
                iid = self._validate_item(iid)

                query = "UPDATE Inv_Item SET mac = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (m, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_ports(self, p, iid):
                p = self._validate_varchar(p)
                iid = self._validate_item(iid)

                query = "UPDATE Inv_Item SET ports = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (p, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_protocols(self, p, iid):
                p = self._validate_varchar(p)
                iid = self._validate_item(iid)

                query = "UPDATE Inv_Item SET protocols = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (p, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_statuses(self, s, iid):
                s = self._validate_varchar(s)
                iid = self._validate_item(iid)

                query = "UPDATE Inv_Item SET statuses = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (s, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_services(self, s, iid):
                s = self._validate_varchar(s)
                iid = self._validate_item(iid)

                query = "UPDATE Inv_Item SET services = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (s, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_services_versions(self, sv, iid):
                sv = self._validate_varchar(sv)
                iid = self._validate_item(iid)

                query = "UPDATE Inv_Item SET services_versions = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (sv, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_vender(self, e, iid):
                e = self._validate_varchar(e)
                iid = self._validate_item(iid)

                query = "SELECT email FROM Vender WHERE email = %s;"
                m = None
                try:
                    self.rs.execute(query, (e,))
                    for (m) in self.rs:
                        m = m[0]
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err
                if m is None:
                    raise ValueError(e + " not a valid vender")

                query = "UPDATE Inv_Item SET vender = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (e, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_auto_log_off_freq(self, f, iid):
                ifid = self._validate_int(f)
                iid = self._validate_item(iid)

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
                iid = self._validate_item(iid)

                query = "SELECT id FROM Server WHERE id=%s;"
                m = None
                try:
                    self.rs.execute(query, (sid,))
                    for (m) in self.rs:
                        server_id = m[0]
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err
                if m is None:
                    raise ValueError("Attempting to set server id field of item to nonexistent server id value")

                query = "UPDATE Inv_Item SET server = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (sid, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_ephi(self, ephi, iid):
                ephi = self._validate_bool(ephi)
                iid = self._validate_item(iid)

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
                iid = self._validate_item(iid)

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
                iid = self._validate_item(iid)

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
                iid = self._validate_item(iid)

                query = "UPDATE Inv_Item SET ephi_encr_tested = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (t, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_interfaces_with(self, t, iid):
                t = self._validate_text(t)
                iid = self._validate_item(iid)

                query = "UPDATE Inv_Item SET interfaces_with = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (t, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def set_user_auth_method(self, m, iid):
                m = self._validate_varchar(m)
                iid = self._validate_item(iid)

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
                iid = self._validate_item(iid)

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
                iid = self._validate_item(iid)

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
                iid = self._validate_item(iid)

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
                iid = self._validate_item(iid)

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
                iid = self._validate_item(iid)

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
                iid = self._validate_item(iid)

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
                iid = self._validate_item(iid)

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
                iid = self._validate_item(iid)

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
                iid = self._validate_item(iid)

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
                iid = self._validate_item(iid)

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
                iid = self._validate_item(iid)

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
                iid = self._validate_item(iid)

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
                iid = self._validate_item(iid)

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
                iid = self._validate_item(iid)

                query = "UPDATE Inv_Item SET link = %s WHERE item_id = %s;"
                try:
                    self.rs.execute(query, (l, iid))
                    self.con.commit()
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

            def check_item_exist(self, name=None, type_=None, version=None,
                                 os=None, os_version=None, mac=None, ports=None,
                                 protocols=None, statuses=None, services=None,
                                 services_versions=None, vender=None,
                                 auto_log_off_freq=None, server=None, ephi=None,
                                 ephi_encrypted=None, ephi_encr_method=None,
                                 ephi_encr_tested=None, interfaces_with=None,
                                 user_auth_method=None, app_auth_method=None,
                                 psw_min_len=None, psw_change_freq=None, dept=None,
                                 space=None, date_last_ordered=None,
                                 purchase_price=None, warranty_expires=None,
                                 item_condition=None, quantity=None,
                                 assset_value=None, model_num=None, notes=None,
                                 link=None):
                params = []
                query = "SELECT id FROM Inv_Item WHERE "

                if name is not None:
                    name = self._validate_varchar(name)
                    query += "name = %s, "
                    params.append(name)
                if type_ is not None:
                    type_ = self._validate_varchar(type_)
                    query += "type = %s, "
                    params.append(type_)
                if version is not None:
                    version = self._validate_varchar(version)
                    query += "version = %s, "
                    params.append(version)
                if os is not None:
                    os = self._validate_varchar(os)
                    query += "os = %s, "
                    params.append(os)
                if os_version is not None:
                    os_version = self._validate_varchar(os_version)
                    query += "os_version = %s, "
                    params.append(os_version)
                if mac is not None:
                    mac = self._validate_int(mac, size="BIGINT")
                    query += "mac = %s, "
                    params.append(mac)
                if ports is not None:
                    ports = self._validate_varchar(ports)
                    query += "ports = %s, "
                    params.append(ports)
                if protocols is not None:
                    protocols = self._validate_varchar(protocols)
                    query += "protocols = %s, "
                    params.append(protocols)
                if statuses is not None:
                    statuses = self._validate_varchar(statuses)
                    query += "statuses = %s, "
                    params.append(statuses)
                if services is not None:
                    services = self._validate_varchar(services)
                    query += "services = %s, "
                    params.append(services)
                if services_versions is not None:
                    services_versions = self._validate_varchar(services_versions)
                    query += "services_versions = %s, "
                    params.append(services_versions)
                if vender is not None:
                    vender = self._validate_varchar(vender)
                    query += "vender = %s, "
                    params.append(vender)
                if auto_log_off_freq is not None:
                    auto_log_off_freq = self._validate_int(auto_log_off_freq)
                    query += "auto_log_off_freq = %s, "
                    params.append(auto_log_off_freq)
                if server is not None:
                    server = self._validate_int(server)
                    query += "server = %s, "
                    params.append(server)
                if ephi is not None:
                    ephi = self._validate_bool(ephi)
                    query += "ephi = %s, "
                    params.append(ephi)
                if ephi_encrypted is not None:
                    ephi_encrypted = self._validate_bool(ephi_encrypted)
                    query += "ephi_encrypted = %s, "
                    params.append(ephi_encrypted)
                if ephi_encr_method is not None:
                    ephi_encr_method = self._validate_varchar(ephi_encr_method)
                    query += "ephi_encr_method = %s, "
                    params.append(ephi_encr_method)
                if ephi_encr_tested is not None:
                    ephi_encr_tested = self._validate_bool(ephi_encr_tested)
                    query += "ephi_encr_tested = %s, "
                    params.append(ephi_encr_tested)
                if interfaces_with is not None:
                    interfaces_with = self._validate_text(interfaces_with)
                    query += "interfaces_with = %s, "
                    params.append(interfaces_with)
                if user_auth_method is not None:
                    user_auth_method = self._validate_varchar(user_auth_method)
                    query += "user_auth_method = %s, "
                    params.append(user_auth_method)
                if app_auth_method is not None:
                    app_auth_method = self._validate_varchar(app_auth_method)
                    query += "app_auth_method = %s, "
                    params.append(app_auth_method)
                if psw_min_len is not None:
                    psw_min_len = self._validate_int(psw_min_len)
                    query += "psw_min_len = %s, "
                    params.append(psw_min_len)
                if psw_change_freq is not None:
                    psw_change_freq = self._validate_int(psw_change_freq)
                    query += "psw_change_freq = %s, "
                    params.append(psw_change_freq)
                if dept is not None:
                    dept = self._validate_varchar(dept)
                    query += "dept = %s, "
                    params.append(dept)
                if space is not None:
                    space = self._validate_varchar(space)
                    query += "space = %s, "
                    params.append(space)
                if date_last_ordered is not None:
                    date_last_ordered = self._validate_date(date_last_ordered)
                    query += "date_last_ordered = %s, "
                    params.append(date_last_ordered)
                if purchase_price is not None:
                    purchase_price = self._validate_decimal(purchase_price)
                    query += "purchase_price = %s, "
                    params.append(purchase_price)
                if warranty_expires is not None:
                    warranty_expires = self._validate_date(warranty_expires)
                    query += "warranty_expires = %s, "
                    params.append(warranty_expires)
                if item_condition is not None:
                    item_condition = self._validate_varchar(item_condition)
                    query += "item_condition = %s, "
                    params.append(item_condition)
                if quantity is not None:
                    quantity = self._validate_int(quantity)
                    query += "quantity = %s, "
                    params.append(quantity)
                if assset_value is not None:
                    assset_value = self._validate_decimal(assset_value)
                    query += "assset_value = %s, "
                    params.append(assset_value)
                if model_num is not None:
                    model_num = self._validate_varchar(model_num)
                    query += "model_num = %s, "
                    params.append(model_num)
                if notes is not None:
                    notes = self._validate_text(notes)
                    query += "notes = %s, "
                    params.append(notes)
                if link is not None:
                    link = self._validate_varchar(link)
                    query += "link = %s, "
                    params.append(link)
                
                if "WHERE " == query[-6:]:
                    raise ValueError("check_item_exist() method called without \
                                      any arguments passed; cannot determine \
                                      existence of nothing.")
                
                query += "client = %s;"
                params.append(self.client)

                try:
                    self.rs.execute(query, tuple(params))
                    for (m) in self.rs:
                        m = m[0]
                    self.rs.reset()
                except mysql_connector_Error as err:
                    self.close()
                    raise err

                return True if m is not None else False

            def close(self):
                self.rs.close()
                self.con.close()

        self.api_obj = Connector()
        return self.api_obj

    def __exit__(self, exc_type, exc_val, exc_tb):
        self.api_obj.close()

if __name__ == '__main__':
    with DBAPI() as c:
        pass
-- /*
--  * File Name: dummy-data.sql
--  * 
--  * Description:
--  * This is the file containing dummy data for testing the database, UI, and
--  * other aspects of the application. It inserts various fake data into the database,
--  * convering all tables, and attempting to cover many edge cases.
--  * 
--  * @package MedcurityNetworkScanner
--  * @authors Brandon Huyck (bhuyck@zagmail.gonzaga.edu)
--  * @license 
--  * @version 1.0.0
--  * @link 
--  * @since 
--  * 
--  * Usage:
--  * This file can be uploaded into the MariaDB with the `source` command. Connect
--  * through the terminal with appropriate credentials, select the database, and
--  * upload. Change the values if desired.
--  * 
--  * Notes:
--  * - Additional notes or special instructions can be added here.
--  * 
--  * TODO:
--  * - List any pending tasks or improvements that are planned for future updates.
--  * 
--  */

INSERT INTO Client VALUES
    ("MedCorp"),
    ("GonMed"),
    ("Office of Dr. Todd Smith, MD"),
    ("MedTech");

INSERT INTO User VALUES
    ("MedCorp", "bhuyck1", "123"),
    ("MedCorp", "bhuyck2", "456"),
    ("GonMed", "bhuyck3", "123"),
    ("MedTech", "bhuyck", "789"),
    ("MedCorp", "testuser", "$2y$09$pyqGNb0LScJiGhizc72u5u3347wmmlJPlIrXPCm6xZR/GnpEylNeO"),
    ("admin", "testadmin", "$2y$09$NHy5D/zIxWY0LkBoyAhhue.6WGF4J5s5OjbYDUz6cV5Pq2v0deD8u");
    
INSERT INTO Vender VALUES
    ("mattm@gmail.com", "Matt M.", FALSE, '2023-10-31', "MedCorp"),
    ("mattm@yahoo.com", "Matt M.", FALSE, '2023-10-31', "MedCorp"),
    ("bobby@startup.co", "Bob Smith", TRUE, '2022-01-28', "MedCorp");

INSERT INTO Location (cloud_prem, details, protection) VALUES
    ("Cloud", "deets1", "protection-notes1"),
    ("On-Premise", "deets2", "notes-2"),
    ("Cloud", "deets", "notes");

INSERT INTO Server (name, ip_address, ip_version, location_id) VALUES
    ("Server-1", 1, "IPv4", 1),
    ("Server-1", 3, "IPv4", 2),
    ("Server-2", 4294967299, "IPv6", 1);

INSERT INTO Inv_Item (client, name, type, version, os,
    os_version, vender, auto_log_off_freq, server, ephi,
    ephi_encrypted, ephi_encr_method, ephi_encr_tested, interfaces_with, user_auth_method,
    app_auth_method, psw_min_len, psw_change_freq, dept, space,
    date_last_ordered, purchase_price, warranty_expires, item_condition, quantity,
    assset_value, model_num, notes, link) VALUES
    ("GonMed", "dev-1", "printer", "v1", "PrinterOS",
     "20.0.1", "bobby@startup.co", 60, 3, TRUE,
     TRUE, "method", FALSE, "list of apps", "password or faceID",
     "faceID or password", 16, 90, "CS", "PACCAR",
     '2023-09-01', 400.13, '2025-10-01', "Good", 5,
     30, "12.1", "lots of notes", "link to picture");

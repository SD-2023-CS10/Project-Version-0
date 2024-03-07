-- /*
--  * File Name: attempt-01.sql
--  * 
--  * Description:
--  * This is the main MariaDB/MySQL database schema file. It creates all the tables.
--  * Please refer to the /database/README.md for more detailed information.
--  * 
--  * @package MedcurityNetworkScanner
--  * @authors Brandon Huyck (bhuyck@zagmail.gonzaga.edu)
--  * @license 
--  * @version 1.0.0
--  * @link 
--  * @since 
--  * 
--  * Usage:
--  * This file can be uploaded to a MariaDB/MySQL database using the `source` command.
--  * 
--  * Notes:
--  * - Additional notes or special instructions can be added here.
--  * 
--  * TODO:
--  * - List any pending tasks or improvements that are planned for future updates.
--  * 
--  */

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS Client;
DROP TABLE IF EXISTS User;
DROP TABLE IF EXISTS Inv_Item;
DROP TABLE IF EXISTS Vender;
DROP TABLE IF EXISTS Server;
DROP TABLE IF EXISTS Location;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE Client (
    name VARCHAR(255) PRIMARY KEY
);

CREATE TABLE User (
    client VARCHAR(255) NOT NULL,
    user_name VARCHAR(255) PRIMARY KEY,
    psw_hash_salted VARCHAR(255) NOT NULL,
    FOREIGN KEY (client) REFERENCES Client(name)
);

CREATE TABLE Vender (
    -- id INT UNSIGNED AUTO NOT NULL,
    email VARCHAR(255) NOT NULL,
    poc VARCHAR(255),
    baa BOOL,
    date DATE,
    client VARCHAR(255) NOT NULL,
    PRIMARY KEY (email, client),
    FOREIGN KEY (client) REFERENCES Client(name)
);

CREATE TABLE Location (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cloud_prem ENUM('Cloud', 'On-Premise'),
    details VARCHAR(255), 
    protection MEDIUMTEXT
);

CREATE TABLE Server (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    ip_address BIGINT UNSIGNED,
    ip_version ENUM('IPv4', 'IPv6'),
    location_id INT UNSIGNED,
    CHECK (ip_address < 4294967296 OR ip_version='IPv6'),
    UNIQUE (ip_address, ip_version),
    FOREIGN KEY (location_id) REFERENCES Location(id)
);

-- break into smaller tables?
CREATE TABLE Inv_Item (
    item_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client VARCHAR(255) NOT NULL,
    
    name VARCHAR(255),
    type VARCHAR(255),
    version VARCHAR(255),
    os VARCHAR(255),
    os_version VARCHAR(255),

    mac BIGINT UNSIGNED,
    ports VARCHAR(255),
    protocols VARCHAR(255),
    statuses VARCHAR(255),
    services VARCHAR(255),
    services_versions VARCHAR(255),
    
    vender VARCHAR(255),
    auto_log_off_freq INT UNSIGNED,
    server INT UNSIGNED,

    ephi BOOL,
    ephi_encrypted BOOL,
    ephi_encr_method VARCHAR(255),
    ephi_encr_tested BOOL,
    interfaces_with MEDIUMTEXT, -- collection? references other Inv_Item entries? or varchar/MEDIUMTEXT text field?

    user_auth_method VARCHAR(255),
    app_auth_method VARCHAR(255),
    psw_min_len INT UNSIGNED,
    psw_change_freq INT UNSIGNED,

    dept VARCHAR(255),
    space VARCHAR(255),
    date_last_ordered DATE,
    purchase_price DECIMAL(13, 4), -- GAAP guideline
    warranty_expires DATE,
    item_condition VARCHAR(255), -- larger? enum?
    quantity INT UNSIGNED,
    assset_value DECIMAL(13, 4),
    model_num VARCHAR(255),
    notes MEDIUMTEXT,
    link VARCHAR(255),

    CHECK (mac < 281474976710656), -- 2^48, or 16^12

    FOREIGN KEY (client) REFERENCES Client(name),
    FOREIGN KEY (vender) REFERENCES Vender(email),
    FOREIGN KEY (server) REFERENCES Server(id)
);

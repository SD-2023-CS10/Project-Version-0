
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS Client;
DROP TABLE IF EXISTS User;
DROP TABLE IF EXISTS Inv_Item;
DROP TABLE IF EXISTS Vender;
DROP TABLE IF EXISTS Server;
DROP TABLE IF EXISTS Location;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE Client (
    id INT UNSIGNED AUTO NOT NULL,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE User (
    client_id INT UNSIGNED NOT NULL,
    user_name VARCHAR(255) NOT NULL,
    psw_hash_salted INT NOT NULL
);

-- break into smaller tables?
CREATE TABLE Inv_Item (
    client_id INT UNSIGNED NOT NULL,
    
    name VARCHAR(255),
    type VARCHAR(255),
    version VARCHAR(255),
    os VARCHAR(255),
    os_version VARCHAR(255),
    
    vender_id INT UNSIGNED,
    auto_log_off_freq INT UNSIGNED,
    server_id INT UNSIGNED,

    ephi BOOL,
    ephi_encrypted BOOL,
    ephi_encr_method VARCHAR(255),
    ephi_encr_tested BOOL,
    interfaces_with _______, -- collection? references other Inv_Item entries? or varchar/MEDIUMTEXT text field?

    user_auth_method VARCHAR(255),
    app_auth_method VARCHAR(255),
    psw_min_len INT,
    psw_change_freq INT UNSIGNED,

    dept VARCHAR(255),
    space VARCHAR(255),
    date_last_ordered DATE,
    purchase_price DECIMAL(13, 4), -- GAP guideline
    warranty_expires DATE,
    condition VARCHAR(255), -- larger? enum?
    quantity INT,
    assset_value DECIMAL(13, 4),
    model_num VARCHAR(255),
    notes MEDIUMTEXT,
    link VARCHAR(255)
);

CREATE TABLE Vender (
    id INT UNSIGNED AUTO NOT NULL,
    poc VARCHAR(255),
    email VARCHAR(255),
    baa BOOL,
    date DATE
);

CREATE TABLE Server (
    id INT UNSIGNED AUTO NOT NULL,
    name VARCHAR(255),
    ip_address BIGINT UNSIGNED,
    ip_version ENUM('IPv4', 'IPv6'),
    location_id INT UNSIGNED,
    CHECK (ip_address > 4294967295 OR ip_version='IPv4')
);

CREATE TABLE Location (
    id INT UNSIGNED AUTO NOT NULL,
    cloud_prem ENUM('Cloud', 'On-Premise'),
    details VARCHAR(255), 
    protection MEDIUMTEXT
);

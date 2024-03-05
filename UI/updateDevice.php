<!--
 * File Name: updateDevice.php
 * 
 * Description:
 * This is the main entry point of the application. It initializes the application
 * environment, loads the necessary resources, and routes the request to the appropriate
 * controller. This file also handles basic configuration settings and global declarations.
 * 
 * @package MedcurityNetworkScanner
 * @authors Jack Nealon (jnealon0805@gmail.com)
 * @license 
 * @version 1.0.0
 * @link 
 * @since 
 * 
 * Usage:
 * This file should be placed in the root directory of the application. It can be directly
 * accessed via the URL [Your Application's URL]. No modifications are necessary for basic
 * operation, but customization can be done by editing the configuration settings within.
 * 
 * Modifications:
 * [Date] - [Your Name] - Version [New Version Number] - [Description of Changes]
 * 
 * Notes:
 * - Additional notes or special instructions can be added here.
 * - Remember to update the version number and modification log with each change.
 * 
 * TODO:
 * - List any pending tasks or improvements that are planned for future updates.
 * 
 -->
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extracting parameters from the POST request
    $item_id = $_POST["item_id"];
    $rowIndex = $_POST["rowIndex"];
    $columnName = $_POST["columnName"];
    $cellValue = $_POST["cellValue"];

    // Add database connection and update logic here
    $config = parse_ini_file("./config.ini");
    $server = $config["servername"];
    $username = $config["username"];
    $password = $config["password"];
    $database = "gu_devices";

    $cn = mysqli_connect($server, $username, $password, $database);

    // Connection error check
    if (!$cn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $columnName = replaceStrings($columnName);

    // Assuming 'item_id' is the primary key column name
    $updateQuery = "UPDATE Inv_Item SET $columnName = ? WHERE item_id = ?";
    $st = $cn->prepare($updateQuery);

    // Determine the data type for binding parameters based on the column type
    $paramType = "";
    switch ($columnName) {
        case "client":
        case "name":
        case "type":
        case "version":
        case "os":
        case "os_version":
        case "ports":
        case "protocols":
        case "statuses":
        case "services":
        case "services_versions":
        case "vender":
        case "ephi_encr_method":
        case "interfaces_with":
        case "user_auth_method":
        case "app_auth_method":
        case "dept":
        case "space":
        case "item_condition":
        case "model_num":
        case "notes":
        case "link":
            $paramType = "s"; // String type
            break;

        case "mac":
        case "auto_log_off_freq":
        case "server":
        case "psw_min_len":
        case "psw_change_freq":
        case "quantity":
            $paramType = "i"; // Integer type
            break;

        case "ephi":
        case "ephi_encrypted":
        case "ephi_encr_tested":
            $paramType = "i"; // BOOL type stored as INT
            break;

        case "purchase_price":
        case "assset_value":
            $paramType = "d"; // Double type
            break;

        case "date_last_ordered":
        case "warranty_expires":
            $paramType = "s"; // Date type (string)
            break;

        default:
            $paramType = "s"; // Default to string if the type is not recognized
            break;
    }

    // Bind parameters based on the determined data type
    $st->bind_param($paramType . "i", $cellValue, $item_id); // Assuming 'item_id' is of integer type

    $st->execute();

    $st->close();
    $cn->close();

}

/*
* Name: replaceStrings()
* Purpose: Class constructor
* Inputs: string
* Output: string
* Notes: swaps out the name for database item name
*/
function replaceStrings($inputString) {
    $replacements = array(
        'Name' => 'name',
        'Type of Application/Device' => 'type',
        'APPLICATION Version in Place' => 'version',
        'Operating System & Version' => 'os os_version',
        'VENDOR POC' => 'vpoc',
        'POC E-mail' => 'vemail',
        'AUTOMATIC LOG-OFF FREQUENCY' => 'auto_log_off_freq',
        'BAA?' => 'baa',
        'DATE BAA SIGNED' => 'date'
    );

    foreach ($replacements as $key => $value) {
        $inputString = str_replace($key, $value, $inputString);
    }

    return $inputString;
}
?>
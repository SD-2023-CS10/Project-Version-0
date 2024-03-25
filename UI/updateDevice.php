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
    $cellValue = $_POST["cellValue"];
    $cellId = $_POST["cellId"];

    // Parsing cellId
    $idArray = explode(".", $cellId); 
    $tableName = $idArray[0];
    $item = $idArray[1];

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

    // Create update query
    $updateQuery = "UPDATE $tableName SET $item = ? WHERE item_id = ?";
    $st = $cn->prepare($updateQuery);

    // Bind parameters based on the determined data type
    $st->bind_param("si", $cellValue, $item_id); // Assuming 'item_id' is of integer type

    $st->execute();

    $st->close();
    $cn->close();
}
?>
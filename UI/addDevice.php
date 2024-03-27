<!--
 * File Name: addDevice.php
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
    $userDevice = $_POST["userDevice"];

    $CLIENT = "Med INC";

    // Add database connection and insertion logic here
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

    // Create query and send
    $insertQuery = "INSERT INTO Inv_Item (name, client) VALUES (?, ?)";
    $st = $cn->prepare($insertQuery);
    $st->bind_param("ss", $userDevice, $CLIENT);
    $st->execute();

    $st->close();
    $cn->close();
    
    // Redirect back to the main page after adding the device
    header("Location: index.php");
    exit();
}
?>
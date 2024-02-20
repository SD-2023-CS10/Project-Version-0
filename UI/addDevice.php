<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userDevice = $_POST["userDevice"];

    $CLIENT = "MedCorp";

    // Add database connection and insertion logic here
    $config = parse_ini_file("./config.ini");
    $server = $config["servername"];
    $username = $config["username"];
    $password = $config["password"];
    $database = "gu_devices";

    $cn = mysqli_connect($server, $username, $password, $database);

    if (!$cn) {
        die("Connection failed: " . mysqli_connect_error());
    }

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
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_id = $_POST["item_id"];

    // Add database connection and deletion logic here
    $config = parse_ini_file("./config.ini");
    $server = $config["servername"];
    $username = $config["username"];
    $password = $config["password"];
    $database = "gu_devices";

    $cn = mysqli_connect($server, $username, $password, $database);

    if (!$cn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $deleteQuery = "DELETE FROM Inv_Item WHERE item_id = ?";
    $st = $cn->prepare($deleteQuery);
    $st->bind_param("i", $item_id);
    $st->execute();

    $st->close();
    $cn->close();
}
?>
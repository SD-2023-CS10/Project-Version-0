<!-- * File Name: adminDelete.php
 * 
 * Description:
 * One half of the "back end" side of the adminActions.php page. The adminDelete.php file checks 
 * if the admin-entered credentials are found in the database. If not, an error screen indicating 
 * that the login was invalid pops up, otherwise, the admin is able to create new user accounts, 
 * setting their usernames and passwords, and a success message will pop up
 * 
 * @package MedcurityNetworkScanner
 * @authors Artis Nateephaisan (anateephaisan@zagmail.gonzaga.edu)
 * @license 
 * @version 1.0.3
 * @link 
 * @since 
 * 
 * Usage:
 * This file should be placed in the root directory of the application. It can be directly
 * accessed via the URL [Your Application's URL]. No modifications are necessary for basic
 * operation.
 * 
 * Modifications:
 * [4/20/24] - [Artis Nateephaisan] - Version [1.0.2] - [Added error message functionality]
 * [4/22/24] - [Artis Nateephaisan] - Version [1.0.3] - [Fixed bug regarding deleting user that did not match client]
 * 
 * Notes:
 * - Additional notes or special instructions can be added here.
 * - Remember to update the version number and modification log with each change.
 * 
 * TODO:
 * - List any pending tasks or improvements that are planned for future updates.
 * 
 */ -->
<?php

session_start();
ob_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // retrieve form data
    $username = $_POST["username"];
    $client = $_POST["client"];
} else {
    header("Location: adminCreate.html");
    exit();
}
// connection params
$config = parse_ini_file("./config.ini");
$server = $config["servername"];
$dbusername = $config["username"];
$dbpassword = $config["password"];
$database = "gu_devices";

// connect to db
$cn = mysqli_connect($server, $dbusername, $dbpassword, $database);

// check connection
if (!$cn) {
    die("Connection failed: " . mysqli_connect_error());
}

// initialize variable for sign in error from login.php
$_SESSION['delete_error'] = null;
$_SESSION['delete_success'] = null;

// set up the query to select for username
$query = "SELECT user_name FROM User WHERE user_name = ?;";

// set up the prepared statement
$st = $cn->stmt_init();
$st->prepare($query);

$st->bind_param("s", $username);

// execute statement and store result in $result1 for first param
$st->execute();

$st->bind_result($result1);

$st->fetch();

// set up the query to select client from username
$query = "SELECT client FROM User WHERE user_name = ?;";

// set up the prepared statement
$st = $cn->stmt_init();
$st->prepare($query);

$st->bind_param("s", $username);

// execute statement and store result in $result2 for second param
$st->execute();

$st->bind_result($result2);

$st->fetch();

// check for both the username and the client to ensure the username is tied with the client before deletion
if ($result1 == $username && $result2 == $client) {

    // delete the username on match of user_name and client info
    $query = "DELETE FROM User WHERE user_name = ? AND client = ?;";

    // set up the prepared statement
    $st = $cn->stmt_init();
    $st->prepare($query);

    $st->bind_param("ss", $username, $client);

    $st->execute();

    $_SESSION['delete_success'] = "Client user account successfully deleted!";
    header("Location: adminActions.php");
} else {
    // if there is no client with that username, error message
    $_SESSION['delete_error'] = "There are no instances of a username associated with that client.";
    header("Location: adminActions.php");
}

$st->close();
$cn->close();

?>
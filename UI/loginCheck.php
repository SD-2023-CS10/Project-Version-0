<!-- * File Name: loginCheck.php
 * 
 * Description:
 * The "back end" side of the login.html page. The loginCheck.php file checks if the user-entered credentials
 * are found in the database. If not, an error screen indicating that the login was invalid pops up, otherwise,
 * the user is able to proceed to the index.php page.
 * 
 * @package MedcurityNetworkScanner
 * @authors Artis Nateephaisan (anateephaisan@zagmail.gonzaga.edu)
 * @license 
 * @version 1.0.0
 * @link 
 * @since 
 * 
 * Usage:
 * This file should be placed in the root directory of the application. It can be directly
 * accessed via the URL [Your Application's URL]. No modifications are necessary for basic
 * operation.
 * 
 * Modifications:
 * [Date] - [Artis Nateephaisan] - Version [New Version Number] - [Description of Changes]
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
    $password = $_POST["password"];
} else {
    header("Location: login.php");
    exit();
}

// DB connection
$config = parse_ini_file("./config.ini");
$server = $config["servername"];
$dbusername = $config["username"];
$dbpassword = $config["password"];
$database = "gu_devices";

$cn = mysqli_connect($server, $dbusername, $dbpassword, $database);

// check connection
if (!$cn) {
    die("Connection failed: " . mysqli_connect_error());
}

// initialize variable for sign in error from login.php
$_SESSION['login_error'] = null;

// set up the query
$query = "SELECT psw_hash_salted FROM User WHERE user_name = ?";

// set up the prepared statement
$st = $cn->stmt_init();

$st->prepare($query);
$st->bind_param("s", $username);

// execute statement and store the psw_hash_salted in $result variable
$st->execute();

$st->bind_result($result);

$st->fetch();

// use password_verify to compare the password from the post with the hashed psw from the DB
if (password_verify($password, $result)) {
    // Use Sessions to transfer username to different PHP pages
    $_SESSION["session_user"] = $username;

    header("Location: index.php");

    exit();
} else {
    // password is incorrect
    $_SESSION['login_error'] = "Invalid login parameters, please try again.";
    header("Location: login.php");
    exit();
}

$st->close();
$cn->close();
?>
<!-- * File Name: changePasswordCheck.php
 * 
 * Description:
 * The "back end" side of the newUser.html page. The newUser.php file checks if the user-entered credentials
 * are found in the database. If not, an error screen indicating that the login was invalid pops up, otherwise,
 * the user is able to set their password to something else.
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
 * operation, but customization can be done by editing the configuration settings within.
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
    $old_password = $_POST["oldPassword"];
    $new_password = $_POST["newPassword"];

    // PASSWORD_DEFAULT is the default hashing algorithm for PHP that is updated with new PHP releases
    // array('cost') param takes the salt in form cost factor

    $new_psw_hash_salt = password_hash($new_password, PASSWORD_DEFAULT, array('cost' => 9));
} else {
    header("Location: newUser.html");
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
$_SESSION['null_user_error'] = null;

// set up the query
$query = "SELECT psw_hash_salted FROM User WHERE user_name = ?;";

// set up the prepared statement
$st = $cn->stmt_init();
$st->prepare($query);
$st->bind_param("s", $username);

// execute statement and store result in $result
$st->execute();

$st->bind_result($result);

$st->fetch();

// $st->store_result();

// check for it user login was successful
if (password_verify($old_password, $result)) {
    // login was successful

    $query = "UPDATE User SET psw_hash_salted = ? WHERE user_name = ?;";

    // set up the prepared statement
    $st = $cn->stmt_init();
    $st->prepare($query);

    $st->bind_param("ss", $new_psw_hash_salt, $username);

    // execute statement
    $st->execute();

    // once the password has been changed, go back to login page
    header("Location: login.php");
} else {

    // if username and old password params don't match, provide error message
    $_SESSION['null_user_error'] = "No existing user account with matching \"Username\" and \"Old Password\" parameters, please try again.";
    header("Location: changePassword.php");
    exit();
}

$st->close();
$cn->close();
?>
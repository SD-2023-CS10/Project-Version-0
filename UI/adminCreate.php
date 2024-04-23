<!-- * File Name: adminCreate.php
 * 
 * Description:
 * One half of the "back end" side of the adminCreate.php page. The adminCreate.php file checks 
 * if the admin-entered credentials are found in the database. If not, an error screen indicating that the 
 * login was invalid pops up in  adminLogin.php, otherwise, the admin is given the functionality to create new users,
 * which includes filling out their username (must be unique), password (which is to be changed by 
 * the client user later), and client company. Upon account creation, a success message pops up.
 * 
 * @package MedcurityNetworkScanner
 * @authors Artis Nateephaisan (anateephaisan@zagmail.gonzaga.edu)
 * @license 
 * @version 1.0.1
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
 * 4/20/24 - Artis Nateephaisan - Version 1.0.1 - Changed file type to php to support error messages
 *
 * Notes:
 * Please ensure that the username for the client user is unique, even if the username is unique to its respective
 * client. If the username is already found in the database (even if it belongs to another client), a new user will
 * not be created and an error message will pop up.
 * The password set up by Medcurity is meant to be sent to the client through email where they will then
 * change the password by accessing the "Change Password" page in the main login menu. This is to ensure that
 * Medcurity does not have access to their client users' passwords, providing security.
 * Client users will only see the network information only from the company they belong to.
 *
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
    $client = $_POST["client"];

    // here the password would have to be salted and hashed upon retrieval from the post form
    // PASSWORD_DEFAULT is the default hashing algorithm for PHP that is updated with new PHP releases
    // array('cost') param takes the salt in form cost factor

    $old_psw_hash_salt = password_hash($old_password, PASSWORD_DEFAULT, array('cost' => 9));
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

// initialize variables for sign in error from adminActions.php
$_SESSION['create_error'] = null;
$_SESSIONS['create_success'] = null;
$_SESSIONS['duplicate_account'] = null;

// set up the query
$query = "SELECT user_name FROM User WHERE user_name = ?;";

// set up the prepared statement
$st = $cn->stmt_init();
$st->prepare($query);
$st->bind_param("s", $username);

// execute statement and store result in $result
$st->execute();
$st->bind_result($result);
$st->fetch();


if ($result == $username) {
    // this would mean there is already a username in the DB, send an error message 
    $_SESSION['duplicate_account'] = "A client user account with that username already exists!";
    header("Location: adminActions.php");

    exit();
} else {
    $query = "SELECT name FROM Client WHERE name = ?;";

    // set up the prepared statement
    $st = $cn->stmt_init();
    $st->prepare($query);

    $st->bind_param("s", $client);

    // execute statement and store result in $result
    $st->execute();
    $st->bind_result($result);
    $st->fetch();

    // if the client company is found in the database, 
    if ($result == $client) {

        // insert the username into the User table under user_name column
        $query = "INSERT INTO User (client, user_name, psw_hash_salted) VALUES (?, ?, ?);";

        // set up the prepared statement
        $st = $cn->stmt_init();

        $st->prepare($query);

        $st->bind_param("sss", $client, $username, $old_psw_hash_salt);

        // execute statement 
        $st->execute();

        // post a success message to let the admin user know the client account was created
        $_SESSION['create_success'] = "Client user account created!";
        header("Location: adminActions.php");
    } else {
        // if the client company isn't in the Client table in the DB yet

        // add the client company to the Client table in the DB
        $query = "INSERT INTO Client (name) VALUES (?);";

        // set up the prepared statement
        $st = $cn->stmt_init();

        $st->prepare($query);

        $st->bind_param("s", $client);

        // execute statement 
        $st->execute();

        // after the client company is in the DB, then the client user account can be created

        // insert the username into the User table under user_name column
        $query = "INSERT INTO User (client, user_name, psw_hash_salted) VALUES (?, ?, ?);";

        // set up the prepared statement
        $st = $cn->stmt_init();

        $st->prepare($query);

        $st->bind_param("sss", $client, $username, $old_psw_hash_salt);

        // execute statement 
        $st->execute();

        // post a success message to let the admin user know the client account was created
        $_SESSION['create_success'] = "Client user account created!";
        header("Location: adminActions.php");
    }
}

$st->close();
$cn->close();

?>
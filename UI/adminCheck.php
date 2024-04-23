<!-- * File Name: adminCheck.php
 * 
 * Description:
 * The "back end" side of the adminLogin.php page. The adminCheck.php file checks if the admin-entered credentials
 * are found in the database. If not, an error message indicating that the login was invalid pops up, otherwise,
 * the admin is able to proceed to the adminCreate.php page.
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
 * [4/22/24] - [Artis Nateephaisan] - Version [1.0.3] - [Fixed unreachable code bug]
 * 
 * Notes:
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
    header("Location: adminLogin.php");
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

// set up the query to check DB for correct login credentials. This query is different from loginCheck.php
// because it uses the client attribute to check if the user is an "admin" client.

$query = "SELECT psw_hash_salted FROM User WHERE user_name = ? AND client = 'admin';";

// set up the prepared statement
$st = $cn->stmt_init();
$st->prepare($query);
$st->bind_param("s", $username);

// execute statement and store result in $result
$st->execute();

$st->bind_result($result);

$st->fetch();

// check for if admin login was successful
if (password_verify($password, $result)) {
    // login was successful

    // no success message needed, they will simply be grantedaccess the adminActions.php page
    header("Location: adminActions.php");
} else {
    // login was unsuccessful

    // automatically reloads the page and display error message indicating invalid login parameters
    $_SESSION['login_error'] = "Invalid login parameters, please try again.";
    header("Location: adminLogin.php");

    exit();
}

$st->close();
$cn->close();

?>
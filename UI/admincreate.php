<!-- * File Name: adminCreate.php
 * 
 * Description:
 * The "back end" side of the adminCreate.html page. The adminCreate.php file checks if the admin-entered credentials
 * are found in the database. If not, an error screen indicating that the login was invalid pops up, otherwise,
 * the admin is able to delete old user accounts, erasing their login information from the database.
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

if($_SERVER["REQUEST_METHOD"] == "POST") {

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
$cn = mysqli_connect($server , $dbusername , $dbpassword , $database);

// check connection
if (!$cn) {
    die("Connection failed: " . mysqli_connect_error ());
}

// // set up the query
// $query = "SELECT user_name, psw_hash_salted FROM User WHERE user_name = ? AND psw_hash_salted = ?;";

// set up the query
$query = "SELECT user_name FROM User WHERE user_name = ?;";

// set up the prepared statement
$st = $cn ->stmt_init();
$st ->prepare($query);
$st ->bind_param("s", $username);

// execute statement and store result in $result
$st ->execute();
$st ->bind_result($result);
// this would be null because if the admin is inputting a new user, then the $username param would have a new
// user not found in the DB and the DB won't recognize it, so the lines after line 87 would not run
$st ->fetch();
// $st->store_result();

// $result = $cn->query($query);

if ($result == $username) {
    // this would mean there is already a username in the DB
    // this needs to be fixed
    // $html = preg_replace('#<div class="invisible">(.*?)</h3>#', '', $html);
    header("Location: adminCreate.html");
    exit();
} 
else 
{
    $query = "SELECT name FROM Client WHERE name = ?;";

    // set up the prepared statement
    $st = $cn ->stmt_init();
    $st ->prepare($query);

    $st ->bind_param("s", $client);

    // execute statement and store result in $result
    $st ->execute();
    $st ->bind_result($result);
    $st ->fetch();

    if ($result == $client) {

        // insert the username into the User table under user_name column
        $query = "INSERT INTO User (client, user_name, psw_hash_salted) VALUES (?, ?, ?);";

        // set up the prepared statement
        $st = $cn ->stmt_init();

        $st ->prepare($query);
        $st ->bind_param("sss", $client, $username, $old_psw_hash_salt);

        // execute statement 
        $st ->execute();

    } 
    else {
        // need an if to check if client is in the DB, if it is, continue with normal query, if it isn't add the 
        // client in the table
        $query = "INSERT INTO Client (name) VALUES (?);";

        // set up the prepared statement
        $st = $cn ->stmt_init();

        $st ->prepare($query);
        $st ->bind_param("s", $client);

        // execute statement 
        $st ->execute();

        // insert the username into the User table under user_name column
        $query = "INSERT INTO User (client, user_name, psw_hash_salted) VALUES (?, ?, ?);";

        // set up the prepared statement
        $st = $cn ->stmt_init();

        $st ->prepare($query);
        $st ->bind_param("sss", $client, $username, $old_psw_hash_salt);

        // execute statement 
        $st ->execute();
    }
}

 $st->close();
 $cn->close();

?>
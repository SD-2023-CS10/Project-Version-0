<!-- * File Name: adminDelete.php
 * 
 * Description:
 * The "back end" side of the adminCreate.html page. The adminDelete.php file checks if the admin-entered credentials
 * are found in the database. If not, an error screen indicating that the login was invalid pops up, otherwise,
 * the admin is able to create new user accounts, setting their usernames and passwords.
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


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // retrieve form data
    $username = $_POST["username"];
    // $old_password = $_POST["old password"];
    $client = $_POST["client"];

    // here the password would have to be salted and hashed upon retrieval from the post form
    // PASSWORD_DEFAULT is the default hashing algorithm for PHP that is updated with new PHP releases
    // array('cost') param takes the salt in form cost factor

    // note that this cannot be tested right now because the password stored in the DB are not hashed and salted
    // $old_psw_hash_salt = password_hash($old_password, PASSWORD_DEFAULT, array('cost' => 9));
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
// $query = "SELECT user_name, psw_hash_salted FROM User WHERE user_name = ?;";

// set up the query
$query = "SELECT user_name FROM User WHERE user_name = ?;";

// set up the prepared statement
$st = $cn ->stmt_init();
$st ->prepare($query);

// Does this work? While the old_password could be an inject, it should get hashed and salted, so the injection shouldnt work.
$st ->bind_param("s", $username);

// execute statement and store result in $result
$st ->execute();
$st ->bind_result($result);
$st ->fetch();
// $st->store_result();
// $result = $cn->query($query);

// check for if the exact same username/password params have already been in the database
if ($result == $username) {

    // insert the username into the User table under user_name column
    $query = "DELETE FROM User WHERE user_name = ? AND client = ?;";

    // set up the prepared statement
    $st = $cn ->stmt_init();
    $st ->prepare($query);

    $st ->bind_param("ss", $username, $client);

    // execute statement and store result in $result
    $st ->execute();
    // $st ->bind_result($result);
 } else {
    // if there is no client with that username, error message
    // $html = preg_replace('#<div class="invisible">(.*?)</h3>#', '', $html);
    header("Location: adminCreate.html");
    exit();
 }

 $st->close();
 $cn->close();

?>
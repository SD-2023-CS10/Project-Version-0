<!-- * File Name: newUser.php
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


    if($_SERVER["REQUEST_METHOD"] == "POST") {

        // retrieve form data
        $username = $_POST["username"];
        $old_password = $_POST["old password"];
        $new_password = $_POST["new password"];

        // here the password would have to be salted and hashed upon retrieval from the post form
        // PASSWORD_DEFAULT is the default hashing algorithm for PHP that is updated with new PHP releases
        // array('cost') param takes the salt in form cost factor
        
        // note that this cannot be tested right now because the password stored in the DB are not hashed and salted
        // $old_psw_hash_salt = password_hash($old_password, PASSWORD_DEFAULT, array('cost' => 9));

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
    $cn = mysqli_connect($server , $dbusername , $dbpassword , $database);

    // check connection
    if (!$cn) {
        die("Connection failed: " . mysqli_connect_error ());
    }

    // // set up the query
    // $query = "SELECT user_name, psw_hash_salted FROM User WHERE user_name = ? AND psw_hash_salted = ?;";

    // set up the query
    $query = "SELECT psw_hash_salted FROM User WHERE user_name = ?;";

    // set up the prepared statement
    $st = $cn ->stmt_init();
    $st ->prepare($query);
    // I need to set up the statements here but I need to use old_psw_hash_salt, which isn't from POST
    $st ->bind_param("s", $username);
 
    // execute statement and store result in $result
    $st ->execute();
    $st ->bind_result($result);
    // $st->store_result();

    // check for it user login was successful
    if (password_verify($password, $result)) {
        // login was successful

        // TODO: I will have to UPDATE the new hashed and salted password into the DB
        $query = "UPDATE User SET psw_hash_salted = $new_psw_hash_salt WHERE user_name = ?;";

        // set up the prepared statement
        $st = $cn ->stmt_init();
        $st ->prepare($query);

        $st ->bind_param("s", $username);

        // execute statement
        $st ->execute();

        // I should echo that new password has been set
        
     } else {
        
        $html = preg_replace('#<div class="invisible">(.*?)</h3>#', '', $html);   
        exit();
     }

     $st->close();
     $cn->close();
?>
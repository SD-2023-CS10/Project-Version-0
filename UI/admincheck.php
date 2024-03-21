<!-- * File Name: adminCheck.php
 * 
 * Description:
 * The "back end" side of the adminLogin.html page. The adminCheck.php file checks if the admin-entered credentials
 * are found in the database. If not, an error screen indicating that the login was invalid pops up, otherwise,
 * the admin is able to proceed to the adminCreate.html page.
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
        $password = $_POST["password"];

        // here the password would have to be salted and hashed upon retrieval from the post form
        // PASSWORD_DEFAULT is the default hashing algorithm for PHP that is updated with new PHP releases
        // array('cost') param takes the salt in form cost factor
        
        // note that this cannot be tested right now because the password stored in the DB are not hashed nor salted
        $psw_hash_salt = password_hash($password, PASSWORD_DEFAULT, array('cost' => 9));
    } else {
        header("Location: adminLogin.html");
        exit();
    }

    // DB connection
    $config = parse_ini_file("./config.ini");
    $server = $config["servername"];
    $dbusername = $config["username"];
    $dbpassword = $config["password"];
    $database = "gu_devices";

    $cn = mysqli_connect($server , $dbusername , $dbpassword , $database);

    // check connection
    if (!$cn) {
        die("Connection failed: " . mysqli_connect_error ());
    }
    
    
    // // set up the query to check DB for correct login credentials. This query is different from loginCheck.php
    // // because it uses the client attribute to check if the user is an "admin" client.
    // $query = "SELECT user_name, psw_hash_salted FROM User WHERE user_name='?' AND psw_hash_salted = '?' AND client='admin';";

    $query = "SELECT psw_hash_salted FROM User WHERE user_name = ? AND client = 'admin';";

    // set up the prepared statement
    $st = $cn ->stmt_init();
    $st ->prepare($query);
    $st ->bind_param("s", $username);

    // execute statement and store result in $result
    $st ->execute();

    $st ->bind_result($result);

    // $result = $cn->query($query);

    // $st->store_result();

    // check for if admin login was successful
    if (password_verify($password, $result)) {
        // login was successful

        
        header("Location: adminCreate.html");
        exit();
    } 
    else {
        // login was unsuccessful
        // header("Location: error.html")
        $html = preg_replace('#<div class="invisible">(.*?)</h3>#', '', $html);
        exit();

        // if possible, display popup that login info was wrong, otherwise, can redirect back to login page
    }

    // probably because the if else statement would take the user to a different page. The st and cn would have to be closed there instead of at the end of the file
    $st->close();
    $cn->close();
    
?>
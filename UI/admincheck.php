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
    
    
    // set up the query
    $query = "SELECT * FROM User WHERE user_name='?' AND psw_hash_salted = '?';";

    // set up the prepared statement
    $st = $cn ->stmt_init();
    $st ->prepare($query);
    $st ->bind_param("ss", $_POST["username"], $psw_hash_salt);

    // execute statement and store result in $result
    $st ->execute();
    $st ->bind_result($result);

    // $result = $cn->query($query)

    // check for if admin login was successful
    if ($result->num_rows == 1) {
        // login was successful

        
        header("Location: admincreate.html");
        exit();
    } 
    else {
        // login was unsuccessful
        // header("Location: error.html")
        $html = preg_replace('#<div class="invisible">(.*?)</h3>#', '', $html);
        exit();

        // if possible, display popup that login info was wrong, otherwise, can redirect back to login page
    }

    $st->close();
    $cn->close();
    
?>
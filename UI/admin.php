<?php


    if($_SERVER["REQUEST_METHOD"] == "POST") {

        // retrieve form data
        $username = $_POST["username"];
        $old_password = $_POST["old password"];

        // here the password would have to be salted and hashed upon retrieval from the post form
        // PASSWORD_DEFAULT is the default hashing algorithm for PHP that is updated with new PHP releases
        // array('cost') param takes the salt in form cost factor
        
        // note that this cannot be tested right now because the password stored in the DB are not hashed and salted
        $old_psw_hash_salt = password_hash($old_password, PASSWORD_DEFAULT, array('cost' => 9));
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

    // set up the query
    $query = "SELECT * FROM User WHERE user_name = ? AND psw_hash_salted = ?;";

    // set up the prepared statement
    $st = $cn ->stmt_init();
    $st ->prepare($query);
    // Does this work? While the old_password could be an inject, it should get hashed and salted, so the injection shouldnt work.
    $st ->bind_param("ss", $_POST["username"], $old_psw_hash_salt);
 
    // execute statement and store result in $result
    $st ->execute();
    $st ->bind_result($result);

    // check for if the exact same username/password params have already been in the database
    if ($result->num_rows == 0) {

        // insert the username into the User table under user_name column
        $query = "INSERT INTO User.user_name VALUES ?';";

        // set up the prepared statement
        $st = $cn ->stmt_init();
        $st ->prepare($query);
        
        $st ->bind_param("s", $_POST["username"]);
 
        // execute statement and store result in $result
        $st ->execute();
        $st ->bind_result($result);


        // insert the hashed and salted into the User table under psw_hash_salted column
        $query = "INSERT INTO User.psw_hash_salted VALUES $new_psw_hash_salt';";

        // set up the prepared statement
        $st = $cn ->stmt_init();
        $st ->prepare($query);
 
        // execute statement and store result in $result
        $st ->execute();
        $st ->bind_result($result);

     } else if ($result->num_rows >= 1) {
        // if there is already one or more instances of a user with these exact params, make an error message popup
        exit();
     } else {
        // this should never happen
        exit();
     }

     $st->close();
     $cn->close();

?>
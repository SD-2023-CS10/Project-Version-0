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
        $old_psw_hash_salt = password_hash($old_password, PASSWORD_DEFAULT, array('cost' => 9));

        $new_psw_hash_salt = password_hash($new_password, PASSWORD_DEFAULT, array('cost' => 9));
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
    $query = "SELECT * FROM User WHERE user_name='$username' AND psw_hash_salted='$old_psw_hash_salt';";

    // set up the prepared statement
    $st = $cn ->stmt_init();
    $st ->prepare($q);
 
    // execute statement and store result in $result
    $st ->execute();
    $st ->bind_result($result);


    if($result->num_rows == 1) {
        // login was successful

        // TODO: I will have to insert the new hashed and salted password into the DB
        $query = "INSERT';";

        // set up the prepared statement
        $st = $cn ->stmt_init();
        $st ->prepare($q);
 
        // execute statement and store result in $result
        $st ->execute();
        $st ->bind_result($result);


        
     } else {

     }



?>
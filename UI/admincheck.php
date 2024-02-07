<?php

    if($_SERVER["REQUEST_METHOD"] == "POST") {

        // retrieve form data
        $username = $_POST["username"];
        $password = $_POST["password"];

        // here the password would have to be salted and hashed upon retrieval from the post form
        // PASSWORD_DEFAULT is the default hashing algorithm for PHP that is updated with new PHP releases
        // array('cost') param takes the salt in form cost factor
        
        // note that this cannot be tested right now because the password stored in the DB are not hashed and salted
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
    
    // Validating the User Login
    
    
    // set up the query
    $query = "SELECT * FROM User WHERE user_name='$username' AND psw_hash_salted='$psw_hash_salt';";

    // set up the prepared statement
    $st = $cn ->stmt_init();
    $st ->prepare($q);

    // execute statement and store result in $result
    $st ->execute();
    $st ->bind_result($result);

    // $result = $cn->query($query)

    if($result->num_rows == 1) {
        // login was successful

        // TODO: will have to change to index.php, since that's the new version of the file
        header("Location: index.php");
        exit();
    } 
    else {
        // login was unsuccessful
        // header("Location: error.html")
        exit();

        // if possible, display popup that login info was wrong, otherwise, can redirect back to login page
    }

    $cn->close();
?>
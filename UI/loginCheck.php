<!-- * File Name: loginCheck.php
 * 
 * Description:
 * The "back end" side of the login.html page. The loginCheck.php file checks if the user-entered credentials
 * are found in the database. If not, an error screen indicating that the login was invalid pops up, otherwise,
 * the user is able to proceed to the index.php page.
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
    
    session_start();
    ob_start();

    if($_SERVER["REQUEST_METHOD"] == "POST") {

        // retrieve form data
        $username = $_POST["username"];
        $password = $_POST["password"];
    } else {
        header("Location: login.html");
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

    // set up the query
    $query = "SELECT psw_hash_salted FROM User WHERE user_name = ?";

    // set up the prepared statement
    $st = $cn ->stmt_init();

    $st ->prepare($query);
    $st ->bind_param("s", $username);

    // execute statement and store the psw_hash_salted in $result variable
    $st ->execute();

    $st ->bind_result($result);

    $st ->fetch();

    // $result = $cn->query($query);
    // $st->store_result();

    // use password_verify to compare the password from the post with the hashed psw from the DB
    if (password_verify($password, $result)) {
        // Use Sessions to transfer username to different PHP pages
        $_SESSION["session_user"] = $username;

        // TODO: will have to change to index.php, since that's the new version of the file
        header("Location: index.php");
        echo("test22");
        echo($result);
        // TODO: 2/20/24 add session_start() in index.php file and add this line: 
        // $username = $_SESSION["session_user"];
        exit();
    } else {
        // password is incorrect
        // $html = preg_replace('#<div class="invisible">(.*?)</h3>#', '', $html);
        // I don't think this will work because login.html will just load in without the regex
        header("Location: login.html");
        echo("test11");
        // $st->close();
        // $cn->close();
        exit();
    }
        
    $st->close();
    $cn->close();
?>
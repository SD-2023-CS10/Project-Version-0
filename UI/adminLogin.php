<!-- * File Name: adminLogin.html
 * 
 * Description:
 * Accessed from the main login.php page, this page allows a user admin to login to their account. Similar to the
 * main login page, admins must log in with valid credentials to gain access to the adminCreate.html page. If their
 * login information is invalid, an error message pops up indicating so, otherwise, they are granted access to the
 * adminActions.php page.
 * 
 * @package MedcurityNetworkScanner
 * @authors Artis Nateephaisan (anateephaisan@zagmail.gonzaga.edu)
 * @license 
 * @version 1.0.1
 * @link 
 * @since 
 * 
 * Usage:
 * This file should be placed in the root directory of the application. It can be directly
 * accessed via the URL [Your Application's URL]. No modifications are necessary for basic
 * operation.
 * 
 * Modifications:
 * [4/20/24] - [Artis Nateephaisan] - Version [1.0.1] - [Added error message functionality]
 * 
 * Notes:
 * - Additional notes or special instructions can be added here.
 * - Remember to update the version number and modification log with each change.
 * 
 * TODO:
 * - List any pending tasks or improvements that are planned for future updates.
 * 
 */ -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <title>User Login</title>
    <link rel="stylesheet" href="styling\login-menu.css">

</head>

<body>
    <div class="container">
        <form action="adminCheck.php" method="POST">
            <h1 class="form__title">Admin Login</h1>
            <div class="form__message form__message--error"></div>
            <div class="form__input-group">
                <input type="text" class="form__input" name="username" autofocus placeholder="Username" required />
                <div class="form__input-error-message"></div>
            </div>
            <div class="form__input-group">
                <input type="password" class="form__input" name="password" autofocus placeholder="Password" required />
                <p class="form__input-error-message">
                    <?php

                    session_start();
                    ob_start();

                    if (isset($_SESSION['login_error'])) {
                        echo ($_SESSION['login_error']);
                        unset($_SESSION['login_error']);
                    }
                    ?>
                </p>
            </div>
            <input type="submit" value="Login" class="form__button">
        </form>
    </div>
</body>

</html>
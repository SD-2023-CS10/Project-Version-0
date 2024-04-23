<!-- * File Name: login.php
 * 
 * Description:
 * The initial page the user sees upon loading the page. Here, the user is prompted to enter their credentials.
 * Upon valid credentials, they will be taken to the index.php page, which contains the core functionality of the 
 * Network Inventory Tool. In this login.php page, the user is also able to click the "Change Password"
 * button, which will take them to the changePassword.php page, or the "Admin Login" button, which will 
 * take them to the adminLogin.php page. There is also a "Forgot Password" button, which takes the user to a page
 * that details Medcurity support's contact information (phone number and email).
 * 
 * @package MedcurityNetworkScanner
 * @authors Artis Nateephaisan (anateephaisan@zagmail.gonzaga.edu)
 * @license 
 * @version 1.0.2
 * @link 
 * @since 
 * 
 * Usage:
 * This file should be placed in the root directory of the application. It can be directly
 * accessed via the URL [Your Application's URL]. No modifications are necessary for basic
 * operation.
 * 
 * Modifications: 
 * [4/8/24] - [Artis Nateephaisan] - Version [1.0.1] - [Removed comments, changed message for link to newUser.html]
 * [4/20/24] - [Artis Nateephaisan] - Version [1.0.2] - [Changed file to php, added support for error
 *  message functionality]
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
        <form action="loginCheck.php" method="POST">
            <h1 class="form__title">Login</h1>
            <div class="form__message form__message--error"></div>
            <div class="form__input-group">
                <input type="text" class="form__input" name="username" autofocus placeholder="Username" required />
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
        <p class="form___link" style="text-align:center">
            <a href="changePassword.php" class="form__links">Change Password</a>
            <a href="forgotPassword.html" class="form__link">Forgot Password</a>
        </p>
        <p class="form__text">
            <a href="adminLogin.php" class="admin__form__link">Admin Login</a>
        </p>
    </div>
</body>

</html>
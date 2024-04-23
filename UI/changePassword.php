<!-- * File Name: changePassword.php
 * 
 * Description:
 * Accessed from the main login.html page, this page allows the user to take their login information (provided
 * by Medcurity) and change the password so that their password is known only to them. They could also use this
 * page to change their password as frequently as necessary.
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
 * [4/20/24] - [Artis Nateephaisan] - Version [1.0.2] - [Added support for error message functionality]
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

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <title>Login / Sign Up Form</title>
    <link rel="stylesheet" href="styling\login-menu.css">
</head>

<body>
    <div class="container">
        <form action="changePasswordCheck.php" method="POST">
            <h1 class="form__title">Change Password</h1>
            <div class="form__message form__message--error"></div>
            <div class="form__input-group">
                <input type="text" class="form__input" name="username" autofocus placeholder="Username" required>
                <div class="form__input-error-message"></div>
            </div>
            <div class="form__input-group">
                <input type="password" class="form__input" name="oldPassword" autofocus placeholder="Old Password" required>
                <div class="form__input-error-message"></div>
            </div>
            <div class="form__input-group">
                <input type="password" class="form__input" name="newPassword" autofocus placeholder="New Password" required>
                <p class="form__input-error-message">
                    <?php

                    session_start();
                    ob_start();

                    if (isset($_SESSION['null_user_error'])) {
                        echo ($_SESSION['null_user_error']);
                        unset($_SESSION['null_user_error']);
                    }
                    ?>
                </p>
            </div>
            <input type="submit" value="Change Password" class="form__button">

        </form>
    </div>
</body>
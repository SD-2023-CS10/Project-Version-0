<!-- * File Name: adminActions.php
 * 
 * Description:
 * Accessed upon valid credentials from the adminLogin.php page, the admin user will be able to access this page,
 * where they are able to create new client user accounts (username and password) as well as delete old client 
 * user accounts. This is the front-end of this page, where the adminCreate.php and adminDelete.php files perform
 * the backend functionality of actually creating and deleting the client user accounts.
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
 * [3/13/24] - [Artis Nateephaisan] - Version [1.0.1] - [Added user account deletion functionality]
 * [4/20/24] - [Artis Nateephaisan] - Version [1.0.2] - [Converted html file to php for error message functionality]
 * 
 * Notes:
 * IMPORTANT: Right now, if the client knows the name of the file "adminActions.php", they can 
 * change the URL to access this page and have admin perms. For future development, there needs to be a way to
 * circumvent this.
 * 
 * TODO:
 * - List any pending tasks or improvements that are planned for future updates.
 * There should be confirmation screen upon client user account deletion so that there is a safeguard in 
 * case of any accidental deletions.
 * Find a way to safeguard normal user access to adminActions.php functionality should they know the url name.
 */ -->

<!DOCTYPE html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <title>Admin Create/Remove Account</title>
    <link rel="stylesheet" href="styling\login-menu.css">
</head>

<body>
    <div class="container">
        <form action="adminCreate.php" method="POST">
            <h1 class="form__title">Create a Client Account</h1>
            <div class="form__input-group">
                <input type="text" class="form__input" name="username" autofocus placeholder="Set Username" required />
                <div class="form__input-error-message"></div>
            </div>
            <div class="form__input-group">
                <input type="text" class="form__input" name="oldPassword" autofocus placeholder="Set Password" required />
                <div class="form__input-error-message"></div>
            </div>
            <div class="form__input-group">
                <input type="text" class="form__input" name="client" autofocus placeholder="Client Company" required />
                <p class="form__input-error-message">
                    <?php

                    session_start();
                    ob_start();

                    if (isset($_SESSION['create_error'])) {
                        echo ($_SESSION['create_error']);
                        unset($_SESSION['create_error']);
                    }

                    ?>
                </p>
                <p class="form__input-error-message">
                    <?php

                    if (isset($_SESSION['duplicate_account'])) {
                        echo ($_SESSION['duplicate_account']);
                        unset($_SESSION['duplicate_account']);
                    }

                    ?>
                </p>
                <p class="form__input-success-message">
                    <?php

                    if (isset($_SESSION['create_success'])) {
                        echo ($_SESSION['create_success']);
                        unset($_SESSION['create_success']);
                    }

                    ?>
                </p>
            </div>
            <input type="submit" value="Create Client Account" class="form__button">

        </form>
    </div>
    <div class="container">
        <form action="adminDelete.php" method="POST">
            <h1 class="form__title">Delete a Client Account</h1>
            <div class="form__input-group">
                <input type="text" class="form__input" name="username" autofocus placeholder="Username" required />
                <div class="form__input-error-message"></div>
            </div>
            <div class="form__input-group">
                <input type="text" class="form__input" name="client" autofocus placeholder="Client Company" required />
                <p class="form__input-error-message">
                    <?php

                    if (isset($_SESSION['delete_error'])) {
                        echo ($_SESSION['delete_error']);
                        unset($_SESSION['delete_error']);
                    }
                    ?>
                </p>
                <p class="form__input-success-message">
                    <?php

                    if (isset($_SESSION['delete_success'])) {
                        echo ($_SESSION['delete_success']);
                        unset($_SESSION['delete_success']);
                    }
                    ?>
                </p>
            </div>
            <input type="submit" value="Delete Client Account" class="form__button">

        </form>
    </div>
</body>
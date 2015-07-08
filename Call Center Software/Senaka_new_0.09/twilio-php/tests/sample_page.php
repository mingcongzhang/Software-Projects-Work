<?php

define('INCLUDE_CHECK',true);

include_once 'includes/database.php';
include_once 'includes/functions_login.php';
 
sec_session_start();
if (login_check($collection_user) != true) {
	header('Location: login.php');
	exit;
} 

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Secure Login: Protected Page</title>
        <link rel="stylesheet" href="styles/main.css" />
    </head>
    <body>
            <p>Welcome <?php echo htmlentities($_SESSION['username']); ?>!</p>
            <p>
                This is an example protected page.  To access this page, users
                must be logged in.  At some stage, we'll also check the role of
                the user, so pages will be able to determine the type of user
                authorised to access the page.
            </p>
            <p>Return to <a href="login.php">login page</a></p>
            <p>
            </p>
    </body>
</html>



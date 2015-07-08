<?php

define('INCLUDE_CHECK',true);

require_once("includes/config_login.php");
require_once 'includes/functions_login.php';


// Must always start the session prior to getting session parameters
sec_session_start();
 
// Delete the cookie (associated with the session)
delete_cookie();

// Delete the session 
sec_session_end();

header('Location: login.php');
exit;


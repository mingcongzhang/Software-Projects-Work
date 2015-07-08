<?php

/**
 *  something about this file
 * 
 */

if(!defined('INCLUDE_CHECK')) die('You are not allowed to execute this file directly');

include_once 'includes/config_login.php';
 

/**
 * function sec_session_start
 * Starts a session securely (or pretty securely)
 */
function sec_session_start() {
	$session_name = '_uvtiqixhej_';   // Set a custom session name
	$secure = SECURE;

	// Prevent JavaScript from being able to access the session id.
	$httponly = true;

	// Force sessions to only use cookies.
	if (ini_set('session.use_only_cookies', 1) === FALSE) {
		header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
		exit();
	}

	// Get current cookies params.
	$cookieParams = session_get_cookie_params();

	session_set_cookie_params($cookieParams["lifetime"],
        $cookieParams["path"], 
        $cookieParams["domain"], 
        $secure,
        $httponly);

	// Set the session name 
	session_name($session_name);
	session_start();            // Start the PHP session 
	session_regenerate_id();    // regenerated the session, delete the old one. 
}


/**
 * function sec_session_end
 * delete the user's cookie
 */
function sec_session_end() {

	// Unset all session values 
	$_SESSION = array();
 
	// Destroy session 
	session_destroy();
}


/**
 * function login
 * Authenticates the user
 */
function login($username, $password, $collection) {

	// Get from the database,  the records for the username 
	$result = $collection->findOne(array('email'=>$username));

	if ($result == NULL) {
                // no matches for username in database 
		// Delete the cookie and the session
		delete_cookie();
		sec_session_end();
		return false; 
	}

//may have to change it later senaka. in 
// http://www.wikihow.com/Create-a-Secure-Login-Script-in-PHP-and-MySQL, user_id comes from database
	$user_id = $username; 

	if (checkbrute($user_id, $collection) == true) {
		// Account is locked - send email to user saying account is locked
// senaka - not good to just send email....better show an error message also

		// Delete the cookie and the session
		delete_cookie();
		sec_session_end();

		return false;
	} else {
		// Check if the password in the database matches the user submitted password. 
// Senaka change this later
		//$password = hash('sha512', $password . $salt);
		$password = md5($password);

		if ($result['password'] != $password) {

			// Invalid password, so we record this attempt, and delete the session.
// Senaka record the invalid login count/time in redis

			// Delete the cookie and the session
			delete_cookie();
			sec_session_end();

			return false;
		}
		else if ($result['status'] != "active" ) {
			// If the user's status is NOT active, the user cannot log in.
			return false;
		}

		// Password is correct! Login successful.

		// XSS protection as we might print this value
		//$user_id = preg_replace("/[^0-9]+/", "", $user_id);
		$_SESSION['user_id'] = $user_id;

		// XSS protection as we might print this value
		//$username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $username);
		$username = preg_replace("/[^a-zA-Z0-9_\-.@]+/", "", $username);
		$_SESSION['username'] = $username;

		// login_string is from hash of password and the browser's user-agent string. 
		$_SESSION['login_string'] = hash('sha512',$password.$_SERVER['HTTP_USER_AGENT']);

		// company id is from the database
		if(isset($result['company'])) 
			$_SESSION['company'] = (string) $result['company'];

		// The user's user type 
		if(isset($result['type'])) 
			$_SESSION['user_type'] = $result['type'];

		if(isset($result['twilio_configured'])) 
			$_SESSION['twilio_configured'] = $result['twilio_configured'];


		return true;
	}
}


/**
 * function checkbrute
 * Checks if there has been an brute force login attemps for the user
 */
function checkbrute($user_id, $collection) {
	// All login attempts are counted from the past 2 hours. 
	$valid_attempts = time() - (2 * 60 * 60);
 
//senaka handle this later, once the rest is working
// If there have been more than 5 failed logins within the 2 hour period ($valid_attempts), lock account
// one successful login, clears all previous logins
return false;
}


/**
 * function login_check
 * Checks if the user is logged in
 */
function login_check($collection) {
	// Check if all session variables are set 
	if (isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])) {

		$result = $collection->findOne(array('email'=>$_SESSION['username'] ));

		// create login_check from hash of {password and browser's user-agent string}
		$login_check = hash('sha512', $result['password'] . $_SERVER['HTTP_USER_AGENT']);

		if ($result == NULL) {
			// Not logged in 
			return false; 
		} elseif ($login_check == $_SESSION['login_string']  ){
			return true;
		} else {
			// Not logged in 
			return false; 
		}
  	 } else {
		// Not logged in 
		return false;
   	 }
}


/**
 * function delete_cookie
 * delete the user's cookie
 */
function delete_cookie() {

	// get session parameters 
	$params = session_get_cookie_params();
 
	// Delete the actual cookie. 
	setcookie(session_name(), '', 1,
	$params["path"], 
	$params["domain"], 
	$params["secure"], 
	$params["httponly"]);
}


/**
 * function esc_url
 * Escapes URL 
 */
function esc_url($url) {
 
	if ('' == $url) return $url;
 
	$url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
	$url = (string) $url;
 
	$strip = array('%0d', '%0a', '%0D', '%0A');
	$count = 1;
	while ($count) {
		$url = str_replace($strip, '', $url, $count);
	}
 
	$url = str_replace(';//', '://', $url);
	$url = htmlentities($url);
	$url = str_replace('&amp;', '&#038;', $url);
	$url = str_replace("'", '&#039;', $url);
 
	if ($url[0] !== '/') {
		// We're only interested in relative links from $_SERVER['PHP_SELF']
		return '';
	} else {
		return $url;
	}
}


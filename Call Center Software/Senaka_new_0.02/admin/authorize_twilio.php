<?php

define('INCLUDE_CHECK',true);

// These files can be included only if INCLUDE_CHECK is defined
//require_once("includes/functions_validateforms.php");
require_once("../includes/config_login.php");
require_once("includes/config_mongo.php"); // establishes connection to database or set $error
require_once("includes/functions_login.php");
 
sec_session_start();
if (login_check($collection_user) != true) {
	$_SESSION['message'] = "ERROR: Please login before you execute this function";
	header('Location: login.php');
	exit;
} 


if (!isset($error)) {
	// Check if company_id is stored in the session. Without it, some modules will not work. 
	if (!isset($_GET['AccountSid'])) {
		$error = "Invalid Request!";
	} 
	else if (!isset($_SESSION['company'])) {
		$error = "Session Error. Please log in again!";
	}
	else if (!isset($_SESSION['user_type'])) {
		$error = "Session Error. Please log in again!";
	}
	else if ($_SESSION['user_type'] != "admin" ) {
		$error = "You are not authorized to perform this action!";
	}
	else {
		// Get user type for this user
		$user_type = $_SESSION['user_type'];

		// get the company ID (MongoID) 
		$company_id = new MongoId($_SESSION['company']);

		// store accountSid in database associated with a company
		$accountsid = $_GET['AccountSid'];

		// If a non-admin user (supervisor, agent), the user has created a password 
		// and needs to be provisioned in the database; 
		try {
			$collection_company->update(array('_id'=>$company_id),
				array('$set'=>(array('a_sid'=> $accountsid ))));
		}
		catch(MongoException $e) {
			$error = "Internal Error. Please try again later!";
		}
	}
}

// set the appropriate notification message to the user
if (!isset($error)) 
	$_SESSION['message'] = "SUCCESS: Thank you for configuring Twilio Connect!";
else 
	$_SESSION['message'] = "ERROR: ".$error;

// Transfer user to the proper location/file
header('Location: login.php');
exit;

?>


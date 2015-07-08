<?php

/**
 *  this file contains functions for twilio sub account
 * 
 */

if(!defined('INCLUDE_CHECK')) die('You are not allowed to execute this file directly');

include_once 'includes/config_twilio.php';
include_once('twilio-php/Services/Twilio.php'); // Load the PHP helper library 


/**
 * function create_twilio_subaccount
 * creates a twilio sub account 
 */
function create_twilio_subaccount($name) {

	$client = new Services_Twilio($sid, $token);
 
	$account = $client->accounts->create(array(
        "FriendlyName" => $name
	));

echo $account->sid;

}



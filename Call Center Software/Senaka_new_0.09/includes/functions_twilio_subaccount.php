<?php

/**
 *  this file contains functions for twilio sub account
 * 
 */

if(!defined('INCLUDE_CHECK')) die('You are not allowed to execute this file directly');

include_once('twilio-php/Services/Twilio.php'); // Load the PHP helper library 


/**
 * function create_twilio_subaccount
 * creates a twilio sub account 
 */
function create_twilio_subaccount($name, $sid, $token) {

	// create a twilio client
	$client = new Services_Twilio($sid, $token);
 
	// Get an object from its sid. If sid is not available, use the list resource 
	$account = $client->accounts->create(array(
        "FriendlyName" => $name
	));

	return $account;
}


/**
 * function delete_twilio_subaccount
 * creates a twilio sub account 
 */
function delete_twilio_subaccount($accountnum, $sid, $token) {

	// create a twilio client
	$client = new Services_Twilio($sid, $token);
 
	// Get an object from its sid. If sid is not available, use the list resource 
	$account = $client->accounts->get("$accountnum");
	$account->update(array(
	"Status" => "closed"
	));

	return $account;
}


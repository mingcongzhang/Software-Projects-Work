<?php

if(!defined('INCLUDE_CHECK')) die('You are not allowed to execute this file directly');

function validate_companyname($key){ 
        if (!preg_match ("/^[a-z0-9 ]+$/i", $key)) 
		return "ERR04: The Name must be a valid character";
	return;
}


function validate_name($key){ 
        if (!preg_match ("/[^a-z0-9\s-]/i", $key)) 
		return "ERR04: The Name must be a valid character";
	return;
}


function validate_email($key){
	if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $key)) 
		return "Error: This is not a valid E-mail address";
	return;
}


function validate_telnumber($key){
	// Telephone number needs to be a 10 digit number; length is 10; all digits;
	if (!preg_match("/^[+]?([\d]{0,3})?[\(\.\-\s]?([\d]{3})[\)\.\-\s]*([\d]{3})[\.\-\s]?([\d]{4})$/", $key)) 
		return "ERR06: The Telephone number must be a 10 DIGIT number";

	return;
}


function format_telnumber($key){
	// if telephone number is a 10 digit number, assume USA/NANP format
	if (preg_match ("/^\d{10}$/", $key)) 
		return "(".substr($key,0,3).") ".substr($key,3,3)."-".substr($key,6);

	return $key;
}


function validate_telprefix($key){
	// Prefix needs to be a 3 - 10 digit number; all digits;
	if (preg_match ("/^[\d]{3,10}$/", $key)) 
		return 1;

	return 0; //"ERR06: The Telephone prefix number must be between 3 and 9 DIGITs";
}


function validate_countryISO($key) {
	$key = strtoupper($key);

	// the list contains the 2-DIGIT ISO codes of countries where telephone numbers are offered
	// Australia, Canada, China, France, Germany, Hong Kong, India, Netherlands, UK, US
	$list = array("AU", "CA", "CN", "FR",  "DE", "HK", "IN", "NL", "GB", "US");

	if (in_array($key, $list)) {
		return $key;
	}

	return 0; //"Error: Invalid Country ISO code";
}


// returns TRUE if telephone number type matches the given 
function validate_telnumbertype($key) { 

	// returns the type accepted by twilio

	if (strcasecmp($key, "Local" ) == 0) {
		return "Local";
	} else if (strcasecmp($key, "Toll-Free" ) == 0) {
		return "TollFree";
	} else if (strcasecmp($key, "Mobile" ) == 0) {
		return "Mobile";
	} else if (strcasecmp($key, "National" ) == 0) {
		return "National";
	}

	//"Error: Invalid phone number type";
	return 0; 
}

?>


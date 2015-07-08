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
	if (!preg_match ("/^[+]?([\d]{0,3})?[\(\.\-\s]?([\d]{3})[\)\.\-\s]*([\d]{3})[\.\-\s]?([\d]{4})$/", $key)) 
		return "ERR06: The Telephone number must be a 10 DIGIT number";

return;
}


function format_telnumber($key){
	// if telephone number is a 10 digit number, assume USA/NANP format
	if (preg_match ("/^\d{10}$/", $key)) 
		return "(".substr($key,0,3).") ".substr($key,3,3)."-".substr($key,6);
	else 
		return $key;
}


?>


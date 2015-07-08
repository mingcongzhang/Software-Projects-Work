<?php

/**
 * Database configuration details and connects to database
 */

if(!defined('INCLUDE_CHECK')) die('You are not allowed to execute this file directly');

$mongo_user = "mingcong";
$mongo_pass = "John123456";
$mongo_url = "ds047440.mongolab.com:47440/donation";

$donorinfo = "donor_info";
$fundraiserinfo = "fundraiser_info";
$db = "donation";

try{
	$connection = new Mongo('mongodb://'.$mongo_user.':'.$mongo_pass.'@'.$mongo_url);
	$database = $connection->selectDB($db);
	$collection_donor = $database->selectCollection($donorinfo);
	$collection_fundraiser = $database->selectCollection($fundraiserinfo);	
} 
catch(MongoConnectionException $e) {
	$error='Error connecting to Database. Please try again later.'.$e;
}

?>

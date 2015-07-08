<?php

/**
 * Database configuration details and connects to database
 */

if(!defined('INCLUDE_CHECK')) die('You are not allowed to execute this file directly');

$mongo_user = "mingcong";
$mongo_pass = "test12";
$mongo_url = "ds039778.mongolab.com:39778/solidbase";

$companyinfo = "company_info";
$userinfo = "user_info";
$queueinfo = "queue_info";
$phonenumber = "phone_number";
$groupinfo = "group_info";
$db = "solidbase";

try{
	$connection = new Mongo('mongodb://'.$mongo_user.':'.$mongo_pass.'@'.$mongo_url);
	$database = $connection->selectDB($db);
	$collection_company = $database->selectCollection($companyinfo);
	$collection_user = $database->selectCollection($userinfo);	
	$collection_queue = $database->selectCollection($queueinfo);
	$collection_phonenum = $database->selectCollection($phonenumber);
	$collection_group = $database->selectCollection($groupinfo);
} 
catch(MongoConnectionException $e) {
	$error='Error connecting to Database. Please try again later.'.$e;
}

?>

<?php

/**
 *  This file provide additional detail and customization info about a company's telephone number 
 * 
 */

define('INCLUDE_CHECK',true);

$PRE_URL = "../";

// These files can be included only if INCLUDE_CHECK is defined
require_once("$PRE_URL"."includes/config_twilio.php");
require_once("$PRE_URL"."includes/config_login.php");
require_once("$PRE_URL"."includes/config_mongo.php"); // connect to database or set $error
require_once("$PRE_URL"."includes/functions_validateforms.php");
require_once("$PRE_URL"."includes/functions_login.php");

// Include files 
require_once('../twilio-php/Services/Twilio.php'); // Load the PHP helper library 

// Copy database issues onto $db_error. $error will be cleared soon after
if(isset($error)) 
	$db_error = $error;

unset($error, $output, $num_found, $have_updates );


// Check if user is logged in.
sec_session_start();
if (login_check($collection_user) != true) {
	header("Location: ".$PRE_URL."login.php");
	exit;
} 

// Check if user is admin; Only an admin has access to this page
if ($_SESSION['user_type'] != "admin" ) {
	$_SESSION['message'] = "ERROR: You are not authorized to perform this action!";
	header("Location: ".$PRE_URL."login.php");
	exit;
}

// Get the company sid and token from the SESSION parameters. 
if (isset($_SESSION['sid']) && isset($_SESSION['token']) ) {
	$company_sid = $_SESSION['sid'];
	$company_token = $_SESSION['token'];
} else {
	$error = "Session Error. Please log in again!";
}

// Get company_id from the SESSION parameters. Without which, several modules will not work. 
if (isset($_SESSION['company'])) {
	// get the company ID (MongoID) 
	$company_id = new MongoId($_SESSION['company']);
} else {
	$error = "Session Error. Please log in again!";
}

if( !isset($db_error, $error, $_POST['number'] ) && $_POST['number'] == "showdetail" ) {

	$phone = isset($_POST['phone']) ? $_POST['phone']: null;

	// Validate each field below. Different validation rules apply.
	if (empty($phone) ) {
		$error = 'Invalid telephone number!';
	} 
	else {
		// Update the details of the telephone number from the database, filtered by company ID
		try {
			$result = $collection_phonenum->findOne(array(
			'company' => $company_id, 
			'phone_number' => $phone
			));

		} catch(MongoException $e) {
			$error = "Database Error (14326). Please try again later!";
		} 

		$phone = isset($result['phone_number']) ? $result['phone_number']: $phone;
		$friendly = isset($result['friendly_name']) ? $result['friendly_name']: null;

		if ($result == NULL) {
			// No match in database
			$error = 'Telephone number not found!';
		} else if (!isset($result['phone_number'] ) ) {
			$error = 'Telephone number not found!';
		} 
	}
}

// This is when the user updates and submits information on a particular telephone number
else if( !isset($db_error, $error, $_POST['number'] ) && $_POST['number'] == "update" ) {

	// Get the telephone number (e.g. "+18475765000"). This is the key to updating the phone info.
	$phone = isset($_POST['phone']) ? $_POST['phone']: null;

	if (empty($phone) ) {
		$error = 'Please enter a valid telephone number!';
	}

	$updates = array();

	// Get the rest of the parameters that have been changed
	if ( !empty($_POST['friendly'] )) {
		$friendly =  $_POST['friendly'];
		$updates['friendly_name'] = $friendly;
		$have_updates = true;
	}

	// If there are any changes that need to be updated, update the database
	if ($have_updates == true){

		// also add 'last_update' to the array to be updated
		$updates['last_update'] = time();

	        try {
			$collection_phonenum->update( 
			array('company' => $company_id, 'phone_number' => $phone ),
			array('$set' => $updates),
			array('upsert' => false)
			);
		}
		catch(MongoException $e) {
			// die('Failed to insert data '.$e->getMessage());
			$error = 'Database error. Please try again later'; 
		}

	} 
}

// copy $db_error, which is the most important/fundamental error, back to $error
if (isset($db_error)) $error = $db_error;

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="Voice BroadCast service" />
<meta name="keywords" content="Voice, Notifications, broadcast, callbroadcast, group" />
<meta name="author" content="SolidBase Consulting LLC" />
<title>CallBroadCast - CallCenter Software</title>
<link href="css/stylesheet.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/stylesheet1.css" rel="stylesheet" type="text/css" media="all" />
<script src="js/button.js" type="text/javascript"></script>

<style type="text/css">
body {
	font-family:"Lucida Grande", "Lucida Sans Unicode", Verdana, Arial, Helvetica, sans-serif;
	font-size:12px;
}
</style>


</head>
<body>
<div id="container">
  <div id="header">

<?php include ("includes/header.html"); ?>

  </div>
  <div id="inner-body">
    <div class="body-container">
      <div class="inner-welcome-box">
       <h1>View/Update Number</h1>
      </div>
      <div class="about-box">


<?php

if(isset($error)) 
	echo '<div class="errormsgbox"> '.$error.'</div>';

if (isset($_SESSION['message'])) {
	echo '<div class="success">'. $_SESSION['message'].'</div>';
	unset($_SESSION['message']);
}

?>


<h3><?php echo $phone;?></h3>
<hr>

<div class="callform">
  <div class="form-item">

  	<form name="input" action="<?php echo basename(__FILE__); ?>" method="post">
	<input type="hidden" name="number" value="update">
	<input type="hidden" name="phone" value="<?php echo $phone; ?>" />

        <table class="number-to-call">
      
	<TR>
	<TD><label for="friendly">Friendly Name</label></TD>
	<TD><input type="text" name="friendly" value="<?php if(isset($friendly)) echo $friendly; ?>" </TD>
	</TR>

	<TR>
	<TD><label for="callsettings"> <h3>Call Settings</H3></label></TD>
	</TR>

	<TR>
	<TD><label for="inboundrecording">Inbound call recording enabled</label></TD>
	<TD>	
		<input type="radio" name="inbound_recording" value="yes" 
		<?php if ($inbound_recording == "yes") echo "checked"; ?> >Yes

		<input type="radio" name="inbound_recording" value="no" 
		<?php if ($inbound_recording == "no") echo "checked"; ?> >No
	</TD>
	</TR>

	<TR>
	<TD><label for="outboundrecording">Outbound call recording enabled</label></TD>
	<TD>	
		<input type="radio" name="outbound_recording" value="yes" 
		<?php if ($outbound_recording == "yes") echo "checked"; ?> >Yes

		<input type="radio" name="outbound_recording" value="no" 
		<?php if ($outbound_recording == "no") echo "checked"; ?> >No
	</TD>
	</TR>

	<TR>
	<TD><label for="queuesettings"> <h3>Queue Settings</H3></label></TD>
	</TR>

	<TR>
	<TD><label for="queuevoicemail">Queue to voicemail <small>(digit to press)</small></label></TD>
	<TD><select type="text" name="queue_voicemail">
		<option value="NO">Not Enabled</option>
		<option value="0">0</option>
		<option value="1">1</option>
		<option value="2">2</option>
		<option value="3">3</option>
		<option value="4">4</option>
		<option value="5">5</option>
		<option value="6">6</option>
		<option value="7">7</option>
		<option value="8">8</option>
		<option value="9">9</option>
	<select></TD>
	</TR>

	<TR>
	<TD><label for="queuecallback">Queue Callback <small>(digit to press)</small></label></TD>
	<TD><select type="text" name="queue_callback">
		<option value="NO">Not Enabled</option>
		<option value="0">0</option>
		<option value="1">1</option>
		<option value="2">2</option>
		<option value="3">3</option>
		<option value="4">4</option>
		<option value="5">5</option>
		<option value="6">6</option>
		<option value="7">7</option>
		<option value="8">8</option>
		<option value="9">9</option>
	<select></TD>
	</TR>

	<TR>
	<TD><label for="maxqueue">Maximum Queue Size</label></TD>
	<TD><select type="text" name="max_queue">
		<option value="3">3</option>
		<option value="4">4</option>
		<option value="5">5</option>
		<option value="10">10</option>
		<option value="15">15</option>
		<option value="20">20</option>
		<option value="25">25</option>
		<option value="30">30</option>
		<option value="40">40</option>
		<option value="50">50</option>
		<option value="75">75</option>
		<option value="100">100</option>
		<option value="1000">Unlimited/Don't Care</option>
	<select></TD>
	</TR>

	<TR>
	<TD> </TD>
	<TD><input type="submit" value="Save" name"submit" ></TD>

        </TABLE>
  </div>
  </form>
</div>


        </div>
      <div class="body-bottom-box">
	<h2>Voice BroadCast - <span style="color: #009dea;">Click.Set.Go</span></h2>
      </div>
    </div>
  </div>
  <div id="footer">
<?php include ("includes/footer.html"); ?>
  </div>
</div>
</body>
</html>

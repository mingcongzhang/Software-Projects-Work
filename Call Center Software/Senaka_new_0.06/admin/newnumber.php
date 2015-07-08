<?php

/**
 *  This file allows an admin to purchase a new telephone number
 * 
 */


define('INCLUDE_CHECK',true);

$PRE_DIR = "../";

// These files can be included only if INCLUDE_CHECK is defined
require_once("$PRE_DIR"."includes/config_twilio.php");
require_once("$PRE_DIR"."includes/config_login.php");
require_once("$PRE_DIR"."includes/config_mongo.php"); // connect to database or set $error
require_once("$PRE_DIR"."includes/functions_validateforms.php");
require_once("$PRE_DIR"."includes/functions_login.php");

// Include files 
require_once("$PRE_DIR"."twilio-php/Services/Twilio.php"); // Load the PHP helper library 

// Copy database issues onto $db_error. $error will be cleared soon after
if(isset($error)) 
	$db_error = $error;

unset($error, $output, $num_found);

// Check if user is logged in.
sec_session_start();
if (login_check($collection_user) != true) {
	header("Location: ".$PRE_DIR."login.php");
	exit;
} 

// Check if user is admin; Only an admin has access to this page
if ($_SESSION['user_type'] != "admin" ) {
	$_SESSION['message'] = "ERROR: You are not authorized to perform this action!";
	header("Location: ".$PRE_DIR."login.php");
	exit;
}

// Check if the company sid and token are available 
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


// Search for a number
if(!isset($db_error) && $_POST['number'] == "search" ) {

	$country = $_POST['country'];
	$numbertype = $_POST['numbertype'];
	$prefix = $_POST['prefix'];

	// Validate each field below. Different validation rules apply.
	if (empty($country) || !($country2 = validate_countryISO($country) ) ) {
		$error = 'Please select the Country for the phone number!';
	} else if (empty($numbertype) || !($num_type = validate_telnumbertype($numbertype) ) ) { 
		$error = 'Please select the type of telephone number !';
	} else if (empty($prefix) || !validate_telprefix($prefix)) {
		$error = 'Please enter an Area Code or phone number prefix!';
	} else {
		// create a twilio client
		$client = new Services_Twilio($company_sid, $company_token);

		// pad $prefix with trailing "*" for twilio
		// In US, telephone number is 10 digits long
		if( $country2 == "US" ) {
			$prefix2 = str_pad($prefix, 10, "*", STR_PAD_RIGHT); 
		} else  {
			// this is a placeholder for future countries 
			$prefix2 = str_pad($prefix, 10, "*", STR_PAD_RIGHT); 
		}
 
		try {
			// Get available telephone numbers from Twilio, filtered by the prefix
			$numbers = $client->account->available_phone_numbers->getList(
				$country2, $num_type, 
				array("Contains" => "$prefix2")
			); 

		} catch (Exception $e) {
			$error =  $e->getMessage();
		}
	}

	if(!isset($error)) {
		$fmt = "<tr><td>%s</td><td>%s</td><td><form name='input' action='%s' method='post'> 
		<input type='hidden' name='number' value='buy'/> 
		<input type='hidden' name='phone' value='%s'/> 
		<input type='submit' value='Buy' /></form></td></tr>\n";

		// for each telephone number 
		foreach($numbers->available_phone_numbers as $number) {
			$num_found = true;

			$output .= sprintf($fmt, $number->friendly_name, 
			$number->rate_center.", ".$number->region, basename(__FILE__), 
			$number->phone_number );
		}
		if(!isset($num_found)) 
			$error = "No telephone numbers found. Please Try again!";
	}
}
else if(!isset($db_error) && !isset($error) && $_POST['number'] == "buy" ) {

	$phone = $_POST['phone'];

	// Validate each field below. Different validation rules apply.
	if (empty($phone) ) {
		$error = 'Please Invalid telephone number selected!';
	} 
	else {
		// create a twilio client
		$client = new Services_Twilio($company_sid, $company_token);

		try {
			$number = $client->account->incoming_phone_numbers->create(array(
				"PhoneNumber" => $phone ));
		} catch (Exception $e) {
			$error =  $e->getMessage();
		}
	}

	if(!isset($error)) {

		// show a message 
		$_SESSION['message'] = "$phone has been added.";

		// insert company info (including twilio sub account info) into company collection 
		try{
			$ret =  $collection_phonenum->insert( array(
			'company' => $company_id, 
			'status' => "active",
			'sid' => $number->sid, 
			'account_sid' => $number->account_sid, 
			'friendly_name' => $number->friendly_name, 
			'phone_number' => $number->phone_number, 
			'voice_url' => $number->voice_url, 
			'voice_method' => $number->voice_method, 
			'voice_fallback_url' => $number->voice_fallback_url, 
			'voice_fallback_method' => $number->voice_fallback_method, 
			'voice_caller_id_lookup' => $number->voice_caller_id_lookup, 
			'date_created' => $number->date_created, 
			'date_updated' => $number->date_updated, 
			'status_callback' => $number->status_callback, 
			'status_callback_method' => $number->status_callback_method, 
			'voice_application_sid' => $number->voice_application_sid,
			'last_update' => time()
			));
		}
		catch(MongoException $e){
			$error = "Internal error (14326). Please notify!";
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
       <h1>New Number1</h1>
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


<h3>New Number</h3>
<hr>

<div class="callform">
  <form name="input" action="<?php echo basename(__FILE__);?>" method="post">
  <input type="hidden" name="number" value="search">
  <div class="form-item">
        <table class="number-to-call">
      
	<TR>
	<TD><label for="fname">Country:</label></TD>
	<TD><select type="text" name="country">
		<option value="us">United States</option>
	<select></TD>
	</TR>
	<TR>
	<TD><label for="fname">Phone Type:</label></TD>
	<TD><input type="radio" name="numbertype" value="local" 
		<?php if ($numbertype == "local") echo "checked"; ?> >Local<br>$1/month</TD>
	<TD><input type="radio" name="numbertype" value="toll-free" 
		<?php if ($numbertype == "toll-free") echo "checked"; ?> >Toll-Free<br>$2/month</TD>

	</TR>
	<TR>
	<TD><label for="fname">Area Code or Prefix</label></TD>
	<TD><input type="text" name="prefix" size="9" value = "<?php if(isset($prefix)) echo $prefix; ?>" ><br></TD>
	</TR>

	<TR>
	<TD> </TD>
	<TD><input type="submit" value="Search Numbers" name"submit" ></TD>

        </TABLE>
  </div>
  </form>
</div>

<div class="form-item">
	<table class="number-to-call">
	<th>Phone Number</th> <th>Location</th> <th></th>
	<?php echo $output; ?>
	</TABLE>
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

<?php

/**
 *  This file allows an admin to view the list of telephone numbers
 * 
 */


define('INCLUDE_CHECK',true);

$PRE_DIR = "../";

// These files can be included only if INCLUDE_CHECK is defined
require_once("$PRE_DIR"."includes/config_twilio.php");
require_once("$PRE_DIR"."includes/config_login.php");
require_once("$PRE_DIR"."includes/config_mandrill.php");
require_once("$PRE_DIR"."includes/config_mongo.php"); // connect to database or set $error
require_once("$PRE_DIR"."includes/functions_validateforms.php");
require_once("$PRE_DIR"."includes/functions_login.php");
require_once("$PRE_DIR"."includes/functions_mandrill.php"); // Mandrill SwiftMailer

// Copy database issues onto $db_error. $error will be cleared soon after
if(isset($error)) 
	$db_error = $error;

unset($error, $output);

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


// Get the list of telephone numbers for the company from the database
if(!isset($db_error) && !isset($error) ) {

	// Get the list of telephone numbers from the database, filtered by company ID
	try {
		$cursor = $collection_phonenum->find(array('company'=>$company_id));
	} catch(MongoException $e) {
		$error = "Database Error (14326). Please try again later!";
	} 

	// No match in database
	if (!isset($cursor)){
		$error = "Database Error (14187). Please try again later!";
	} elseif ($cursor->count() <= 0){
		$error = "No Telephone numbers found!";
	} else {
//$fmt = "<tr><td>%s</td><td>%s</td><td></td></tr>\n";

		$fmt = "<tr><td>
		<a href=\"#\" onclick=\"document.getElementById('inputform').submit(); return false;\">%s</a>
		</td><td>%s</td><td>
		<input type='hidden' name='phone' value='%s'/> 
		</td></tr>\n";


		while ($cursor->hasNext()) {
			$result = $cursor->getNext();
			$output .= sprintf($fmt, $result['phone_number'], $result['friendly_name'], $result['phone_number'], $result['friendly_name'] );
		}
	}
//==================only for test(needs removal)==============
	$fmt = "<tr><td>
		<a href=\"#\" onclick=\"document.getElementById('inputform').submit(); return false;\">%s</a>
		</td><td>%s</td><td>
		<input type='hidden' name='phone' value='%s'/> 
		</td></tr>\n";

$output .= sprintf($fmt, "12345", "12345", "123456", "54321" );
//============================================================

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
       <h1>Numbers</h1>
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








<h3>Numbers</h3>
<hr>

<div class="callform">
  <div class="form-item">
	<a href="newnumber.php" /> New Phone Number</a>
  </div>
</div>

<div class="form-item">
	<table class="number-to-call">
	<th>Phone Number</th> <th>Nick Name</th>

	<form name="inputform" id="inputform" method="post" action="numberdetail.php">
	<input type='hidden' name='number' value='showdetail'/> 
	<?php echo $output; ?>
	</form>
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

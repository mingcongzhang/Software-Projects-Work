<?php
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




if(isset($_POST['hhhggg'])){

echo "OKAY!";
}








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
.rb {padding: 0;
border: none;
background: none; color:red;}
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
</form>
	<form name="input" action="<?php echo basename(__FILE__); ?>" method="post">
	<input type="hidden" name="hhhggg" value="upda">


<a href=\"#\" onclick=\"document.getElementById('inputform').submit(); return false;\">Click here</a>	
<form name="inputform" id="inputform" method="post" action="numberdetail.php">

</form>





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


<?php

define('INCLUDE_CHECK',true);

unset($error);
unset($success);
unset($sendemail);
unset($pw_reset);

// These files can be included only if INCLUDE_CHECK is defined
require_once("includes/config_mongo.php"); // establishes connection to database or set $error
require_once("includes/config_mandrill.php");
require_once("includes/functions_validateforms.php");
require_once("includes/functions_mandrill.php"); // Mandrill SwiftMailer


// HTTP POST Form has been submitted, which means the request came from a webpage
if (!isset($error) && isset($_POST['formsubmitted'])) {
	$email= urldecode($_POST['email']);
	$key = urldecode($_POST['key']);

	if (empty($_POST['password1']) || empty($_POST['password2'])) {
		// one of the passwords is empty
		$error = "Please fill out the required field(s)!";
	} else if ($_POST['password1'] != $_POST['password2']){
		// passwords do not match
		$error = "The passwords do not match! Please try again.";
	} else if (empty($email) || empty($key)) {
		// either the email or the activation key is empty
		$error = "Invalid Request. Please copy/paste or click on entire link!";
	} else if (validate_email($email)){
		$error="Invalid Request (13001). Please copy/paste or click on entire link!";
	} else if (strlen($key) != 32) {
		//The Activation key is 32 long since it is MD5 Hash
		$error="Invalid Request (13002). Please copy/paste or click on entire link!";
	} else {
		// passwords match and everything else is ok
		$password = $_POST['password1'];

		// check if user exists in database
		$result = $collection_user->findOne(array('email'=>$email));
		$firstname = $result['firstname'];
		$lastname = $result['lastname'];

		if ($result == NULL) {
			// No match in database
			$error="Invalid Request (13003). Please copy/paste or click on entire link!";
		} else if (!isset($result['code']) || !isset($result['status'])){
			$error="Invalid Request (13004). Please copy/paste or click on entire link!";
		} else if ($result['status'] == "active" ) {
			$error = "Your account is already active. Please login!";
		} else if($result['status'] != "signup"){
			$error = "Error in Account! Please notify error to customer support";
		} else if ($result['code'] != $key){
			$error = "Oops! Invalid activation code. Please see activation email!";
		} else if ($result['type'] == "admin"){

			// Admin types should not be here
			$error = "Error in Account! Please notify error to customer support";
		} else if ($result['type'] == "agent" || $result['type'] == "supervisor" ){

			// If a Non-Admin user (supervisor, agent), the user has created a password 
			// and needs to be provisioned in the database; 
			$collection_user->update(array('email'=>$email),
				array('$set'=>(
					array('status'=>"active", 
						'code'=>"",
						'password'=>md5($_POST['password1']),
						'online_status'=>"1",
						'last_update'=>time()
					)
				))
			);
			$sendemail = true;
		} else {
			$error = "Error - unknown error!";
		}
	}		
} 
// HTTP POST Form has NOT been submitted, which means the request came from an email or link
else if (!isset($error)) {

	// get the HTTP GET parameters from the link in the activation email
	$email= $_GET['email'];
        $key = $_GET['key'];

	if (empty($email) || empty($key)) {

		// either the email or the activation key is empty
		$error = "Invalid Request. Please copy/paste or click on entire link!";
	} else if (validate_email($email)){
		$error="Invalid E-mail address. Please copy/paste or click on entire link!";
	} else if (strlen($key) != 32) {

		//The Activation key is 32 long since it is MD5 Hash
		$error="Invalid Activation Key. Please copy/paste or click on entire link!";
	} else {

		// check if user exists in database
		$result = $collection_user->findOne(array('email'=>$email));
		$firstname = $result['firstname'];
		$lastname = $result['lastname'];
		$company_id = $result['company'];

		if ($result == NULL) {
			// No match in database
			$error="Invalid Request (13005). Please copy/paste or click on entire link!";
		} else if (!isset($result['code']) || !isset($result['status'])){
			$error="Invalid Request (13006). Please copy/paste or click on entire link!";
		} else if ($result['status'] == "active" ) {
			$error = "Your account is already active. Please login!";
		} else if($result['status'] !="signup"){
			$error = "Error in Account! Please notify error to customer support";
		} else if ($result['code'] != $key){
			$error = "Oops! Invalid activation code. Please see activation email!";
		} else if ($result['type'] == "admin"){
			// If an Admin, then the user has created a password during self-signup 
			// and needs to be provisioned in the database; 
			$collection_user->update(array('email'=>$email),
				array('$set'=>(
				array( 'status'=>"active",
					'code'=>"", 
					'last_update'=>time())
					)
				)
			);
			$sendemail = true;
		} else if ($result['type'] == "agent" || $result['type'] == "supervisor" ){
			// If a Non-Admin user (supervisor, agent), then the user has not
			// created a password and needs to be shown the password form 
			$pw_reset = true;
		} else {
			$error = "Error - unknown error!";
		}
	}
}

if (!isset($error) && isset($sendemail)) {
	$message = "A user successfully signed-up.\n
	First name: $firstname \n
	Last name: $lastname \n
	Email: $email \n
	Please setup an account and notify when done.\n";
	$body_type = 'text/plain';
	$from = 'support@solidbaseconsulting.com';
	$to = 'senaka@solidbaseconsulting.com';
	$subject = 'New User Sign-Up: ('. $email .')';

	$status = send_email_mandrill($MANDRILL_USERNAME, $MANDRILL_PASSWORD, $from, $to, $subject, 
		$message, $body_type);

	$success = 'Thank you for signing-up. Your account is now active! Please login to use features.';
}


?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="Voice BroadCast service" />
<meta name="keywords" content="Voice, Notifications, broadcast, callbroadcast, group" />
<meta name="author" content="SolidBase Consulting LLC" />
<title>Confirm Sign-Up/Activate</title>
<link href="css/stylesheet.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/stylesheet1.css" rel="stylesheet" type="text/css" media="all" />
<script src="js/button.js" type="text/javascript"></script>

<style type="text/css">
body {
        font-family:"Lucida Grande", "Lucida Sans Unicode", Verdana, Arial, Helvetica, sans-serif;
        font-size:12px;
}
.registration_form {
        margin:0 auto;
        width:500px;
        padding:14px;
}
label {
        width: 10em;
        float: left;
        margin-right: 0.5em;
        display: block
}
.submit {
        float:right;
}
fieldset {
        background:#EBF4FB none repeat scroll 0 0;
        border:2px solid #B7DDF2;
        width: 500px;
}
legend {
        color: #fff;
        background: #80D3E2;
        border: 1px solid #781351;
        padding: 2px 6px
}
.elements {
        padding:10px;
}
p {
        border-bottom:1px solid #B7DDF2;
        color:#666666;
        font-size:11px;
        margin-bottom:20px;
        padding-bottom:10px;
}
a{
    color:#0099FF;
font-weight:bold;
}

 .success {
        border: 1px solid;
        margin: 0 auto;
        padding:10px 5px 10px 60px;
        background-repeat: no-repeat;
        background-position: 10px center;

     width:450px;
     color: #4F8A10;
        background-color: #DFF2BF;
        background-image:url('images/success.png');
}
 .errormsgbox {
        border: 1px solid;
        margin: 0 auto;
        padding:10px 5px 10px 60px;
        background-repeat: no-repeat;
        background-position: 10px center;

     width:450px;
        color: #D8000C;
        background-color: #FFBABA;
        background-image: url('images/error.png');
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
        <h1>Confirm Sign Up/ Activate</h1>
      </div>
      <div class="about-box">

<?php

if(isset($error)) {
	echo '<div class="errormsgbox"> '.$error. '</div>';
	unset($error);
}
if(isset($success)) {
	echo '<div class="success">'. $success.'</div>';
	unset($success);
}

if(isset($pw_reset)) { ?>

<form action="<?php  basename(__FILE__);?>" method="post" class="registration_form">
  <fieldset>
    <legend>Activation Form </legend>
	<p>Welcome <?php echo $firstname." ".$lastname."!";?><br>
	Please remember your Login ID: <?php echo $email;?></p>

  <div class="elements">
      <label for="password">New Password:</label>
      <input type="password" id="password" name="password1"  size="50" /> (required)
    </div>
  <div class="elements">
      <label for="password">Confirm:</label>
      <input type="password" id="password" name="password2"  size="50" /> (required)
    </div>
 <div class="submit">
      <input type="hidden" name="formsubmitted" value="TRUE" />      
      <input type="hidden" name="email" value="<?php echo urlencode($email); ?>" />      
      <input type="hidden" name="key" value="<?php echo urlencode($key); ?>" />      
      <input type="submit" value="Activate" />
    </div>
</fieldset>
</form>

<?php }; ?>


      </div>
      <div class="body-bottom-box">
        <h2>Voice BroadCast - <span style="color: #009dea;">Click.Set.Go.</span></h2>
      </div>
    </div>
  </div>

  <div id="footer">
<?php include ("includes/footer.html"); ?>
  </div>

</div>

</body>
</html>


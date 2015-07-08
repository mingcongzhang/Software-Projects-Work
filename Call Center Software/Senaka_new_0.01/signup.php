<?php

define('INCLUDE_CHECK',true);

unset($error);
unset($email_sent);

// These files can be included only if INCLUDE_CHECK is defined
include_once("includes/config_twilio.php");
require_once("includes/config_mongo.php"); // establishes connection to database or set $error
require_once("includes/functions_twilio_subaccount.php");
require_once("includes/functions_validateforms.php");
require_once("includes/functions_mandrill.php"); // Mandrill SwiftMailer

$ACTIVATION_URL = 'http://'.$_SERVER['SERVER_NAME'].'/~mingcong/activate.php';


if (!isset($error) && isset($_POST['formsubmitted'])) {

	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$email = $_POST['e-mail'];
	$password = $_POST['password'];
	$phone = $_POST['phone'];
	$companyname = $_POST['companyname'];

	// Validate each field below. Different validation rules apply.
	if (empty($firstname) || !validate_name($firstname)) {
		$error = 'Please Enter a valid First Name!';
	}
	else if (empty($lastname) || !validate_name($lastname)) { 
		$error = 'Please Enter a valid Last Name!';
	}
	else if (empty($email) || validate_email($email)) {
		$error = 'Please Enter a valid Email Address!';
	}
	else if (empty($password)){
		$error = 'Please Enter a Password!';
	}
	else if (!empty($phone) && validate_telnumber($phone)) {
		$error = 'Please Enter a valid Phone Number!';
	}
	else if (!empty($companyname) && validate_companyname($companyname)){
		$error = 'Please Enter a valid Company Name!';
	}
	else {
		// Check if email address already exists in database
		$result = $collection_user->findOne(array('email'=>$email));	

		// If already exists
		if ($result['status'] == "signup"){
			$error = "You have already signed up. Please click the link in 
				your E-mail to activate your account!";
		} elseif ($result['status'] == "active"){
			$error = "There is an existing account associated with this email!";
		} else {

			// create a friendly name for the twilio sub account
			$friendly = !empty($companyname) ? $companyname : $firstname."_".$lastname;

			// create a twilio sub account
			$account = create_twilio_subaccount($friendly, $sid, $token);

			// create a company id using MongoID
			$company_id = new MongoId();  // create a new MongoID for the company	

			// insert company info (including twilio sub account info) into company collection 
			try{
				$ret = $collection_company->insert( array('_id' => $company_id, 
				'name' => $companyname, 'status' => "active", 'price_plan' => "free1",
				'sid' => $account->sid, 'friendly' => $account->friendly_name,
				'twilio_status' => $account->status, 'date_created'=>$account->date_created,
				'date_updated'=>$account->date_updated,'token' =>$account->auth_token,
				'type' => $account->type
				));
			}
			catch(MongoException $e){
				$error = "Failed to insert data in Database. Please try again!";
			} 

			$code = md5(uniqid(rand(), true)); // create a unique activation code
			$password = md5($password); // password must be encrypted

			// store new user in database
			try{
				$collection_user->insert(array('password'=>$password,'email'=>$email,
				'phone'=>$phone,'firstname'=>$firstname,'lastname'=>$lastname,'code'=>$code,
				'type'=>"admin",'status'=>"signup",'company'=>$company_id));
			}	
			catch(MongoException $e){
				$error = "Failed to insert data in Database. Please try again!";
			} 
		}

		if (!$collection_company || !$collection_user){
			$error = 'Database Error';
		} 
	}

	if (!isset($error)){
		// send confirmation email
		//$from = array('support@solidbaseconsulting.com' => 'CallCenter Support');
		$from = 'support@solidbaseconsulting.com';
		$subject = 'Call Center - Please Confirm Sign-Up';
		$format = 'plain';
		$sitepath = $ACTIVATION_URL.'?email='.urlencode($email).'&key='.urlencode($code);
		$email_message=format_email_mandrill($TEMPLATE_EMAIL_SIGNUP,$format,$firstname,$sitepath);
		$body_type='text/'.$format;
		$status=send_email_mandrill($MANDRILL_USERNAME,$MANDRILL_PASSWORD,$from,$email,$subject,$email_message,$body_type);
		$success = "Confirm Your Email Address! A confirmation email has 
		been sent to ".$email." . Click on the Activation Link to activate 
		your account.";
		$email_sent = true;

                $_SESSION['success-msg'] = $success;
	} 
} 

if (isset($error)){
	$_SESSION['error-msg'] = $error;		
}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="Voice BroadCast service" />
<meta name="keywords" content="Voice, Notifications, broadcast, callbroadcast, group" />
<meta name="author" content="SolidBase Consulting LLC" />
<title>Sign-Up Form</title>
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

/* Box Style */

 .success, .warning, .errormsgbox, .validation {
	border: 1px solid;
	margin: 0 auto;
	padding:10px 5px 10px 50px;
	background-repeat: no-repeat;
	background-position: 10px center;
     font-weight:bold;
     width:450px;
     
}
.success {
	color: #4F8A10;
	background-color: #DFF2BF;
	background-image:url('images/success.png');
}
.warning {
	color: #9F6000;
	background-color: #FEEFB3;
	background-image: url('images/warning.png');
}
.errormsgbox {
	color: #D8000C;
	background-color: #FFBABA;
	background-image: url('images/error.png');
	
}
.validation {
	color: #D63301;
	background-color: #FFCCBA;
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
        <h1>Sign Up</h1>
      </div>
      <div class="about-box">

<?php

if(isset($error)) echo '<div class="errormsgbox"> '.$error.'</div>'; 
if(isset($success)) echo '<div class="success">'. $success.'</div>';

?>
       
<?php if(!isset($email_sent)):?>

<form action="<?php echo basename(__FILE__);?>" method="post" class="registration_form">
  <fieldset>
    <legend>Registration Form </legend>

    <p>Create A new Account <span style="background:#EAEAEA none repeat scroll 0 0;line-height:1;margin-left:210px;;padding:5px 7px;">Already a member? <a href="login.php">Log in</a></span> </p>
    
    <div class="elements">
      <label for="fname">First Name:</label>
      <input type="text" id="name" name="firstname" value = "<?php echo $firstname ?>" size="50" /> (required)
    </div>

    <div class="elements">
      <label for="lname">Last Name:</label>
      <input type="text" id="name" name="lastname"  value = "<?php echo $lastname ?>" size="50" /> (required)
    </div>
    
    <div class="elements">
      <label for="e-mail">E-mail:</label>
      <input type="text" id="e-mail" name="e-mail" placeholder="Email Address"  value = "<?php echo $email ?>" size="50" /> (required)
    </div>

    <div class="elements">
      <label for="password">Password:</label>
      <input type="password" id="password" name="password"  size="50" /> (required)
    </div>

    <div class="elements">
      <label for="phone">Phone Number:</label>
      <input type="text" id="name" name="phone" placeholder="10 or 11 digits number"  value = "<?php echo $phone ?>" size="50" /> 
    </div>

    <div class="elements">
      <label for="cname">Company Name:</label>
      <input type="text" id="name" name="companyname"  value = "<?php echo $companyname ?>" size="50" /> 
    </div>

    <div class="submit">
     <input type="hidden" name="formsubmitted" value="TRUE" />
      <input type="submit" value="Register" />
    </div>

  </fieldset>
</form>

<?php endif;?>

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

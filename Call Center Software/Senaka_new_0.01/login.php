<?php

define('INCLUDE_CHECK',true);

// These files can be included only if INCLUDE_CHECK is defined
require_once("includes/config_login.php"); 
require_once("includes/config_mongo.php"); // establishes connection to database or set $error
include_once 'includes/functions_login.php';
 
// Must always start the session prior to getting session parameters
sec_session_start();

// Copy database issues onto $db_error. $error will be cleared soon after
$db_error = $error;

unset($error, $user_type);

if ($_POST['submit'] == "login" && isset($_POST['email'], $_POST['password']) ) {

	// Get the POST parameters
	$email = $_POST['email'];
	$password = $_POST['password']; 
 
	if (login($email, $password, $collection_user) != true) {
		// Login failed 
		$error = "Wrong Username and/or Password! Please try again.";
	}
} 

if (login_check($collection_user) == true) {
	$logged = 'in';
} else {
	$logged = 'out';
}

// Get user type for this user
if (isset($_SESSION['user_type'])) 
	$user_type = $_SESSION['user_type'];

// Get the company ID (MongoID) 
if (isset($_SESSION['company'])) 
	$company_id = new MongoId($_SESSION['company']);

if (isset($db_error)) {
	// copy $db_error, which is the most important/fundamental error, back to $error
	$error = $db_error;
}

else if (isset($user_type) && $user_type == "admin" && (isset($company_id))) {

	try {
		// Get the company information, particularly a_sid
		$result = $collection_company->findOne(array('_id'=>$company_id));
	} catch(MongoException $e) {
		$_SESSION['message'] = "Failed to connect to Database. Please try again!";
		header('Location: logout.php');
		exit;
	} 

	if ($result == NULL) {
		// No match in database
		$error = "Internal Error (14365). Please Try again later!";
	} else if (isset($result['sid']) && isset($result['token'])){
		// Get the sid and token for the company
		$_SESSION['sid'] = $result['sid'];
		$_SESSION['token'] = $result['token'];
		
	} else {
		$error = "Internal Error (14366). Please Try again later!";
	}
}


?>

<!DOCTYPE html>
<html>
<head>
<title>Secure Login: Log In</title>
<link rel="stylesheet" href="styles/main.css" />
</head>
<body>

	<h2>
<?php 
	if (isset($_SESSION['message'])) {
		echo $_SESSION['message']; 
		unset($_SESSION['message']);
	}

	if (isset($error)) 
		echo($error);
?> 
	</h2>

<?php if ($logged == "out") :  ?>

	<h3> Please login.</h3>
        <form action="<?php echo basename(__FILE__);?>" method="post" name="login_form"> 
		Email: <input type="text" name="email" />
		Password: <input type="password" name="password" id="password"/>
		<input type="submit" value="Login" />
		<input type="hidden" name="submit" value="login"> 
        </form>

<?php elseif ($logged == "in") :  ?>

        <p>You are logged <?php echo $logged ?>. 
	If you want, <a href="logout.php"><strong>log out</strong></a> </p>


        <p>User type is: <?php echo $user_type ?>. 
        <p>company id is: <?php echo $company_id ?>. 

<?php endif;?>

	<hr>

<?php if ( isset($user_type) && $user_type == "admin") : ?>

        <p>var_dump company_info is: <?php echo var_dump($result); ?>. 

	<P>ToDo <a href="users.php">Manager Users</a></p>

<?php elseif ( isset($user_type) && $user_type == "admin" ) : ?>

        <p>First you must Configure Twilio by clicking on the twilio-connect button below.
<style type="text/css">
	#twilio-connect-button {
		background: url(https://www.twilio.com/packages/connect-apps/images/connect-button.png);
		width: 130px; height: 34px; display: block;	margin: 0 auto;
	}
	#twilio-connect-button:hover { background-position: 0 34px; }
</style>
<a href="https://www.twilio.com/authorize/CNf22ded458f751ca481a5ed2719d42f4f" id="twilio-connect-button"></a>

<?php else: ?>


<?php endif;?>

</body>


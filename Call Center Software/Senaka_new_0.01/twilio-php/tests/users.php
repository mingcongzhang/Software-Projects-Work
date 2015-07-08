<?php

define('INCLUDE_CHECK',true);

// These files can be included only if INCLUDE_CHECK is defined
require_once("includes/functions_validateforms.php");
require_once("includes/functions_login.php");
require_once("includes/mandrill.php"); // Mandrill SwiftMailer
require_once("includes/database.php"); // establishes connection to database or set $error

// Check if user is logged in.
sec_session_start();
if (login_check($collection_user) != true) {
	header('Location: login.php');
	exit;
} 

// Copy database issues onto $db_error. $error will be cleared soon after
$db_error = $error;

unset($error, $result, $output, $success, $no_users);

$ACTIVATION_URL = 'http://'.$_SERVER['SERVER_NAME'].'/~mingcong/activate.php';

// Check if company_id is stored in the SESSION parameters. Without it, several of the below 
// modules will not work properly. 
if (isset($_SESSION['company'])) {
	// get the company ID (MongoID) 
	$company_id = new MongoId($_SESSION['company']);
} else {
	$error = "Session Error. Please log out and log in again!";
}


// Admin adds a user
if(!isset($db_error) && $_POST['contact'] == "add" ) {

	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$email = $_POST['email'];
	$phone = $_POST['phone'];
	$type = $_POST['type'];

	// Validate each field below. Different validation rules apply.
	if (empty($firstname) || !validate_name($firstname)) {
		$error = 'Please Enter a valid First Name!';
	} else if (empty($lastname) || !validate_name($lastname)) { 
		$error = 'Please Enter a valid Last Name!';
	} else if (empty($email) || validate_email($email)) {
		$error = 'Please Enter a valid Email Address!';
	} else if (!empty($phone) && validate_telnumber($phone)) {
		$error = 'Please Enter a valid Phone Number!';
	} else if(empty($type) || !($type == "supervisor" || $type == "agent") ){
		$error = "Invalid user type";
	} else if (empty($company_id)) {
		$error = "Session Error. Please log out and log in again!";
	} else {
		if(isset($_POST['checkbox'])){
			$q_selected = array();
			foreach($_POST['checkbox'] as $val) { 
				$q_selected[] = $val; 
			} 
		}

		// Check if the user's email address exists in the database
		$repeat = $collection_user->findOne(array('email'=>$email));
		if ($repeat != NULL) {
			$error = "This user already exists!";
		}
	}
	// $error can only be set within the outer loop, and when checking if the user already exists 
	if(!isset($error)){
		// store new user in database
		$code = md5(time().uniqid(rand(),true));
		try{
			$collection_user->insert(array('password'=>$code,'email'=>$email,'phone'=>$phone, 'firstname'=>$firstname,'lastname'=>$lastname,'code'=>$code,'type'=>$type, 'status'=>"signup", 'company'=>$company_id, 'queue'=>$q_selected));
		}	
		catch(MongoException $e) {
			die("Failed to insert data ".$e->getMessage());
		}

		if(!$collection_user) {
			$error = 'Database Error';
		} else {
			$from = array('support@solidbaseconsulting.com' => 'CallBroadCast Support');
			$from = 'support@solidbaseconsulting.com';
			$subject = 'Call Center - Please Confirm Sign-Up';
			$format = 'plain';
	                $sitepath = $ACTIVATION_URL.'?email='.urlencode($email).'&key='.urlencode($code);
			$email_message=format_email_mandrill($TEMPLATE_EMAIL_SIGNUP,$format,$firstname,$sitepath);
			$body_type='text/'.$format;
			$status=send_email_mandrill($MANDRILL_USERNAME,$MANDRILL_PASSWORD,$from,$email,$subject,$email_message,$body_type);
			$success = "A confirmation email has been sent to ".$email;
		}
	}
}
else if(!isset($db_error) && $_POST["contact"] == "delete"){
	$email = $_POST['email'];

	// Validate each field below. Different validation rules apply.
	if (empty($email) || validate_email($email)) {
		$error = 'Please select a valid user to delete!';
	} else if (empty($company_id)) {
		$error = "Session Error. Please log out and log in again!";
	} else {
		$result = $collection_user->remove(array('email'=>$email,
			'company'=>$company_id), array("justOne" => true, "w" => 1));
		if ($result == NULL) {

			// No match in database
			$error = "Database Error (14365). Please Try again later!";
		} else if (isset($result['n'])){
			if ($result['n'] >= 1) $success = "Successfully removed the user: ".$email;
			else $error = "Error removing user ".$email.". Please Try again later!";
		}
	}
}


// Retrieve and show the list of queues corresponding to the company. This allows the admin
// to view the list of available queues and associate select queues with the new user
if (!isset($db_error) && (isset($company_id))) {

	// Get the company information, particularly a_sid
	$result = $collection_company->findOne(array('_id'=>$company_id));

	if ($result == NULL) {
		// No match in database
		$error = "Database Error (14365). Please Try again later!";
	} else if (isset($result['a_sid'])){
		// Get the queues that match the a_sid of the company
		$cursor = $collection_queue->find(array('a_sid'=>$result['a_sid']));
	} else $error = "Database Error (14366). Please Try again later!";

	// No match in database
	if (!isset($cursor)){
		$error = "Database Error (14367). Please Try again later!";
	} elseif ($cursor->count() <= 0){
		$error = "No Queues Found!";
	} else {
		while ($cursor->hasNext()) {
			$result = $cursor->getNext();
			$q_array[$result['q_sid']] = $result['q_name'];
		}
	}
}


// Show the list of non-admin users in the "My Users" area.
if (!isset($db_error) && isset($company_id)) {
	$cursor = $collection_user->find(array('company'=>$company_id,
		'type'=>array('$nin'=>array('admin'))));

	if (!isset($cursor)){
		$output = "<tr><td></td><td>Database Error (14368). Please try again!</td>";
	}
	else { 
		$fmt1 = "<tr><td><input type='radio' name='email' value='%s'/></td><td>%s</td>";
		$fmt2 = "<td>%s</td> <td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>\n";
		while ($cursor->hasNext()) {
			$result = $cursor->getNext();

			unset($queue_str);
			foreach($result['queue'] as $val){
				$queue_str .= "<li>".$val."</li>";
			}
			if(!isset($queue_str)) $queue_str = "<li>Not Assigned</li>";

			$output .=sprintf($fmt1,$result['email'],$result['phone']);
			$output .=sprintf($fmt2,$result['firstname'],$result['lastname'],
				$result['email'],$result['type'],$result['status'],$queue_str);
		}
	}
	if(!isset($output)) {
		$output = "<tr><td></td><td>No Users! Please add a user</td>";
		$no_users = true;
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
       <h1>Management</h1>
      </div>
      <div class="about-box">

<?php

if(isset($error)) 
	echo '<div class="errormsgbox"> '.$error.'</div>';
if(isset($success)) 
                echo '<div class="success">'. $success.'</div>';

?>


<h3>Add a User</h3>

<div class="callform">
  <form name="input" action="<?php echo basename(__FILE__);?>" method="post">
  <input type="hidden" name="contact" value="add">
  <div class="form-item">
        <table class="number-to-call">
	<th>Action</th> <th>Phone Number</th> <th>First Name</th> <th>Last Name</th><th>E-mail</th><th>Type</th><th>Agent Queue</th>
	<TR><TD><input type="submit" value="Add" name"submit" ></TD>
	<TD><input type="text" name="phone" placeholder="Optional"  size="15" ></TD>
	<TD><input type="text" name="firstname" placeholder="Required" size="15" ></TD>
	<TD><input type="text" name="lastname" placeholder="Required" size="15" ></TD>
	<TD><input type="text" name="email" placeholder="Required" size="15" ></TD>
	<TD><select type="text" name="type">
		<option value="supervisor">Supervisor</option>
		<option value="agent">Agent</option>
	<select></TD>
	<TD>
<?php foreach ($q_array as $q => $val) { ?>
	<input type="checkbox" name="checkbox[]" value="<?php echo $q; ?>" > <?php echo $val; ?><br> 
<?php } if(!isset($q_array)) echo "Not Available"; ?>
	</TD>
	</TR>
        </TABLE>
  </div>
  </form>
</div>

<hr/>
<p>

<h3>My Users</h3>

<div class="form-item">
<?php if(!isset($no_users)) : ?>
	<form name="input" action="<?php echo basename(__FILE__);?>" method="post">
	<input type="hidden" name="contact" value="delete">
<?php endif;?>

	<table class="number-to-call">
	<th>Action</th> <th>Phone Number</th> <th>First Name</th> <th>Last Name</th> 
		<th>E-mail</th> <th>Type</th> <th>Status</th> <th>Agent Queue</th>
	<?php echo $output; ?>
	</table>
<?php if(!isset($no_users)) : ?>
	<input type="submit" value="Delete" name"submit" />
	</form>
<?php endif;?>
</div>

        </div>
      <div class="body-bottom-box">
	<h2>Voice BroadCast - <span style="color: #009dea;">Click.Set.Go</span></h2>
      </div>
    </div>
  </div>
  <div id="footer">
<?php
include ("includes/footer.html");
?>
  </div>
</div>
</body>
</html>

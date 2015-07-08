<?php

define('INCLUDE_CHECK',true);

$PRE_DIR = "../";

// These files can be included only if INCLUDE_CHECK is defined
require_once("$PRE_DIR"."includes/config_login.php");
require_once("$PRE_DIR"."includes/config_mongo.php"); 		// connect to database or set $error
require_once("$PRE_DIR"."includes/config_mandrill.php");
require_once("$PRE_DIR"."includes/functions_validateforms.php");
require_once("$PRE_DIR"."includes/functions_login.php");
require_once("$PRE_DIR"."includes/functions_mandrill.php"); 	// Mandrill SwiftMailer

// start the session
sec_session_start();

// Check if user is logged in.
if (login_check($collection_user) != true) {
	header("Location: $PRE_DIR/login.php");
	exit;
} 

// Copy database error onto $db_error. $error will be cleared soon after
if(isset($error))
	$db_error = $error;


unset($error, $success);

$ACTIVATION_URL = 'http://'.$_SERVER['SERVER_NAME'].'/activate.php';

// Check if company_id is stored in the SESSION parameters. Without it, several of the below 
// modules will not work properly. 
if (isset($_SESSION['company'])) {
	// get the company ID (MongoID) 
	$company_id = new MongoId($_SESSION['company']);
} else {
	$error = "Session Error. Please log out and log in again!";
}


if($_POST['submit']=="cancel"){
	unset($_SESSION['edit_user']);
}
//after "Save" button is clicked for editing current user
elseif ( !isset($error) && isset($company_id, $_SESSION['edit_user']) && $_POST['submit']=="save"){

	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$email = $_POST['email'];
	$group = $_POST['select_group'];

	if(!isset($error) && (empty($firstname) || empty($lastname))){
		$error = 'Please Enter a valid First Name or/and Last Name!';
	}
	if (!isset($error) && (empty($email) || validate_email($email))) {
		$error = 'Please Enter a valid Email Address!';
	}
	if(!isset($error) && !isset($group)){
		$error = 'Please select group(s) for the New User!';
	}

	if(!isset($error)){
		try {
			$cursor_user = $collection_user->findOne(array(
						'email'=>$email,
						'company'=>$company_id
						));
		}
		catch(MongoException $e) {
			die("Failed to access data ".$e->getMessage());
		}

		if(isset($cursor_user)){
			try {
				$collection_user->update(
					array('email'=>$email),
					array('$set'=>(array(
							'firstname' => $firstname,
							'lastname' => $lastname,
							'group_id' => $group,
							'last_update' => time()
							)
						)
					),
					array('upsert'=>false)
				);
			}
			catch(MongoException $e) {
				die("Failed to update data ".$e->getMessage());
			}
			$success = "Changes are saved successfully!";
		} else {
			$code = md5(time().uniqid(rand(),true));
			try{
				$collection_user->insert(array(
						'password' => $code,
						'email' => $email,
						'firstname' => $firstname,
						'lastname' => $lastname,
						'code' => $code,
						'status' => "signup", 
						'company' => $company_id,
						'group_id' => $group,
						'online_status' => "0",
						'last_update' => time()
				));
			}
			catch(MongoException $e) {
				die("Failed to insert data ".$e->getMessage());
			}

			if(!$collection_user) {
				$error = 'Database Error';
			} else {
				$from = 'support@solidbaseconsulting.com';
				$subject = 'Call Center - Please Confirm Sign-Up';
				$format = 'plain';
				$token = explode('/',__DIR__);
				$sitepath = ($ACTIVATION_DIR.DIRECTORY_SEPARATOR.'~'.$token[sizeof($token)-3].DIRECTORY_SEPARATOR.'activate.php').'?email='.urlencode($email).'&key='.
						urlencode($code);
				$email_message = format_email_mandrill($TEMPLATE_EMAIL_SIGNUP, 
					$format, $firstname, $sitepath);
				$body_type = 'text/'.$format;
				$status = send_email_mandrill($MANDRILL_USERNAME, $MANDRILL_PASSWORD, 
					$from, $email, $subject, $email_message, $body_type);
				$success = "A confirmation email has been sent to ".$email;
			}	
		}
	}
	if(!isset($error)){
		unset($_SESSION['edit_user']);//back to first page
		unset($cursor_user);
	}
}


// A current user is selected, so retrieve the user's info to edit
elseif( !isset($error, $_SESSION['edit_user']) && isset($company_id) && $_POST['contact'] == 'add' ){
		$name_selected = $_POST['submit'];
		$pos = strrpos($name_selected, " ");
		$_SESSION['firstname'] = substr($name_selected, 0, $pos);
		$_SESSION['lastname'] = substr($name_selected, $pos+1);
		try{
			$cursor_user = $collection_user->findOne(array(
				'firstname'=>$_SESSION['firstname'] ,
				'lastname'=>$_SESSION['lastname'],
				'company'=>$company_id
			));
			$cursor_group = $collection_group->findOne(array('_id'=>		$company_id));
		}
		catch(MongoException $e) {
			die("Failed to access data ".$e->getMessage());
		}
		$_SESSION['email_selected'] = $cursor_user['email'];
		$_SESSION['online_status_selected'] = $cursor_user['online_status'];
		$_SESSION['group_selected'] = $cursor_user['group_id'];
		$_SESSION['groups_selected'] = $cursor_group['label'];
		$_SESSION['edit_user'] = 1;
		unset($cursor_user,$cursor_group);
}
// "Add New Agent" is clicked to add a new agent
elseif (!isset($error, $_SESSION['edit_user']) && isset($company_id) && $_POST['add']){
		try{
			$cursor_group = $collection_group->findOne(array('_id'=>$company_id));
		}
		catch(MongoException $e) {
			die("Failed to access data ".$e->getMessage());
		}
		$_SESSION['groups_selected'] = $cursor_group['label'];
		$_SESSION['edit_user']=2;
}

if (isset($db_error)) 
	$error = $db_error;

?>

<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="Voice BroadCast service" />
<meta name="keywords" content="Voice, Notifications, broadcast, callbroadcast, group" />
<meta name="author" content="SolidBase Consulting LLC" />
<title>CallBroadCast - CallCenter Software</title>
<style type="text/css">
body {
	font-family:"Lucida Grande", "Lucida Sans Unicode", Verdana, Arial, Helvetica, sans-serif;
	font-size:12px;
}
input {padding: 0;border: none;background: #D8D8D8;} 
.bigbutton {width: 6em;  height: 2em;}.bigbutton2 {height: 2em;}
 .success, .warning, .errormsgbox, .validation {
	border: 1px solid;
	margin: 0 auto;
	padding:10px 5px 10px 50px;
	background-repeat: no-repeat;
	background-position: 10px center;
     font-weight:bold;
     width:450px;  
}
.errormsgbox {
	color: #D8000C;
	background-color: #FFBABA;
	background-image: url('images/error.png');
}
.success {
	color: #4F8A10;
	background-color: #DFF2BF;
	background-image:url('images/success.png');
}
</style></head>

<?php include ("includes/header.html");?>
<body>
<fieldset>
      <div class="inner-welcome-box">
       <h1>Agents</h1>

      </div>

<!--=================show first page===============-->

<div>
<?php if( !isset($error, $_SESSION['edit_user']) && isset($company_id) ){ ?>

<form name="input" action="<?php echo basename(__FILE__);?>" method="post">
	<p><input class="bigbutton2" type=submit value="Add New Agent" name="add" ></p>
</form>
</div>

<div>
<form  action="<?php echo basename(__FILE__);?>" method="post">

<?php
	$cursor_user = $collection_user->find( array('company' => $company_id) );

	if (!isset($cursor_user)){
		$error = "Database Error (14368). Please try again!";
	}
	else { ?> 
		<input type="hidden" name="contact" value="add">

		<?php while($cursor_user->hasNext()){
			$result = $cursor_user->getNext();
		?>	
			<p><input class="bigbutton2" type=submit value="<?php echo $result['firstname'].
			" ".$result['lastname'];?>" name="submit" >(<?php echo $result['email']; ?>) 
			&nbsp;&nbsp;&nbsp;&nbsp;

Groups: 
		<?php	if( !isset($result['group_id'] )){
			echo "None";
			} else {
				$group_str = "";
				foreach($result['group_id'] as $var){
					$group_str .= $var." ";
				}
				echo $group_str;
			} ?> 
			</p>
<?php		}
	}
} ?>

</form>
</div>

<!--============ show error & success ============-->

<?php
if(isset($error)) 
	echo '<div class="errormsgbox"> '.$error.'</div>';

if(isset($success)) 
	echo '<div class="success">'. $success.'</div>';
?>


<!--==============User settings=================-->

<?php if(isset($_SESSION['edit_user'])): ?>

<form action="<?php echo basename(__FILE__); ?>" method="post">
<input type="hidden" name="submit" value="save"> 
<div>
<p>
<?php	if ($_SESSION['edit_user'] == 1){
		echo "<h2>".$_SESSION['firstname']." ". $_SESSION['lastname']."</h2>\n";
	} else
		echo "<h2>New Agent</h2>\n";
?>
</p>

<p>
<label for="textfield">First Name*:</label>
  	<input type="text" name="firstname" id="textfield" 
	<?php if( $_SESSION['edit_user'] == 1 && isset($_SESSION['firstname'])) 
	echo 'value='.$_SESSION['firstname']; ?> />

<label for="textfield">Last Name*:</label>
  	<input type="text" name="lastname" id="textfield1" 
	<?php if($_SESSION['edit_user'] == 1 && isset($_SESSION['lastname'])) 
	echo 'value='.$_SESSION['lastname']; ?> />
</p>

<p>
<label for="email">Email*:</label>
	<input type="email" name="email" id="email" 
	<?php if($_SESSION['edit_user'] == 1 && isset($_SESSION['email_selected']))
	echo 'value='.$_SESSION['email_selected']; ?> size="30" />
</p>


<p>
<label>Status:</label>
<label class="bigbutton">
<?php echo ($_SESSION['edit_user'] == 1 && $_SESSION['online_status_selected'] == 1) ? Online:Offline ?>
</label> 
</p>

<p>
<label>Groups*:  
	<blockquote>
		<blockquote>
<?php 
$group = $_SESSION['group_selected'] ;
$groups = $_SESSION['groups_selected'] ;

if($_SESSION['edit_user'] == 2){

	for( $i=0; $i < sizeof($groups); $i++ )
		echo "<div><input type='checkbox' name='select_group[]' 
		value='$groups[$i]' id='$i' >$groups[$i]</div>\n";
} 
else if($_SESSION['edit_user'] == 1){

	for( $i=0; $i < sizeof($groups); $i++ ){
		$checked = in_array($groups[$i],$group) ? "checked" : null;

		echo "<div><input type='checkbox' name='select_group[]' 
		value='$groups[$i]' id='$i' $checked >$groups[$i]</div>\n";
	}
}
?>
		</blockquote>
	</blockquote>
</p>
		

<p>
      <div class="form-item-field">
		<input class="bigbutton" type="submit" value="Save" />
</form>
</p><p>

<form action="<?php echo basename(__FILE__);?>" method="post">
<input type="hidden" name="submit" value="cancel"> 
<input class="bigbutton" type="submit" value="Cancel" />
	</div>

</p>
</div>

</form>
<?php endif; ?>


 <div class="body-bottom-box">
	<h2>Voice BroadCast - <span style="color: #009dea;">Click.Set.Go</span></h2>
      </div>
    </div>
  </div>
  <div id="footer">
<?php
//include ("includes/footer.html");
?>
  </div>
</div>
</fieldset>
</body>
</html>

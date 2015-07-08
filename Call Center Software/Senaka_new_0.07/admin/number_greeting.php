<?php

/**
 * This file allows an admin to configure messages such as the "welcome message" 
 * for a given telephone number. 
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
require_once("$PRE_URL"."twilio-php/Services/Twilio.php"); // Load the PHP helper library 

// Copy database issues onto $db_error. $error will be cleared soon after
if(isset($error)) 
	$db_error = $error;

unset($error, $output, $num_found, $have_updates );

// start session
sec_session_start();

// Check if user is logged in.
if (login_check ($collection_user) != true) {
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
// no need to check isset($company_id) below this line, just check for isset($error)

//return to initial greeting page
if($_POST['return']){
	unset( $_SESSION['edit_greeting'] );
}
//save one of greetings
elseif (isset($_SESSION['edit_greeting'])&&!isset($_POST['phone'])){
//save "Welcome Greeting"
	if($_POST['set_WG']){
		try{
			$collection_phonenum->update(
					array('phone_number' => $_SESSION['phone_selected']),
					array('$set' => ( array(
						'welcome_greeting' => array(
							'name' => $_POST['welcome_name'],
							'content' => $_POST['welcome_content']
						), 
						'last_update' => time())
					)),
					array('upsert' => false)
				);
		}
		catch(MongoException $e) {
			die("Failed to update data ".$e->getMessage());
		}	
//save "Available Agents Greeting"
	} elseif ($_POST['set_AAG']){
		try{
			$collection_phonenum->update(
					array('phone_number' => $_SESSION['phone_selected']),
					array('$set' => (array(
						'available_agents_greeting' => array(
							'name' => $_POST['available_agents_name'],
							'content' => $_POST['available_agents_content']
						), 
						'last_update' => time())
					)),
					array('upsert' => false)
				);
		}
		catch(MongoException $e) {
			die("Failed to update data ".$e->getMessage());
		}
//save "Waiting Message"
	} elseif ($_POST['set_WM']){
		try{
			$collection_phonenum->update(
					array('phone_number' => $_SESSION['phone_selected']),
					array('$set' => (array(
						'waiting_message' => array(
							'name' => $_POST['waiting_msg_name'],
							'content' => $_POST['waiting_msg_content']
						),
											'last_update' => time())
					)),
					array('upsert'=>false)
				);
		}
		catch(MongoException $e) {
			die("Failed to update data ".$e->getMessage());
		}
//save "Voicemail Message"		
	} elseif ($_POST['set_VM']){
		try{
			$collection_phonenum->update(
					array('phone_number' => $_SESSION['phone_selected']),
					array('$set'=>(array(
						'voice_message'=> array(
							'name'=>$_POST['voicemail_msg_name'],
							'content'=>$_POST['voicemail_msg_content']
						),
											'last_update' => time())
					)),
					array('upsert'=>false)
				);
		}
		catch(MongoException $e) {
			die("Failed to update data ".$e->getMessage());
		}
//save "Outside Business Hours Message"		
	} elseif ($_POST['set_OBHM']){
		try{
			$collection_phonenum->update(
					array('phone_number' => $_SESSION['phone_selected']),
					array('$set'=>(array(
						'outside_hours_message'=> array(
							'name'=>$_POST['outside_hours_name'],
							'content'=>$_POST['outside_hours_content']
						),
											'last_update' => time())
					)),
					array('upsert'=>false)
				);
		}
		catch(MongoException $e) {
			die("Failed to update data ".$e->getMessage());
		}	
//save "Delay Announcement Message"		
	} elseif ($_POST['set_DAM']){
		try{
			$collection_phonenum->update(
					array('phone_number' => $_SESSION['phone_selected']),
					array('$set'=>(array(
						'delay_announcement_message'=> array(
							'name'=>$_POST['delay_announce_name'],
							'content'=>$_POST['delay_announce_content']
						),
											'last_update' => time())
					)),
					array('upsert'=>false)
				);
		}
		catch(MongoException $e) {
			die("Failed to update data ".$e->getMessage());
		}
//save "Waiting Queue Full Message"		
	} elseif ($_POST['set_WQFM']){
		try{
			$collection_phonenum->update(
					array('phone_number' => $_SESSION['phone_selected']),
					array('$set'=>(array(
						'full_queue_message'=> array(
							'name'=>$_POST['full_queue_name'],
							'content'=>$_POST['full_queue_content']
						),
											'last_update' => time())
					)),
					array('upsert'=>false)
				);
		}
		catch(MongoException $e) {
			die("Failed to update data ".$e->getMessage());
		}
	}
}
//a phone number is chosen
elseif (isset($_POST['phone'])){
	$_SESSION['phone_selected'] = $_POST['submit'];
	if(!isset($_SESSION['edit_greeting'])){
		$_SESSION['edit_greeting'] = 1;
	}
}

// <!--==============Show list of phone numbers=================-->
if( !isset($error, $_SESSION['edit_greeting'] ) ){ 
	$phone_number_list = "";

	// html format to show list of phone numbers
	$fmt1 = "<input class='bigbutton2' type='submit' value='%s' name='submit'><p>\n";
	try{
		$cursor = $collection_phonenum->find( array('company'=>$company_id));
	}
	catch(MongoException $e) {
		die("Failed to access data ".$e->getMessage());
	}
	if (!isset($cursor)){
		$error = "Database Error (14368). Please try again!";
	} else { 
		while($cursor->hasNext()){
			$result = $cursor->getNext();
			$phone_number_list .=sprintf($fmt1, $result['phone_number']);
		}
	}
	unset($cursor);
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
input {padding: 0;border: none;background: #D8D8D8;} 
div1 {
    display: inline-block;
    background-color:yellow;
    background-size: 100% 100%;
    background-repeat:no-repeat;
    min-height: 75px;
    min-width: 350px;
    outline: 0;
    padding: 30px 30px 40px 20px;
}textarea {
   resize: none;
}
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
}.label-style{
background-color:#66CCFF;
width:auto;
}
</style>


</head>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript" src="animatedcollapse.js">
</script>
<script type="text/javascript">
animatedcollapse.addDiv('animate1', 'fade=1,height=160px')
animatedcollapse.addDiv('animate2', 'fade=1,height=160px')
animatedcollapse.addDiv('animate3', 'fade=1,height=160px')
animatedcollapse.addDiv('animate4', 'fade=1,height=160px')
animatedcollapse.addDiv('animate5', 'fade=1,height=160px')
animatedcollapse.addDiv('animate6', 'fade=1,height=160px')
animatedcollapse.addDiv('animate7', 'fade=1,height=160px')
animatedcollapse.ontoggle=function($, divobj, state){ //fires each time a DIV is expanded/contracted
	//$: Access to jQuery
	//divobj: DOM reference to DIV being expanded/ collapsed. Use "divobj.id" to get its ID
	//state: "block" or "none", depending on state
}
animatedcollapse.init()
</script>
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

<!--==============Show list of phone numbers=================-->

<h3>Greeting Setting </h3>
<form  action="<?php echo basename(__FILE__);?>" method="post">
<input type="hidden" name="phone" value="add">
<br/>
<?php echo $phone_number_list; ?>
<br/>
</form>

<!--==================show error==================-->

<?php
if(isset($error)) 
	echo '<div class="errormsgbox"> '.$error.'</div>';

if (isset($_SESSION['message'])) {
	echo '<div class="success">'. $_SESSION['message'].'</div>';
	unset($_SESSION['message']);
}
?>

<!--==============Greeting Settings===============-->

<?php if(isset($_SESSION['edit_greeting'])): ?>
<h2><?php echo $_SESSION['phone_selected'] ; ?></h2>
<h3>Greetings </h3><br/>

<?php 
$result = $collection_phonenum->findOne(array('phone_number'=>$_SESSION['phone_selected']));
?>

<label style = 'display:inline-block;width:250px;'>Welcome Greeting </label><a href="javascript:animatedcollapse.toggle('animate1')"><label>Expand/Collapse</label></a>
<br/><br/>&nbsp;&nbsp;&nbsp; 
<?php $welcome_array = $result['welcome_greeting']; ?>
<label class="label-style"><?php echo $welcome_array['name']; ?></label> 
<div id="animate1" style="width: 500px; display:none">
<form  action="<?php echo basename(__FILE__);?>" method="post">
<input type="hidden" name="set_WG" value="add">
<p></p>
<label>Name*</label> <input type="text" name="welcome_name" value="<?php echo $welcome_array['name']; ?>" > <p></p>
<label>TTS Message*</label> <textarea style="width: 200px; height: 90px;" name="welcome_content"><?php echo $welcome_array['content']; ?></textarea><br/><br/>
<input class="bigbutton2" type=submit value= 
"Save"
name="submit">  
</form>
</div><br/><br/>


<label style = 'display:inline-block;width:250px;'>Available Agents Greeting </label><a href="javascript:animatedcollapse.toggle('animate2')"><label>Expand/Collapse</label></a> 
<br/><br/>&nbsp;&nbsp;&nbsp; 
<?php $available_agents_array = $result['available_agents_greeting']; ?>
<label class="label-style"><?php echo $available_agents_array['name']; ?></label> 
<div id="animate2" style="width: 500px; display:none">
<form  action="<?php echo basename(__FILE__);?>" method="post">
<input type="hidden" name="set_AAG" value="add">
<p></p>
<label>Name*</label> <input type="text" name="available_agents_name" value="<?php echo $available_agents_array['name'];  ?>" > <p></p>
<label>TTS Message*</label> <textarea style="width: 200px; height: 90px;"  name="available_agents_content"><?php echo $available_agents_array['content'];  ?></textarea><br/><br/>
<input class="bigbutton2" type=submit value= 
"Save"
name="submit">  
</form>
</div><br/><br/>


<label style = 'display:inline-block;width:250px;'>Waiting Message </label><a href="javascript:animatedcollapse.toggle('animate3')"><label>Expand/Collapse</label></a> 
<br/><br/>&nbsp;&nbsp;&nbsp; 
<?php $waiting_message_array = $result['waiting_message']; ?>
<label class="label-style"><?php echo $waiting_message_array['name']; ?></label> 
<div id="animate3" style="width: 500px; display:none">
<form  action="<?php echo basename(__FILE__);?>" method="post">
<input type="hidden" name="set_WM" value="add">
<p></p>
<label>Name*</label> <input type="text" name="waiting_msg_name" value="<?php echo $waiting_message_array['name'];  ?>" > <p></p>
<label>TTS Message*</label> <textarea style="width: 200px; height: 90px;"  name="waiting_msg_content"><?php echo $waiting_message_array['content']; ?> </textarea><br/><br/>
<input class="bigbutton2" type=submit value= 
"Save"
name="submit">  
</form>
</div><br/><br/>


<label style = 'display:inline-block;width:250px;'>Voicemail Message </label><a href="javascript:animatedcollapse.toggle('animate4')"><label>Expand/Collapse</label></a> 
<br/><br/>&nbsp;&nbsp;&nbsp; 
<?php $voice_message_array = $result['voice_message']; ?>
<label class="label-style"><?php echo $voice_message_array['name']; ?></label> 
<div id="animate4" style="width: 500px; display:none">
<form  action="<?php echo basename(__FILE__);?>" method="post">
<input type="hidden" name="set_VM" value="add">
<p></p>
<label>Name*</label> <input type="text" name="voicemail_msg_name" value="<?php echo $voice_message_array['name'];  ?>" > <p></p>
<label>TTS Message*</label> <textarea style="width: 200px; height: 90px;"  name="voicemail_msg_content"><?php echo $voice_message_array['content'];   ?></textarea><br/><br/>
<input class="bigbutton2" type=submit value= 
"Save"
name="submit">  
</form>
</div><br/><br/>


<label style = 'display:inline-block;width:250px;'>Outside Business Hours Message </label><a href="javascript:animatedcollapse.toggle('animate5')"><label>Expand/Collapse</label></a> 
<br/><br/>&nbsp;&nbsp;&nbsp; 
<?php $outside_hours_array = $result['outside_hours_message']; ?>
<label class="label-style"><?php echo $outside_hours_array['name']; ?></label> 
<div id="animate5" style="width: 500px; display:none">
<form  action="<?php echo basename(__FILE__);?>" method="post">
<input type="hidden" name="set_OBHM" value="add">
<p></p>
<label>Name*</label> 
<input type="text" name="outside_hours_name" value="<?php echo $outside_hours_array['name']; ?>">
<p></p>
<label>Message*</label> 
<textarea style="width: 200px; height: 90px;"  name="outside_hours_content"><?php echo $outside_hours_array['content'];  ?> </textarea>
<br/><br/>
<input class="bigbutton2" type=submit value="Save" name="submit">  
</form>
</div>
<br/><br/>


<label style = 'display:inline-block;width:250px;'>Delay Announcement Message </label><a href="javascript:animatedcollapse.toggle('animate6')"><label>Expand/Collapse</label></a> 
<br/><br/>&nbsp;&nbsp;&nbsp; 
<?php $delay_announcement_message_array = $result['delay_announcement_message']; ?>
<label class="label-style"><?php echo $delay_announcement_message_array['name']; ?></label> 
<div id="animate6" style="width: 500px; display:none">
<form  action="<?php echo basename(__FILE__);?>" method="post">
<input type="hidden" name="set_DAM" value="add">
<p></p>
<label>Name*</label> <input type="text" name="delay_announce_name" value= "<?php echo $delay_announcement_message_array['name']; ?>" > <p></p>
<label>TTS Message*</label> <textarea style="width: 200px; height: 90px;"  name="delay_announce_content"><?php echo $delay_announcement_message_array['content']; ?> </textarea><br/><br/>
<input class="bigbutton2" type=submit value= 
"Save"
name="submit">  
</form>
</div><br/><br/>


<label style = 'display:inline-block;width:250px;'>Waiting Queue Full Message </label><a href="javascript:animatedcollapse.toggle('animate7')"><label>Expand/Collapse</label></a> 
<br/><br/>&nbsp;&nbsp;&nbsp; 
<?php $full_queue_message_array = $result['full_queue_message']; ?>
<label class="label-style"><?php echo $full_queue_message_array['name']; ?></label> 
<div id="animate7" style="width: 500px; display:none">
<form  action="<?php echo basename(__FILE__);?>" method="post">
<input type="hidden" name="set_WQFM" value="add">
<p></p>
<label>Name*</label> <input type="text" name="full_queue_name" value= "<?php echo $full_queue_message_array['name']; ?>" > <p></p>
<label>TTS Message*</label> <textarea style="width: 200px; height: 90px;"  name="full_queue_content"><?php echo $full_queue_message_array['content']; ?> </textarea><br/><br/>
<input class="bigbutton2" type=submit value= 
"Save"
name="submit">  
</form>
</div><br/><br/>

<?php unset($result);endif; ?>


<!--=============Return==================-->
<?php if(isset( $_SESSION['edit_greeting'] )): ?>
<div><br/>
<form  action="<?php echo basename(__FILE__);?>" method="post">
<input type="hidden" name="return" value="add">
<input class="bigbutton2" type=submit value="Return" >
</form>
</div>
<?php endif; ?>
<!--========================================-->
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


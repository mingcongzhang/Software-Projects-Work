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


if($_POST['return']){
	unset( $_SESSION['edit_IVR'] );
}
elseif(isset($_SESSION['edit_IVR'])&& $_POST['save1']){
	if($_POST['save1']){
		$collection_phonenum->update(array('phone_number'=>$_SESSION['phone_selected']),array('$set'=>(array('ivr_message'=>array('name'=>$_POST['ivr_message_name'],'content'=>$_POST['ivr_message_content'])))),array('upsert'=>false));
	}

}
elseif(!isset($_SESSION['edit_IVR'])&&$_POST['phone']){
	$_SESSION['phone_selected'] = $_POST['submit'];
	$cursor1 = $collection_phonenum->findOne(array('company'=>$company_id));
	$_SESSION['edit_IVR']=1;
	unset($cursor,$cursor2);
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
animatedcollapse.addDiv('alan1', 'fade=1,height=160px')
animatedcollapse.addDiv('alan2', 'fade=1,height=160px')
animatedcollapse.addDiv('alan3', 'fade=1,height=160px')
animatedcollapse.addDiv('alan4', 'fade=1,height=160px')
animatedcollapse.addDiv('alan5', 'fade=1,height=160px')
animatedcollapse.addDiv('alan6', 'fade=1,height=160px')
animatedcollapse.addDiv('alan7', 'fade=1,height=160px')
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

<?php if(!isset($error)&&isset($company_id)&&!isset($_SESSION['edit_IVR'])){ ?>
<h3>IVR Setting </h3><br/>
<form  action="<?php echo basename(__FILE__);?>" method="post">
<?php $cursor = $collection_phonenum->find(array('company'=>$company_id));
	if (!isset($cursor)){
		$error = "Database Error (14368). Please try again!";
	}else{ ?> 
<input type="hidden" name="phone" value="add">
		<?php while($cursor->hasNext()){
			$result = $cursor->getNext();?>	
<input class="bigbutton2" type=submit value= 
<?php echo $result['phone_number']; ?> 
name="submit">  
<br/><br/>
<?php	}}unset($result); } ?>
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

<!--==============IVR Settings===============-->

<?php if(isset($_SESSION['edit_IVR'])): ?>
<h2><?php echo $_SESSION['phone_selected'] ; ?></h2>
<h3>IVR </h3><br/>

<?php 
$result = $collection_phonenum->findOne(array('phone_number'=>$_SESSION['phone_selected']));
?>

<label>IVR Message </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="javascript:animatedcollapse.toggle('alan1')"> <label>Expand/Collapse</label> </a>
<br/><br/>&nbsp;&nbsp;&nbsp; 
<?php $ivr_message_array = $result['ivr_message']; ?>
<label class="label-style"><?php echo $ivr_message_array['name']; ?></label> 
<div id="alan1" style="width: 500px; display:none">
<form  action="<?php echo basename(__FILE__);?>" method="post">
<input type="hidden" name="save1" value="add">
<p></p>
<label>Name*</label> <input type="text" name="ivr_message_name" placeholder=<?php echo $ivr_message_array['name']; ?> > <p></p>
<label>TTS Message*</label> <textarea style="width: 200px; height: 90px;" name="ivr_message_content"><?php echo $ivr_message_array['content']; ?></textarea><br/><br/>
<input class="bigbutton2" type=submit value= 
"Save"
name="submit">  
</form>
</div><br/><br/>



<script src="addIVR.js" language="Javascript" type="text/javascript"></script>
<form action="<?php echo basename(__FILE__);?>" method="post">
     <div id="dynamicInput">
           <br><label>When the caller presses:</label><select name="number_choice[]"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="0">0</option></select>&nbsp;<label>then:</label><select name="route_choice[]"><option value="1">route to</option><option value="2">send to voicemail</option><option value="3">hang up call</option><option value="4">forward to phone number</option><option value="5">send to new IRV</option></select> 
     </div>
     <input class="bigbutton2" type="button" value="New IVR Prompt" onClick="addInput('dynamicInput');">
</form>




<?php endif; ?>

<!--=============Return==================-->
<?php if(isset( $_SESSION['edit_IVR'] )): ?>
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

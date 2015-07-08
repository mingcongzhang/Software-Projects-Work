<?php

define('INCLUDE_CHECK',true);
$PRE_DIR = "../";
// These files can be included only if INCLUDE_CHECK is defined
require_once("$PRE_DIR"."includes/config_login.php");
require_once("$PRE_DIR"."includes/config_mandrill.php");
require_once("$PRE_DIR"."includes/config_mongo.php"); // establishes connection to database or set $error
require_once("$PRE_DIR"."includes/functions_validateforms.php");
require_once("$PRE_DIR"."includes/functions_login.php");
require_once("$PRE_DIR"."includes/functions_mandrill.php"); // Mandrill SwiftMailer
require_once("$PRE_DIR"."includes/select_list.php"); 

// Check if user is logged in.
sec_session_start();
if (login_check($collection_user) != true) {
	header("Location: ".$PRE_DIR."login.php");
	exit;
} 

// Check if user is admin or supervisor; Only an admin or supervisor has access to this page
if ( $_SESSION['user_type'] != "admin") {
	$_SESSION['message'] = "ERROR: You are not authorized to perform this action!";
	header("Location: ".$PRE_DIR."login.php");
	exit;
}


// Copy database issues onto $db_error. $error will be cleared soon after
if(isset($error)){
	$db_error = $error;
}

unset($error, $result_company, $output, $success, $no_users, $cursor, $result_group);

// Check if company_id is stored in the SESSION parameters. Without it, several of the below 
// modules will not work properly. 
if (isset($_SESSION['company'])) {
	// get the company ID (MongoID) 
	$company_id = new MongoId($_SESSION['company']);
} else {
	$error = "Session Error. Please log out and log in again!";
}

if(!isset($error)&&isset($company_id)&&isset($_POST['submit'])){
	$_SESSION['timezone_new'] = $_POST['select_timezone'];
	$_SESSION['timeout_inactivity_new'] = $_POST['select_inactivity'];
	$_SESSION['business_hours_new'] = $_POST['BusinessHours'];
	$_SESSION['start_time_new'] = $_POST['select_start'];
	$_SESSION['end_time_new'] = $_POST['select_end'];
	$_SESSION['inboundcall_recording_new'] = $_POST['Inbound_Recording'];
	$_SESSION['outboundcall_recording_new'] = $_POST['Outbound_Recording'];
	$_SESSION['number_agents_ring_new'] = $_POST['select_numbertoring'];
	$_SESSION['queue_size_new'] = $_POST['select_queuesize'];
	$groups = $_POST['groupInput'];
	$cursor = $collection_group->findOne(array('_id'=>$company_id));
	if(!isset($cursor)){
		$groups[]="Admin";
		$groups[]="Agent";
		$groups = array_unique($groups) ;
		$groups = array_filter($groups) ;
		$groups = array_values($groups) ;
		$_SESSION['groups_new'] = $groups;
		try{
		$collection_group->insert(array('_id'=>$company_id,'label'=>$_SESSION['groups_new'],'last_update'=>time()));
		}
		catch(MongoException $e) {
			die("Failed to insert data ".$e->getMessage());
		}
	}else{
		$groups_origin = $cursor['label'];
		$groups = array_merge($groups, $groups_origin);
//====temporary for coding update(need be removed later)====
		$groups[] = "Admin";
		$groups[] = "Agent";
//==========================================================
		$groups = array_unique($groups) ;
		$groups = array_filter($groups) ;
		$groups = array_values($groups) ;
		$_SESSION['groups_new'] = $groups;
		try{
		$collection_group->update(array('_id'=>$company_id),array('$set'=>(array('label'=>$_SESSION['groups_new'],'last_update'=>time()))),array('upsert'=>false));
		}
		catch(MongoException $e) {
			die("Failed to update data ".$e->getMessage());
		}
	}
	try{
		$collection_company->update(array('_id'=>$company_id),array('$set'=>(array('timezone'=>$_SESSION['timezone_new'],'timeout_inactivity'=>$_SESSION['timeout_inactivity_new'],'business_hours'=>$_SESSION['business_hours_new'],'start_time'=>$_SESSION['start_time_new'],'end_time'=>$_SESSION['end_time_new'],'inboundcall_recording'=>$_SESSION['inboundcall_recording_new'],'outboundcall_recording'=>$_SESSION['outboundcall_recording_new'],'number_agents_ring'=>$_SESSION['number_agents_ring_new'],'queue_size'=>$_SESSION['queue_size_new'],'last_update'=>time()))),array('upsert'=>false));
	}
	catch(MongoException $e) {
		die("Failed to update data ".$e->getMessage());
	}
	if(!$collection_company) {
		$error = 'Database Error';
	}else{
		$success="Changes are saved successfully!";
	}
	$_SESSION['clicked'] = 1;
}else{
	$result_company = $collection_company->findOne(array('_id'=>$company_id));
	$result_group = $collection_group->findOne(array('_id'=>$company_id));
	if($result_company == NULL){
		$error = "Database error, please try again!";
	}else{
		$timezone_get = $result_company['timezone'];
		$timeout_inactivity_get = $result_company['timeout_inactivity'];
		$business_hours_get = $result_company['business_hours'];
		$start_time_get = $result_company['start_time'];
		$end_time_get = $result_company['end_time'];
		$inboundcall_recording_get = $result_company['inboundcall_recording'];
		$outboundcall_recording_get = $result_company['outboundcall_recording'];
		$number_agents_ring_get = $result_company['number_agents_ring'];
		$queue_size_get = $result_company['queue_size'];
		$companyname_get = $result_company['name'];
	}
	if($result_group == NULL){
		$group_get = NULL;
	}else{
		$group_get= $result_group['label'];
	}
	unset( $_SESSION['clicked'], $groups );
}



if (isset($db_error)) $error = $db_error;
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
       <h1>Preferences</h1>
		<?php if(!isset($error)){?>
		<h2><?php if(isset($_SESSION['companyname_get'])){echo $_SESSION['companyname_get'];} ?></h2>
		<?php } ?>
      </div>
<!--===============error msg============================-->
<?php

if(isset($error)) 
	echo '<div class="errormsgbox"> '.$error.'</div>';
if(isset($success)) 
                echo '<div class="success">'. $success.'</div>';

?>

<!--=================time zone select===================-->


<form action="<?php echo basename(__FILE__);?>" method="post">
<input type="hidden" name="submit" value="login"> 
<div>


<p>
  <label for="select">Time Zone:</label>
</p>
<blockquote>
  <blockquote>
    <p class="time" id="select" name="select">
      <select name="select_timezone" class="time" id="select">
<?php
if(!isset($_SESSION['clicked'])){
	$timezone = $timezone_get;
}else{
	$timezone = $_SESSION['timezone_new'];
}

	foreach($timezone_array2 as $key=>$val){
		echo ($key == $timezone) ? "<option selected=\"selected\" value=\"$key\">$val</option>":"<option value=\"$key\">$val</option>";
} unset($timezone);?>

     </select>
    </p>
  </blockquote>
</blockquote>


<!--==============Business Hours===================-->
<p>
  Business Hours:<div>
<p>
  <label>
    		<blockquote>
    <blockquote>
              <p>
<?php 
if(!isset($_SESSION['clicked'])){
	$businesshours = $business_hours_get;  
}else{
	$businesshours = $_SESSION['business_hours_new'] ;
}

for ($i = 0; $i <7; $i++) {
		if(in_array(($i+1), $businesshours )){?>
			<input type="checkbox" name="BusinessHours[]" value=<?php echo $i+1;?> id=<?php echo $i+1;?> checked>
		<?php echo $day_array[$i];}else{ ?>
			<input type="checkbox" name="BusinessHours[]" value=<?php echo $i+1;?> id=<?php echo $i+1;?> >
		<?php echo $day_array[$i];}} unset($businesshours);?>
</p>
      <p>
            From:<select name="select_start" id="select3">                
<?php
if(!isset($_SESSION['clicked'])){
	$starttime = $start_time_get;  
}else{
	$starttime = $_SESSION['start_time_new'] ;
}

	foreach($time_array as $key=>$val){
		echo ($key == $starttime) ? "<option selected=\"selected\" value=\"$key\">$val</option>":"<option value=\"$key\">$val</option>"; } unset($starttime);?>
	      </select>
      To:
                    <select name="select_end" id="select4">
                  <?php
if(!isset($_SESSION['clicked'])){
	$endtime = $end_time_get;  
}else{
	$endtime = $_SESSION['end_time_new'] ;
}

	$default = $endtime;
	foreach($time_array as $key=>$val){
		echo ($key == $default) ? "<option selected=\"selected\" value=\"$key\">$val</option>":"<option value=\"$key\">$val</option>";} unset($endtime);?>
                </select>
      </p>
    </blockquote>
    </div>
<!--===============Timeout Inactivity==============-->
<p>
  <label for="select2">Timeout Inactivity:</label>
</p>
<blockquote>
  <blockquote>
    <p id="select2" name="select2">
      <select name="select_inactivity" id="select2">

<?php 
if(!isset($_SESSION['clicked'])){
	$timeout = $timeout_inactivity_get; 
}else{
	$timeout = $_SESSION['timeout_inactivity_new'] ;
}

if($timeout ==1){?>
       <option value="0">Disabled</option>
       <option value="1" selected="selected">Enabled</option>
<?php }else{ ?>
		<option value="0">Disabled</option>
     		<option value="1">Enabled</option>
 <?php } unset($timeout);?>
     </select>
    </p>
  </blockquote>
</blockquote>

<!--===============Groups==============-->
<p>
  <label for="textbox1">Groups:</label>
</p>
<blockquote>
  <blockquote>

<?php 
if(!isset($_SESSION['clicked'])){
	$group = $group_get;
	if($group==NULL){?>
		<p>Admin</p>
		<p>Agent</p>
<?php	}
}else{
	$group = $_SESSION['groups_new'];
}
foreach($group as $val){ ?>
	<p><?php echo $val; ?></p>
<?php } unset($group); ?>
</blockquote>
</blockquote>

<blockquote>
  <blockquote>

<script src="addTextInput.js" language="Javascript" type="text/javascript"></script>
<form action="<?php echo basename(__FILE__);?>" method="post">
     <div id="dynamicInput">
          New Group 1<br><input type="text" name="groupInput[]">
     </div>
     <input class="bigbutton2" type="button" value="Add another group" onClick="addInput('dynamicInput');">
</form>


</blockquote>
</blockquote>

<!--===========Inbound Call Recording=============-->
  <h3>Call Settings</h3>
  
<p>Inbound Call Recording Enabled:
<div><blockquote><label>
      <blockquote>
         <p>

<?php 
if(!isset($_SESSION['clicked'])){
	$inbound = $inboundcall_recording_get;  
}else{
	$inbound = $_SESSION['inboundcall_recording_new'] ;
}

if($inbound ==1){?>
           <input type="radio" name="Inbound_Recording" value="1" id="InboundCallRecording_0" checked>
           Yes
           <input type="radio" name="Inbound_Recording" value="0" id="InboundCallRecording_1">
		No
 <?php }elseif($inbound ==0){ ?>
           <input type="radio" name="Inbound_Recording" value="1" id="InboundCallRecording_0">
           Yes
           <input type="radio" name="Inbound_Recording" value="0" id="InboundCallRecording_1" checked>
		No
<?php }else{ ?>
		<input type="radio" name="Inbound_Recording" value="1" id="InboundCallRecording_0">
           Yes
           <input type="radio" name="Inbound_Recording" value="0" id="InboundCallRecording_1">
		No
<?php } unset($inbound);?>

</p>
    </blockquote>
     </label></blockquote></div>
   
   <p>Outbound Call Recording Enabled:
<div><blockquote><label>
      <blockquote>
         <p>
           
<?php 
if(!isset($_SESSION['clicked'])){
	$outbound = $outboundcall_recording_get;  
}else{
	$outbound = $_SESSION['outboundcall_recording_new'] ;
}
if($outbound == 1){?>
           <input type="radio" name="Outbound_Recording" value="1" id="OutboundCallRecording_2" checked>
           Yes
           <input type="radio" name="Outbound_Recording" value="0" id="OutboundCallRecording_3">
		No
 <?php }elseif($outbound == 0){ ?>
           <input type="radio" name="Outbound_Recording" value="1" id="OutboundCallRecording_2">
           Yes
           <input type="radio" name="Outbound_Recording" value="0" id="OutboundCallRecording_3" checked>
		No
<?php }else{ ?>
		<input type="radio" name="Outbound_Recording" value="1" id="OutboundCallRecording_2">
           Yes
           <input type="radio" name="Outbound_Recording" value="0" id="OutboundCallRecording_3">
		No
<?php } unset($outbound);?>

</p>
      </blockquote>
     </label></blockquote></div>  
<!--============Number of Agents to Ring==============-->     
<h3>Routing Settings</h3>
<p>Number of Agents to ring:</p>
<blockquote>
  <blockquote>
    <p>
      <select name="select_numbertoring" id="select5">

<?php
if(!isset($_SESSION['clicked'])){
	$numberring = $number_agents_ring_get;
}else{
	$numberring = $_SESSION['number_agents_ring_new']; ;
}

	foreach($ringagent_array as $key=>$val){
echo ($key == $numberring) ? "<option selected=\"selected\" value=\"$key\">$val</option>":"<option value=\"$key\">$val</option>"; } unset($numberring);?>

    </select>
    </p>
  </blockquote>
</blockquote>
<!--================Queue Size===============--> 
<h3>Queue Settings</h3>
<p>Maximum Queue Size:</p>
<blockquote>
  <blockquote>
    <p>
      <select name="select_queuesize" id="select6">
       
<?php
if(!isset($_SESSION['clicked'])){
	$queuesize = $queue_size_get;
}else{
	$queuesize = $_SESSION['queue_size_new'];
}

	foreach($ringagent_array as $key=>$val){
		echo ($key == $queuesize) ? "<option selected=\"selected\" value=\"$key\">$val</option>":"<option value=\"$key\">$val</option>";} unset($queuesize);?>

      </select>
    </p>
  </blockquote>
</blockquote>
<!--===========================================--> 
<p>
  <input class = "bigbutton" type="submit" name="submit" id="submit" value="Save">
</p>
</div>
</form>
<!--===========================================--> 



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
</fieldset>
</body>
</html>
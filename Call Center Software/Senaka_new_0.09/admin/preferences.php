<?php

define('INCLUDE_CHECK',true);

$PRE_DIR = "../";

// These files can be included only if INCLUDE_CHECK is defined
require_once("$PRE_DIR"."includes/config_login.php");
require_once("$PRE_DIR"."includes/config_mandrill.php");
require_once("$PRE_DIR"."includes/config_mongo.php"); 		// connect to database or set $error
require_once("$PRE_DIR"."includes/functions_validateforms.php");
require_once("$PRE_DIR"."includes/functions_login.php");
require_once("$PRE_DIR"."includes/functions_mandrill.php"); 	// Mandrill SwiftMailer
require_once("$PRE_DIR"."includes/functions_preferences_select.php"); 

// Check if user is logged in.
sec_session_start();

if (login_check($collection_user) != true) {
	header("Location: ".$PRE_DIR."login.php");
	exit;
} 

// Check if user is an admin or a supervisor; Only an admin or supervisor has access to this page
if ( $_SESSION['user_type'] != "admin") {
	$_SESSION['message'] = "ERROR: You are not authorized to perform this action!";
	header("Location: ".$PRE_DIR."login.php");
	exit;
}


// Copy database error onto $db_error. $error will be cleared soon after
if(isset($error)){
	$db_error = $error;
}

unset($error, $result_company, $success, $cursor, $clicked, $result_group);

// Check if company_id is stored in the SESSION parameters. Without it, several of the below 
// modules will not work properly. 
if (isset($_SESSION['company'])) {

	// get the company ID (MongoID) 
	$company_id = new MongoId($_SESSION['company']);
} else {
	$error = "Session Error. Please log out and log in again!";
}
// no need to check isset($company_id), as long as $error is checked

// no error and user submits form
if (!isset($error) && $_POST['submit']=="Save" ){
	// Get all the parameters 
	$time_zone 		= $_POST['select_timezone'];
	$business_days 		= $_POST['BusinessDays'];
	$business_start		= $_POST['select_start'];
	$business_end 		= $_POST['select_end'];
	$time_out 		= $_POST['select_inactivity'];
	$groups 		= $_POST['selectgroups'];
	$inbound_record 	= $_POST['Inbound_Record'];
	$outbound_record 	= $_POST['Outbound_Record'];
	$num_agents_ring 	= $_POST['numbertoring'];
	$max_queue 		= $_POST['select_queuesize'];

	//** validate time_zone
	if(!array_key_exists($time_zone, $timezone_array2)){
		$error = "Database Violation: Illegal Time Zone value!";
	}else if((!is_array($business_days))&&($business_days!=NULL)){
		$error = "Database Violation: Illegal Business Days value!";
	}else if(!array_key_exists($business_start, $time_array)||!array_key_exists($business_end, $time_array)){
		$error = "Database Violation: Illegal Business Time value!";
	}else if($time_out!=1&&$time_out!=0){
		$error = "Database Violation: Illegal Time Out value!";
	}else if(!is_array($groups) && $groups!=NULL){
		$error = "Database Violation: Illegal Group Label value!";
	}else if(($inbound_record!=1&&$inbound_record!=0)||($outbound_record!=1&&$outbound_record!=0)){
		$error = "Database Violation: Illegal Inbound/Outbound value!";
	}else if(!array_key_exists($num_agents_ring, $ringagent_array)){
		$error = "Database Violation: Illegal Routing Setting value!";
	}else if(!array_key_exists($max_queue, $queuesize_array)){
		$error = "Database Violation: Illegal Queue Size value!";
	}else{
	
		// check if any groups are provisioned for this company in the group collection
		$cursor = $collection_group->findOne(array('_id' => $company_id));

		// if no groups provisioned for this company in the group collection, create them
		if (!isset($cursor) || $cursor['label'] == NULL){
			$groups[] = "Admin";
			$groups[] = "Agent";
		} 
		// groups are provisioned for this company in the group collection
		else {
			// $groups_origin = $cursor['label'];
			// $groups = array_merge($groups, $groups_origin);
			//**PHP bug: if one parameter in array_merge is null or empty, the returned value is null, so the code here is a little ugly
			if($groups != NULL){
				if(($key = array_search("", $groups)) !== false) {
					unset($groups[$key]);
				}
				$groups = array_merge($groups, $cursor['label']);
				$groups = array_unique($groups);
				$groups = array_filter($groups);
				$groups = array_values($groups);
			}else{
				$groups = $cursor['label'];
			}
		}
		
		// update or insert into the group collection
		try{
			$collection_group->update(
				array('_id' => $company_id),
				array('$set' => (array(
					'label' => $groups,
					'last_update' => time())
				)),
				array('upsert' => true ));
		}
		catch(MongoException $e) {
			die("Failed to update data ".$e->getMessage());
		}

		// update into the company collection
		try {
			$collection_company->update(
				array('_id'=>$company_id),
				array('$set'=>(array(
					'timezone' => $time_zone,
					'timeout_inactivity' => $time_out,
					'business_days' => $business_days,
					'business_start' => $business_start,
					'business_end' => $business_end,
					'inboundcall_recording' => $inbound_record,
					'outboundcall_recording' => $outbound_record,
					'number_agents_ring' => $num_agents_ring,
					'queue_size' => $max_queue,
					'last_update' => time())
				)),
				array('upsert'=>false));
		}
		catch(MongoException $e) {
			die("Failed to update data ".$e->getMessage());
		}

		// ** check if start time is later than end time
		if((0+$_POST['select_start'])>(0+$_POST['select_end'])){
			$error = 'Start time can not be later than end time in \'Business Hours\' ';
		} else {
			if(!$collection_company) {
				$error = 'Database Error';
			} else {
				$success="Changes are saved successfully!";
			}
		}
		$clicked = 1;
	}
}

// form is not submitted, page is refreshed, or if there is an error 
else if (!isset($error) ){

	try {
		$result_company = $collection_company->findOne(array('_id'=>$company_id));
	}
	catch(MongoException $e) {
		die("Failed to update data ".$e->getMessage());
	}

	if($result_company == NULL){
		$error = "Database error, please try again!";
	} else {
		$timezone_get = $result_company['timezone'];
		$timeout_inactivity_get = $result_company['timeout_inactivity'];
		$business_days_get = $result_company['business_days'];
		$business_start_get = $result_company['business_start'];
		$business_end_get = $result_company['business_end'];
		$inbound_record_get = $result_company['inboundcall_recording'];
		$outbound_record_get = $result_company['outboundcall_recording'];
		$num_agents_ring_get = $result_company['number_agents_ring'];
		$maxqueue_get = $result_company['queue_size'];
		$companyname_get = $result_company['name'];
	}

	try {
		$result_group = $collection_group->findOne(array('_id'=>$company_id));
	}
	catch(MongoException $e) {
		die("Failed to update data ".$e->getMessage());
	}

	if($result_group == NULL){
		$group_get = NULL;
	} else {
		$group_get= $result_group['label'];

		// if delete button is clicked, update label in group collection
		if(is_string($_POST['delete'])||!isset($_POST['delete'])){
			if(($key = array_search($_POST['delete'], $group_get)) !== false) {
				unset($group_get[$key]);
				try{
					$collection_group->update(
						array('_id' => $company_id),
						array('$set' => (array(
							'label' => $group_get,
							'last_update' => time())
						)),
						array('upsert' => false ));
				}
				catch(MongoException $e) {
					die("Failed to update data ".$e->getMessage());
				}
			}
		}else{
			$error = "Database Violation: Illegal Database Access!";
		}
	}
// ** check if start time is later than end time
	if((0+$result_company['business_start'])>(0+$result_company['business_end'])){
		$error = 'Start time can not be later than end time in \"Business Hours\" section';
	}
	unset( $clicked, $groups );
}
// $error is set
else {
	// do nothing
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
       <h1>Preferences</h1>
		<?php if (isset($companyname_get)) echo "<h2>".$companyname_get."</h2>"; ?>
      </div>
<!--===============error msg============================-->
<?php

if(isset($error)) 
	echo '<div class="errormsgbox"> '.$error.'</div>';
if(isset($success)) 
                echo '<div class="success">'. $success.'</div>';

?>


<!--===============Groups==============-->

<p> <label for="textbox1">Groups:</label> </p>
<blockquote>
  <blockquote>

<?php 
$group = ( isset($clicked )) ?  $groups : $group_get;

// Show "admin" and "agent" as static default labels. They are not editable.
	echo "<li><strong style = 'display:inline-block;width:200px;'>Admin (by default)</strong></li><br/>";
	echo "<li><strong style = 'display:inline-block;width:200px;'>Agent (by default)</strong></li><br/>";

foreach($group as $val){
	if( $val != "Admin" && $val != "Agent" ){
		echo "<form action=".basename(__FILE__)." method='post'>
			<div>
			<li><strong style = 'display:inline-block;width:200px;'>$val</strong>
			<input type='hidden' name='delete' value='$val'> 
			<input type = 'submit' value = 'Delete'></li></div>
		     </form><br/>";
	}
} 

unset($group); 
?>

</blockquote>
</blockquote>

<blockquote>
  <blockquote>

<!--=================beginning of main form ===================-->
<form action="<?php echo basename(__FILE__);?>" method="post">
<input type="hidden" name="submit" value="login"> 


     <div id="dynamicInput">
          New Group<br><input type="text" name="selectgroups[]">
     </div>

     <script src="addTextInput.js" language="Javascript" type="text/javascript"></script>
     <a id="myLink" title="Add another Group" href="#" onclick="addInput('dynamicInput');return false;">Add another Group</a>

</blockquote>
</blockquote>



<!--=================time zone select===================-->

<div>

<p> <label for="select">Time Zone:</label> </p>

<blockquote>
  <blockquote>
    <p class="time" id="select" name="select">
      <select name="select_timezone" class="time" id="timezone">

<?php
$timezone = ( isset($clicked)) ?  $time_zone : $timezone_get;  

foreach($timezone_array2 as $key=>$val){
	echo ($key == $timezone) ? 
		"<option selected=\"selected\" value=\"$key\">$val</option>" : 
		"<option value=\"$key\">$val</option>";
} 
?>

     </select>
    </p>
  </blockquote>
</blockquote>


<!--==============Business Hours===================-->
<p>Business Hours:<div>
<p>
  <label>
   <blockquote>
    <blockquote>
              <p>
<?php 
$businessdays = ( isset($clicked)) ?  $business_days : $business_days_get;  

for ($i = 1; $i <8; $i++) {
	echo (in_array(($i), $businessdays )) ?
		"<input type=\"checkbox\" name=\"BusinessDays[]\" value=$i id=$i checked>" :
		"<input type=\"checkbox\" name=\"BusinessDays[]\" value=$i id=$i >";
	echo $day_array[$i-1]."\n";
} 
?>
		</p>

      <p>From: <select name="select_start" id="biz_start">                

<?php

$starttime = ( isset($clicked )) ?  $business_start : $business_start_get;  

foreach($time_array as $key=>$val){
	echo ($key == $starttime) ? 
		"<option selected=\"selected\" value=\"$key\">$val</option>" :
		"<option value=\"$key\">$val</option>"; 
} 

?>
      </select>

      To: <select name="select_end" id="biz_end">
<?php

$endtime = ( isset($clicked )) ?  $business_end : $business_end_get;  

foreach($time_array as $key=>$val){
	echo ($key == $endtime) ? 
		"<option selected=\"selected\" value=\"$key\">$val</option>" :
		"<option value=\"$key\">$val</option>";
} 

unset($endtime);
?>
	</select>
      </p>
    </blockquote>
    </div>

<!--===============Timeout Inactivity==============-->
<p>
  <label for="timeout">Timeout Inactivity:</label>
</p>
<blockquote>
  <blockquote>
    <p id="timeout" name="timeout">
      <select name="select_inactivity" id="timeout">

<?php 
$timeout = ( isset($clicked )) ?  $time_out : $timeout_inactivity_get;

if($timeout == 1) $selected = "selected";
else $selected = null;

echo "<option value='0' >Disabled</option>\n";
echo "<option value='1' $selected >Enabled</option>\n";
?>

     </select>
    </p>
  </blockquote>
</blockquote>

<!--===========Inbound Call Recording=============-->
  <h3>Call Settings</h3>
  
<p>Inbound Call Recording Enabled:
<div><blockquote><label>
      <blockquote>
         <p>

<?php 

$inbound = ( isset($clicked )) ?  $inbound_record : $inbound_record_get;

if ($inbound == 1){
	echo "<input type='radio' name='Inbound_Record' value='1' id='InboundRecord_1'>Yes\n";
	echo "<input type='radio' name='Inbound_Record' value='0' id='InboundRecord_0'> No\n";
} else {
	echo "<input type='radio' name='Inbound_Record' value='1' id='InboundRecord_1'> Yes\n";
	echo "<input type='radio' name='Inbound_Record' value='0' id='InboundRecord_0' checked> No\n";
} 

unset($inbound);
?>

</p>
    </blockquote>
     </label></blockquote></div>
   
<!--===========Outbound Call Recording=============-->
   <p>Outbound Call Recording Enabled:
<div><blockquote><label>
      <blockquote>
         <p>
           
<?php 
$outbound = ( isset($clicked )) ?  $outbound_record : $outbound_record_get;

if($outbound == 1){
	echo "<input type='radio' name='Outbound_Record' value='1' id='OutboundRecord_2' checked> Yes\n";
	echo "<input type='radio' name='Outbound_Record' value='0' id='OutboundRecord_3'> No\n";
} else {
	echo "<input type='radio' name='Outbound_Record' value='1' id='OutboundRecord_2'> Yes\n";
	echo "<input type='radio' name='Outbound_Record' value='0' id='OutboundRecord_3' checked> No\n";
} 
?>

</p>
      </blockquote>
     </label></blockquote></div>  
<!--============Number of Agents to Ring==============-->     

<h3>Routing Settings</h3>
<p>Number of Agents to ring:</p>
<blockquote>
  <blockquote>
    <p>
      <select name="numbertoring" id="numbertoring">

<?php
$numberring = ( isset($clicked )) ?  $num_agents_ring : $num_agents_ring_get;

foreach($ringagent_array as $key=>$val){
	echo ($key == $numberring) ? 
		"<option selected=\"selected\" value=\"$key\">$val</option>" :
		"<option value=\"$key\">$val</option>"; 
} 
?>

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
      <select name="select_queuesize" id="queuesize">
       
<?php

$queuesize = ( isset($clicked )) ?  $max_queue : $maxqueue_get;

foreach($queuesize_array as $key=>$val){
	echo ($key == $queuesize) ? 
		"<option selected=\"selected\" value=\"$key\">$val</option>" :
		"<option value=\"$key\">$val</option>";
} 
?>

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

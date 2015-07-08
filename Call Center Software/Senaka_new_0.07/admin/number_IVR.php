<?php

//var_dump($_POST);

//var_dump(json_decode($json));

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
elseif(isset($_SESSION['edit_IVR'])&& $_POST['save_IVR_msg']){
	
	// try{
		// $collection_phonenum->update(array('phone_number'=>$_SESSION['phone_selected']),array('$set'=>(array('ivr_message'=>array('name'=>$_POST['ivr_message_name'],'content'=>$_POST['ivr_message_content'])))),array('upsert'=>false));
	// }catch(MongoException $e){
		// die("Failed to update data ".$e->getMessage());
	// }
	
	//IVR data process==========================================
	$numberChoiceArray = array();
	$routeChoiceArray = array();
	$subIVRNameArray = array();
	$subIVRContentArray = array();
	foreach($_POST as $key => $value){		
		if(substr($key,0,9) === "tableName"){	
			if(strrpos($key,"number_choice")){
				$tableIndex_num = substr($key,9,strpos($key,"_number_choice_")-9);
				$rowIndex_num = substr($key,strpos($key,"_number_choice_")+15);
				$name = $tableIndex_num."_".$rowIndex_num;
				//echo "**".$name." :".$value;
				$numberChoiceArray[$name] = $value;
			}elseif(strrpos($key,"route")){
				$tableIndex_num = substr($key,9,strpos($key,"route")-9);
				$rowIndex_num = substr($key,strpos($key,"route")+5);
				$name = $tableIndex_num."_".$rowIndex_num;
				//echo "**".$name." :".$value;
				$routeChoiceArray[$name] = $value;
			}
		}elseif(substr($key,0,6) === "ivrMsg"){
		
			if(strpos($key,"name+")){
				$tableIndex_num = substr($key,16,strpos($key,"name+")-16);
				$tableCoordinate = substr($key,strpos($key,"name+")+4);
				$name = $tableIndex_num.$tableCoordinate;
				//echo "**".$name." :".$value;
				$subIVRNameArray[$name] = $value;
			}elseif(strpos($key,"content+")){
				$tableIndex_num = (int)substr($key,16,strpos($key,"content+")-16);
				$tableCoordinate = substr($key,strpos($key,"content+")+7);
				$name = $tableIndex_num.$tableCoordinate;
				//echo "**".$name." :".$value;
				$subIVRContentArray[$name] = $value;
			}
		}
	}
	//print_r($numberChoiceArray);
	//print_r($routeChoiceArray);
	// print_r($subIVRNameArray);
	// print_r($subIVRContentArray);
	//=============================================================
	
	try{
		$collection_phonenum->update(
			array('phone_number'=>$_SESSION['phone_selected']),
			array('$set'=>(array('ivr_message'=>array('name'=>$_POST[	'ivr_message_name'],'content'=>$_POST['ivr_message_content']),'ivr_numberSelect_array'=>$numberChoiceArray, 'ivr_routeSelect_array'=>$routeChoiceArray, 'ivr_subMsgName_array'=>$subIVRNameArray,'ivr_subMsgContent_array'=>$subIVRContentArray))),
			array('upsert'=>true)
		);
	}catch(MongoException $e){
		die("Failed to update data ".$e->getMessage());
	}
	
}
elseif($_POST['phone']){
	$_SESSION['phone_selected'] = $_POST['submit'];
	$cursor_ph = $collection_phonenum->findOne(array('company'=>$company_id));
	$cursor_group = $collection_group->findOne(array('_id'=>$company_id));
	$_SESSION['edit_IVR']=1;
	unset($cursor,$cursor2);
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
	function toggle_visibility(id) {
		var e = document.getElementById(id);
		e.style.display = (e.style.display == 'block') ? 'none':'block';
	}
</script>

<body id="observable">
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

<!--==============IVR Settings===============-->
<form  action="<?php echo basename(__FILE__);?>" method="post" >
<div id = "ivr_div1">
<?php if(isset($_SESSION['edit_IVR'])): ?>
<h2><?php echo $_SESSION['phone_selected'] ; ?></h2>
<h3>IVR </h3><br/>

<?php 
$result = $collection_phonenum->findOne(array('phone_number'=>$_SESSION['phone_selected']));
?>



<label style = 'display:inline-block;width:250px;'>IVR Message </label><a href="#" onClick ="toggle_visibility('expandcollapse_1');return false;"> <label>Expand/Collapse</label> </a>
<br/><br/>&nbsp;&nbsp;&nbsp; 
<?php $ivr_message_array = $result['ivr_message']; ?>
<label class="label-style"><?php echo $ivr_message_array['name']; ?></label> 
<div id="expandcollapse_1" style="width: 500px; display:none">

<input type="hidden" name="save_IVR_msg" value="add">
<p></p>
<label>Name*</label> <input type="text" name="ivr_message_name" value=<?php echo $ivr_message_array['name']; ?> > <p></p>
<label>TTS Message*</label> <textarea style="width: 200px; height: 90px;" name="ivr_message_content"><?php echo $ivr_message_array['content']; ?></textarea><br/><br/> 

</div><br/><br/>



<script type="text/javascript">
var numberChoiceArray = <?php echo json_encode($result['ivr_numberSelect_array']); ?>;
var routeChoiceArray = <?php echo json_encode($result['ivr_routeSelect_array']); ?>;
var subIVRNameArray = <?php echo json_encode($result['ivr_subMsgName_array']); ?>;
var subIVRContentArray = <?php echo json_encode($result['ivr_subMsgContent_array']); ?>;
</script>

<script id="js" src="addIVR.js" language="Javascript" type="text/javascript"></script>



<?php endif; ?>

<?php if(isset( $_SESSION['edit_IVR'] )): ?>
<br/><br/>


<input type="hidden" name="save" value="save">
<input class="bigbutton2" type="submit" value="Save" >



&nbsp;<br/><br/>


<?php endif; ?>

</div>
</form>


<!--return button-->

<?php if(isset($_SESSION['edit_IVR'])): ?>
<form  action="<?php echo basename(__FILE__);?>" method="post">
<input type="hidden" name="return" value="return">
<input class="bigbutton2" type=submit value="Return" >
</form>
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

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

// Check if user is logged in.

sec_session_start();
if (login_check($collection_user) != true) {

    		exit(header("Location: ".$PRE_DIR."login.php"));
} 

// Check if user is admin or supervisor; Only an admin or supervisor has access to this page
if (( $_SESSION['user_type'] != "admin") && ($_SESSION['user_type'] != "supervisor"  )) {
	$_SESSION['message'] = "ERROR: You are not authorized to perform this action!";
	header("Location: ".$PRE_DIR."login.php");
	exit;
}

// Copy database issues onto $db_error. $error will be cleared soon after
if(isset($error)){
	$db_error = $error;
}

unset($error, $success);

$ACTIVATION_DIR = 'http://'.$_SERVER['SERVER_NAME'].'/~mingcong/activate.php';

// Check if company_id is stored in the SESSION parameters. Without it, several of the below 
// modules will not work properly. 
if (isset($_SESSION['company'])) {
	// get the company ID (MongoID) 
	$company_id = new MongoId($_SESSION['company']);
} else {
	$error = "Session Error. Please log out and log in again!";
}





if( (isset($_POST['submit']) || isset($_POST['contact']) || isset($_POST['add']) )&&!isset($error)&&isset($company_id)){
if(isset($_SESSION['edit_user'])&&$_POST['submit']=="cancel"){
	unset($_SESSION['edit_user']);
}
//after "Save" button is clicked for editing current user
elseif(isset($_SESSION['edit_user'])&&$_POST['submit']=="save"){
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$email = $_POST['email'];
	$group = $_POST['select_group'];
	if(!isset($error)&&(empty($firstname)||empty($lastname))){
		$error = 'Please Enter a valid First Name or/and Last Name!';
	}
	if (!isset($error)&&(empty($email) || validate_email($email))) {
		$error = 'Please Enter a valid Email Address!';
	}
	if(!isset($error)&&!isset($group)){
		$error = 'Please select group(s) for the New User!';
	}

	if(!isset($error)){
		$cursor = $collection_user->findOne(array('email'=>$email,'company'=>$company_id));
		if(isset($cursor)){
			$collection_user->update(array('email'=>$email),array('$set'=>(array('firstname'=>$firstname,'lastname'=>$lastname,'group_id'=>$group,'last_update'=>time()))),array('upsert'=>false));
			$success = "Changes are saved successfully!";
		}else{
			$code = md5(time().uniqid(rand(),true));
			try{
				$collection_user->insert(array('password'=>$code,'email'=>$email,'firstname'=>$firstname,'lastname'=>$lastname,'code'=>$code,'status'=>"signup", 'company'=>$company_id,'group_id'=>$group,'online_status'=>"0",'last_update'=>time() ));
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
				$sitepath = $ACTIVATION_DIR.'?email='.urlencode($email).'&key='.urlencode($code);
				$email_message=format_email_mandrill($TEMPLATE_EMAIL_SIGNUP,$format,$firstname,$sitepath);
				$body_type='text/'.$format;
				$status=send_email_mandrill($MANDRILL_USERNAME,$MANDRILL_PASSWORD,$from,$email,$subject,$email_message,$body_type);
				$success = "A confirmation email has been sent to ".$email;
			}	
		}
	}
	if(!isset($error)){
		unset($_SESSION['edit_user']);//back to first page
		unset($cursor);
	}
}


//when a current user is selected, retrieve the user's info to edit
elseif(!isset($_SESSION['edit_user'])&&$_POST['contact']){
		$name_selected = $_POST['submit'];
		$pos = strrpos($name_selected, " ");
		$_SESSION['firstname_selected'] = substr($name_selected, 0, $pos);
		$_SESSION['lastname_selected'] = substr($name_selected, $pos+1);
		$cursor = $collection_user->findOne(array('firstname'=>$_SESSION['firstname_selected'] ,'lastname'=>$_SESSION['lastname_selected'],'company'=>$company_id));
		$cursor2 = $collection_group->findOne(array('_id'=>$company_id));
		$_SESSION['email_selected'] = $cursor['email'];
		$_SESSION['online_status_selected'] = $cursor['online_status'];
		//$_SESSION['group_selected'] = $cursor['group'];
		$_SESSION['group_selected'] = $cursor['group_id'];
		$_SESSION['groups_selected'] = $cursor2['label'];
		$_SESSION['edit_user']=1;
		unset($cursor,$cursor2);
}

//when add_new_agent is clicked
elseif(!isset($_SESSION['edit_user'])&&$_POST['add']){
		$cursor2 = $collection_group->findOne(array('_id'=>$company_id));
		$_SESSION['groups_selected'] = $cursor2['label'];
		$_SESSION['edit_user']=2;
}
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
       <h1>Agents</h1>

      </div>


<!--=================show first page===============-->


<div>
<?php if(!isset($error)&&isset($company_id)&&!isset($_SESSION['edit_user'])){ ?>
<form name="input" action="<?php echo basename(__FILE__);?>" method="post">
	<p><input class="bigbutton2" type=submit value= "Add New Agent" name= "add" ></p>
</form>
</div>
<div>
  <form  action="<?php echo basename(__FILE__);?>" method="post">

<?php
	$cursor = $collection_user->find(array('company'=>$company_id));
	if (!isset($cursor)){
		$error = "Database Error (14368). Please try again!";
	}else{ ?> <input type="hidden" name="contact" value="add">

		<?php while($cursor->hasNext()){

			$result = $cursor->getNext();
?>	
			<p><input class="bigbutton2" type=submit value= "<?php echo $result['firstname']." ".$result['lastname']; ?>" name="submit" ><?php echo "(".$result['email'].")";?>  
&nbsp;&nbsp;&nbsp;&nbsp;

Group: <?php if(sizeof( $result['group_id'] )<1){echo "None";
			}else{foreach($result['group_id'] as $var){
					$group_str .= $var." ";
				}
			echo $group_str;
			unset($group_str,$result);} ?> 

			</p>
<?php		}
	}
} ?>

</form>
</div>
<!--====================show error=====================-->
<?php

if(isset($error)) 
	echo '<div class="errormsgbox"> '.$error.'</div>';
if(isset($success)) 
                echo '<div class="success">'. $success.'</div>';

?>


<!--==============User settings=================-->


<?php if(isset($_SESSION['edit_user'])): ?>
<form action="<?php echo basename(__FILE__);?>" method="post">
<input type="hidden" name="submit" value="save"> 
<div>
<p>
<?php if($_SESSION['edit_user']==1){ ?>
	<h2><?php echo $_SESSION['firstname_selected']." ".$_SESSION['lastname_selected']; ?></h2>
	<?php }else{?>
	<h2>New Agent</h2>
	<?php }?>
</p>


<p>
  	<label for="textfield">First Name*:</label>
<?php if( $_SESSION['edit_user']==1&& isset($_SESSION['firstname_selected'])){?>
  	<input type="text" name="firstname" id="textfield" value=<?php echo $_SESSION['firstname_selected']; ?> />
<?php }else{ ?>
	<input type="text" name="firstname" id="textfield" />
<?php }?>
<label for="textfield">Last Name*:</label>
<?php if($_SESSION['edit_user']==1&& isset($_SESSION['lastname_selected'])){?>
  	<input type="text" name="lastname" id="textfield1" value=<?php echo $_SESSION['lastname_selected']; ?> />
<?php }else{ ?>
	<input type="text" name="lastname" id="textfield1" />
<?php }?>
</p>


<p>
 	<label for="email">Email*:</label>
<?php if($_SESSION['edit_user']==1&& isset($_SESSION['email_selected'])){?>
  	<input type="email" name="email" id="email" value=<?php echo $_SESSION['email_selected']; ?> size="30" />
<?php }else{ ?>
	<input type="email" name="email" id="email" size="30"/>
<?php }?>
</p>


<p>
  <label>Status:</label>
 <?php if($_SESSION['edit_user']==1&& $_SESSION['online_status_selected'] == 1){?>
<label class="bigbutton">Online</label>
<?php }else{ ?>
<label class="bigbutton">Offline</label>
<?php } ?>




 <!--<select name="select" id="select">
    


<?php if($status_selected == "active"&&$online_status_selected == 1){?>
<option value="1" selected="selected">Online</option>
 <option value="0">Offline</option>
<?php }elseif($status_selected == "active"&&$online_status_selected == 1){ ?>
<option value="1" >Online</option>
 <option value="0" selected="selected">Offline</option>
<?php } ?>


</select>-->


</p>


<p>
  <label>Groups*:  
	<blockquote>
    <blockquote>
<?php 
		$group = $_SESSION['group_selected'] ;
		if($_SESSION['edit_user']==2){ 
			unset($group);
		
		}
		$groups = $_SESSION['groups_selected'] ;


		for($i=0;$i<sizeof($groups);$i++){

			if(in_array($groups[$i],$group)){ ?>
				<div>	<input type="checkbox" name="select_group[]" value=<?php echo $groups[$i];?> id=<?php echo $i;?> checked><?php echo $groups[$i]; ?></div>
		<?php }else{ ?>
				<div>	<input type="checkbox" name="select_group[]" value=<?php echo $groups[$i];?> id=<?php echo $i;?> ><?php echo $groups[$i]; ?></div>
		<?php }}unset($group,$groups); ?>
			

		</blockquote>
    </blockquote>

</p>
		



<!--
<p>
  <label>Groups*:
<?php if($_SESSION['edit_user']==1&& in_array("supervisor",$_SESSION['group_selected'])){ ?>
    <input type="checkbox" name="check[]" value="supervisor" id="Groups_0" checked/>  <?php }else{ ?>

    <input type="checkbox" name="check[]" value="supervisor" id="Groups_0"/> 

<?php } ?>
    supervisors</label>
  <label>
<?php if($_SESSION['edit_user']==1&& in_array("agent",$_SESSION['group_selected'])){ ?>
    <input type="checkbox" name="check[]" value="agent" id="Groups_1" checked/>   <?php }else{ ?>
<input type="checkbox" name="check[]" value="agent" id="Groups_1" /> 
<?php } ?>
    agents</label>
</p>
-->



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
include ("includes/footer.html");
?>
  </div>
</div>
</fieldset>
</body>
</html>

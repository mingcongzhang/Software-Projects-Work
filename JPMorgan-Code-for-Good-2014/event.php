<?php
define('INCLUDE_CHECK',true);

unset($error,$success);

require_once("config_mongo.php"); 
require_once("config_mandrill.php");
require_once("functions_validateforms.php");
require_once("functions_mandrill.php");
require_once("config_login.php");
require_once("functions_login.php");
require 'facebook.php';
$facebook = new Facebook(array(
		'appId'=> '1461329180809897',
		'secret'=> '2e1388e2bb1f5cda4f7ba22fadc9f399'
	));
	
	sec_session_start();
$sema = 0;
if (login_check($collection_fundraiser) != true) {
	//header("Location: ".$PRE_DIR."login.php");
	//exit;
	$sema =1;
} 
	
	
	if (!isset($error) && isset($_POST['event_formsubmitted'])) {
		$eventname = isset($_POST['event_name']) ? $_POST['event_name']: null;
		$eventdescription = isset($_POST['event_description']) ? $_POST['event_description']: null;
		$donationgoal = isset($_POST['donation_goal']) ? $_POST['donation_goal']: null;
		$per = isset($_POST['measured_unit']) ? $_POST['measured_unit']: null;
		$_SESSION['name'] = $eventname;
		$_SESSION['description'] = $eventdescription;
		$_SESSION['goal'] = $donationgoal;
		$_SESSION['per'] = $per;
		
		
		
		
		if($eventname == null){
			$error = "Please enter event name";
		}elseif($eventdescription == null){
			$error = "Please enter event description";
		}elseif($donationgoal == null){
			$error = "Please enter donation goal";
		}elseif($per == null){
			$error = "Please enter measured unit";
		}
		$result = $collection_fundraiser->findOne(array('name'=>$eventname));
		$event_id = new MongoId();
		$code = md5(uniqid(rand(), true));
		try{
				$collection_fundraiser->insert(array(
				'name'=>$eventname,
				'description'=>$eventdescription,
				'code'=>$code,
				'goal'=>$donationgoal,
				'per'=>$per,
				'last_update'=>time()
));
		}catch(MongoException $e){
				$error = "Failed to insert data in Database. Please try again!";
		} 
		if (!$collection_donor){
			$error = 'Database Error';
		} 
		if(!isset($error)){
			$success = "Event creation successful!";
			$_SESSION['success-msg'] = $success;
		}
	}elseif(!isset($error) && isset($_POST['signup_formsubmitted'])){
		$username = isset($_POST['username']) ? $_POST['username']: null;
		$password1 = isset($_POST['password1']) ? $_POST['password1']: null;
		$password2 = isset($_POST['password2']) ? $_POST['password2']: null;
		
		if (empty($username) || validate_email($username)) {
			$error = 'Please Enter a valid Email address!';
		}else if (empty($password1)){
			$error = 'Please Enter the Password!';
		}else if (empty($password2)){
			$error = 'Please Verify Your Password!';
		}else if($password1 !== $password2){
			$error = 'Your Passwords Do Not Match!';
		}else{
			$result = $collection_fundraiser->findOne(array('email'=>$username));
			if ($result['status'] == "signup"){
				$error = "You have already signed up. Please click the link in 
					your E-mail to activate your account!";
			} elseif ($result['status'] == "active"){
				$error = "There is an existing account associated with this email!";
			}else{
				$raiser_id = new MongoId();
				$code = md5(uniqid(rand(), true));
				$password = md5($password1);
				try{
					$collection_fundraiser->insert(array('password'=>$password,'email'=>$username,
					'token'=>$code,
					'type'=>"individual",
					'status'=>"active", 
					'online_status'=>"0",
					'sid'=>$raiser_id,
					'last_update'=>time()
	));
				}	
				catch(MongoException $e){
					$error = "Failed to insert data in Database. Please try again!";
				} 
				$_SESSION['sid'] = $raiser_id;
				$_SESSION['token'] = $code;
			}
			if (!$collection_fundraiser){
				$error = 'Database Error';
			} 
			if(!isset($error)){
				$success = "Sign up successful!";
				$_SESSION['success-msg'] = $success;
				$sema = 0;
				//login($username, $password1, $collection_fundraiser);
			}
		}
	
	}
	
	
	
	
if (isset($error)){
	$_SESSION['error-msg'] = $error;		
}	
	
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Maguwohost Website Template | Home :: w3layouts</title>
		<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="js/jquery.min.js"></script>
		 <!-- Custom Theme files -->
		<link href="css/style.css" rel='stylesheet' type='text/css' />
   		 <!-- Custom Theme files -->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
		</script>
		<!----webfonts--->
		<link href='http://fonts.googleapis.com/css?family=Raleway:400,100,200,300,500,600,700,800,900' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic|Roboto+Slab:400,100,700' rel='stylesheet' type='text/css'>
		<!---//webfonts--->
		<!----start-top-nav-script---->
		<script>
			$(function() {
				var pull 		= $('#pull');
					menu 		= $('nav ul');
					menuHeight	= menu.height();
				$(pull).on('click', function(e) {
					e.preventDefault();
					menu.slideToggle();
				});
				$(window).resize(function(){
	        		var w = $(window).width();
	        		if(w > 320 && menu.is(':hidden')) {
	        			menu.removeAttr('style');
	        		}
	    		});
			});
		</script>
		<!----//End-top-nav-script---->
		<style>

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
		<?php
			if(isset($error)) echo '<div class="errormsgbox"> '.$error.'</div>'; 
			if(isset($success)) echo '<div class="success">'. $success.'</div>';
		?>
		<!----- start-header---->
			<div id="home" class="header">
					<div class="top-header" id="move-top">
						<div class="container">
						<div class="logo">
							<a href="index.html"><img src="images/MJFOX Foundation Icon.gif" title="MJ Fox" width="500"></a>
						</div> 
						<!----start-top-nav---->
						 
						
						<!----- contact-info ----->
						<div class="clearfix"> </div>
					</div>
				</div>
			</div>
			<!----- //End-header---->
			<!----- banner ------>
			<div class="banner">
				<div class="container">
					<div class="banner-info text-center">
						<h1>Michael J. Fox Foundation</h1>
						<ul class="footer-social-icons">
							<div>
								<a href="http://instagram.com/michaeljfoxorg"><img src="images/IG.jpg"  width = "125" height ="125"></a>
								<a href="https://twitter.com/MichaelJFoxOrg"><img src="images/twitter.jpg"  width = "125" height ="125"></a>
								<a href="https://www.facebook.com/michaeljfoxfoundation"><img src="images/facebook_logo.jpg"  width = "125" height ="125"></a>
							</div>
						</ul>
					</div>
					<div class="row">
						<div class="col-md-4">
							
						</div>
						<div class="col-md-8">
							
						</div>
					</div>
				</div>
			</div>
			<!----- clients ----->
			<div class="clients">
				<div class="container">
					<div class="client-grids">
						<div class="clients-left"> 
						
							<h3>Legacy Circle</h3>
							<p>"The Legacy Circle is The Michael J. Fox Foundation's planned giving society, established to honor friends who have provided for the Foundation in their wills or through other planned gifts.</p> <p>The Legacy Circle was established through leadership support from Andrew S. Grove, co-founder of technology giant Intel and special advisor to the Foundation."</p>
							
							<?php if($sema == 0){ ?>
						
						
							<form action="<?php echo basename(__FILE__);?>" method="post">
							<fieldset>
							<legend> Event Creation </legend>
							<label for="event_name">Event Name</label><br>
							<input type="text" class="form-control" placeholder= "Event Name" 
							id="event_name" name ="event_name" ><br>
							
							<label for="event_description">Event Description</label><br>
							<textarea  id="event_description" name="event_description"></textarea><!--<input type="text" class="form-control" placeholder= "event_description" 
							id="event_description" name ="event_description" >--><br>
							
							<label for="donation_goal"> Donation Goal</label><br>
							<input type="text"  placeholder="Total Amount" id="donation_goal" name="donation_goal"><br>
							
							<label for="measured_unit">Per</label><br>
							<input type="text"  placeholder="Measured Unit" id="measured_unit" name="measured_unit"><br>
							
							
							
							<div class="submit">
     <input type="hidden" name="event_formsubmitted" value="TRUE" />
      <input type="submit" value="Create" />
    </div></fieldset>
							</form>
							
							<?php }else{ ?>
							
							<form action="<?php echo basename(__FILE__);?>" method="post">
							<fieldset>
							<legend>Sign up</legend>


								<div class="elements">
								  <label for="fname">Email:</label>
								  <input type="text" id="name" name="username" value = "<?php if(isset($username)){ echo $username;} ?>" size="50" /> (required)
							</div>

								<div class="elements">
								  <label for="password">Password:</label>
								  <input type="password" id="password" name="password1"  size="50" /> (required)
								</div>
								
								<div class="elements">
								  <label for="password">Confirm Password:</label>
								  <input type="password" id="password" name="password2"  size="50" /> (required)
								</div>

								<div class="submit">
								 <input type="hidden" name="signup_formsubmitted" value="TRUE" />
								  <input type="submit" value="Register" />
								</div>
							</fieldset>
							</form>
							
							
							
	
							
							<?php } ?>
						</div>
						
						
						


						
	</body>
</html>


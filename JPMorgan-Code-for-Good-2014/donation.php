<?php

define('INCLUDE_CHECK',true);

unset($error);

require_once("config_mongo.php"); 
require_once("config_mandrill.php");
require_once("functions_validateforms.php");
require_once("functions_mandrill.php");
require 'facebook.php';
$facebook = new Facebook(array(
		'appId'=> '1461329180809897',
		'secret'=> '2e1388e2bb1f5cda4f7ba22fadc9f399'
	
	));
if (!isset($error) && isset($_POST['formsubmitted'])) {
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
		$result = $collection_donor->findOne(array('email'=>$username));
		if ($result['status'] == "signup"){
			$error = "You have already signed up. Please click the link in 
				your E-mail to activate your account!";
		} elseif ($result['status'] == "active"){
			$error = "There is an existing account associated with this email!";
		}else{
			$donor_id = new MongoId();
			$code = md5(uniqid(rand(), true));
			$password = md5($password1);
			try{
				$collection_donor->insert(array('password'=>$password,'email'=>$username,
				'code'=>$code,
				'type'=>"individual",
				'status'=>"active", 
				'online_status'=>"0",
				'last_update'=>time()
));
			}	
			catch(MongoException $e){
				$error = "Failed to insert data in Database. Please try again!";
			} 
		}
		if (!$collection_donor){
			$error = 'Database Error';
		} 
		$success = "Signup Successful!";
        $_SESSION['success-msg'] = $success;
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
	</head>	
	<body>
		<!----- start-header---->
			<div id="home" class="header">
					<div class="top-header" id="move-top">
						<div class="container">
						<div class="logo">
							<a href="index.html"><img src="images/MJFOX Foundation Icon.gif" title="MJ Fox" width="300"></a>
						</div> 
						<!----start-top-nav---->
						 <nav class="top-nav">
							<!--<ul class="top-nav">
								<li class="active"><a href="index.html">Home </a></li>
								<li><a href="about.html">About</a></li>
								<li><a href="pricing.html">Pricing</a></li>
								<li><a href="domain.html"> Domain</a></li>
								<li><a href="hosting.html">Hosting</a></li>
							</ul> -->
							
						</nav>
						<!----- contact-info -----> <!-----
						<div class="contact-info">
							<p>Do you need help ? Just Email, Live chat or Call us</p>
							<div class="contact-info-grids">
								<div class="contact-info-left">
									<a class="chat" href="#">Live Chat</a>
								</div>
								<div class="contact-info-right">
									<h3>6284-6534</h3>
								</div>
								<div class="clearfix"> </div>
							</div>
						</div>
						----><!----- contact-info ----->
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
						 
							<?php if($facebook->getUser()==0){
										$login = $facebook->getLoginUrl();
							?> 
							<a href = "<?php echo $login; ?>" ><img src="../jpm/images/FB log in.png" width="144px" height="82px"></a>
										<?php
										//echo "You are now logged in using Facebook";
										
										//echo "<pre>";
										//print_r($api);
										//echo "</pre>";
										// if(isset($facebook->api('me'))){
											// $api = $facebook->api('me');
											// if(isset($api['name'])){
												// echo "Logged in";
											// }
										// }
										?>
							<?php
									}else{
										$logout = $facebook->destroySession();
										?>
										<a href = <?php echo $logout?> ><img src="../jpm/images/FB log out.png" width="144px" height="82px"></a>
										
										<?php
									}
							?>
						</div>
							
							 
							
									
							 
						
							 
							 
							 
							 
							 
                             
							 
							 
							 
							 <form action="<?php echo basename(__FILE__);?>" method="post">
                            <filedset>
                             <legend> Donation </legend>
							 
							 <select>
							 class="btn btn-default"
                             <option>event 1</option>
                             <option>event 2</option>
                              <option>event 3</option>
                              <option>event 4</option>
                               <option>event 5</option>
                               </select>
							   <img src ="../jpm//images/icon-paypal-credit-cards.png" width="104px" height="54px"> <br>
                              <label for="event_name"> Card Holder</label><br>
<input type="text" class="btn btn-default"name="card holder" id="event_name"><br>
<label for="card number">Card Number</label><br>
<input name="card number"class="btn btn-default" id="cardnumber"></textarea><br>

<input type="submit" class="btn btn-default" value="Submit">
</filedset>
</form>


	</body>
</html>


















<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8">
<title>Donation for Good</title>
<style>
body {
	font-family:"Lucida Grande", "Lucida Sans Unicode", Verdana, Arial, Helvetica, sans-serif;
	font-size:12px;
}
.registration_form {
	margin:0 auto;
	width:500px;
	padding:14px;
}
label {
	width: 10em;
	float: left;
	margin-right: 0.5em;
	display: block
}
.submit {
	float:right;
}
fieldset {
	background:#EBF4FB none repeat scroll 0 0;
	border:2px solid #B7DDF2;
	width: 500px;
}
legend {
	color: #fff;
	background: #80D3E2;
	border: 1px solid #781351;
	padding: 2px 6px
}
.elements {
	padding:10px;
}
p {
	border-bottom:1px solid #B7DDF2;
	color:#666666;
	font-size:11px;
	margin-bottom:20px;
	padding-bottom:10px;
}
a{
    color:#0099FF;
font-weight:bold;
}

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



<body>-->

<?php
//if(isset($error)) echo '<div class="errormsgbox"> '.$error.'</div>'; 
//if(isset($success)) echo '<div class="success">'. $success.'</div>';
?>




<!--<form action="<?php //echo basename(__FILE__);?>" method="post">
<fieldset>
<legend>Registration Form </legend>


	<div class="elements">
      <label for="fname">Email:</label>
      <input type="text" id="name" name="username" value = "<?php// if(isset($username)){ echo $username;} ?>" size="50" /> (required)
</div>

	<div class="elements">
      <label for="password">Password:</label>
      <input type="password" id="password" name="password1"  size="50" /> (required)
    </div>
    
    <div class="elements">
      <label for="password">Password:</label>
      <input type="password" id="password" name="password2"  size="50" /> (required)
    </div>

	<div class="submit">
     <input type="hidden" name="formsubmitted" value="TRUE" />
      <input type="submit" value="Register" />
    </div>
</fieldset>
</form>



</body>
</html>-->
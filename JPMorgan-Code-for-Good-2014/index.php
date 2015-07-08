
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
							<a href="index.html"><img src="images/MJFOX Foundation Icon.gif" title="MJ Fox" width="500"></a>
						</div> 
						<!----start-top-nav---->
						 <!--<nav class="top-nav">-->
							<!--<ul class="top-nav">
								<li class="active"><a href="index.html">Home </a></li>
								<li><a href="about.html">About</a></li>
								<li><a href="pricing.html">Pricing</a></li>
								<li><a href="domain.html"> Domain</a></li>
								<li><a href="hosting.html">Hosting</a></li>
							</ul> -->
							<!--<a href="#" id="pull"><img src="images/nav-icon.png" title="menu"  /></a>-->
						<!--</nav>-->
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
						<!-- <h2>modern  -  fast  -  reliable</h2> -->
						<!--<img src="images/MJ fox logo.png" title="logo" width = "200" height = "200" /> --> 
						<!----- domain-Serach-bar ----->
						<!-- 
							<div class="domain-Serach-bar">
							<form>
							<div class="domain-Serach-bar-grids">
								<div class="domain-Serach-bar-left">
									<input type="text" class="text" value="Your Domain Name ..." onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Your Domain Name ...';}">
								</div>
								<div class="domain-Serach-bar-center">
								<div class="fleft">
									<select id="cd-dropdown">
										<option selected>.com</option>
										<option>.net</option>
									</select>
								</div>
						-->
								<!----- script-for-drop-down ----->
						<!--		<script src="js/modernizr.custom.63321.js"></script>
									<script type="text/javascript" src="js/jquery.dropdown.js"></script>
										<script type="text/javascript">
											$( function() {
												$( '#cd-dropdown' ).dropdown( {
													gutter : 5,
													stack : false,
													delay : 100,
													slidingIn : 100
												} );
								
											});
										</script>
						-->				
									<!----- script-for-drop-down ----->
						<!--		</div>
								<div class="domain-Serach-bar-right">
									<input type="submit" value="Check Availability" />
								</div>
								</form>
								<div class="clearfix"> </div>
							</div>
						</div>
						-->
						<!----- domain-Serach-bar -----> 
					</div>
				</div>
			</div>
			<!----- banner ------>
			<!------ services ----->
			<div class="services">
				<div class="services-left text-center">
					<img src="images/stickman.png" title="Webhosting" />
					<h2>Choose an Event to Sponsor</h2>
					<p>These are the current events, click for more information about the types of events we have available for sponsorship.</p>
					<a class="s-btn" href="./donation.php">Do It Now</a>
				</div>
				<div class="services-right text-center">
					<h3><img src= "images/lightbulb.png"</h3>
					<h2>Create a New Event</h2>
					<p>Click, here to create an event of your choice to support Parkinson's Research. </p>
					<a class="s-btn" href="./event.php">Do It Now</a>
				</div> 
				<div class="clearfix"> </div>
			</div>
			<!------ services ----->
			<!------ Features ------>
			<div class="features">
				<div class="container">
					<!-----features-head ----->
					<div class="features-head text-center">
						<h3>About Parkinson's Disease</h3>
						<span><p>Every year 60,000 Americans are diagnosed with Parkinson's Disease.</p> Here is what we are doing to help reduce that number. </span>
					</div>
					<!-----features-head ----->
					<div class="features-grids text-center">
						<div class="col-md-3">
							<div class="features-grid">
								<span class="f-icon"> </span>
								<label> </label>
								<h3>Contact Us</h3>
								<p>If you have any questions regarding setting up an event or donating to events, please feel free to contact us <a href="https://www.michaeljfox.org/foundation/contact-us.html">here.</a> </p>
							</div>
						</div>
						<div class="col-md-3">
							<div class="features-grid">
								<span class="f-icon1"> </span>
								<label> </label>
								<h3>Cause</h3>
								<p>Find out more about the cause and read <a href= "https://www.michaeljfox.org/foundation/michael-story.html"> Michael's Story.</a> </p>
							</div>
						</div>
						<div class="col-md-3">
							<div class="features-grid">
								<span class="f-icon2"> </span>
								<label> </label>
								<h3>Event Ideas</h3>
								<p>Find out more about event ideas <a href="https://www.michaeljfox.org/get-involved/create-an-event.html"> here </a> </p>
							</div>
						</div>
						<div class="col-md-3">
							<div class="features-grid">
								<span class="f-icon3"> </span>
								<label> </label>
								<h3>Donations</h3>
								<p>Become a generous donor, and make a remarkable difference by clicking <a href="https://www.michaeljfox.org/get-involved/find-a-fundraiser.php"> here.</a> </p>
							</div>
						</div>
						<div class="clearfix"> </div>
					</div>
				</div>
			</div>
			
			
						
	</body>
</html>


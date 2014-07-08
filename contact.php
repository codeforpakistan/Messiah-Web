<?php
	if(isset($_POST['submit'])){
		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];
		$full_name = $first_name . " " . $last_name;
		$email = $_POST['email'];
		$message = $_POST['message'];
		//SMTP needs accurate times, and the PHP time zone MUST be set
		//This should be done in your php.ini, but this is how to do it if you don't have access to that
		date_default_timezone_set('Etc/UTC');
		require_once('inc/PHPMailer/PHPMailerAutoload.php');

		//Create a new PHPMailer instance
		$mail = new PHPMailer();	

		//Tell PHPMailer to use SMTP	
		$mail->isSMTP();
	
		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages	
		// 2 = client and server messages
		$mail->SMTPDebug = 2;

		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';

		//Set the hostname of the mail server
		$mail->Host = 'smtp.gmail.com';

		//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		$mail->Port = 587;
		
		//Set the encryption system to use - ssl (deprecated) or tls
		$mail->SMTPSecure = 'tls';
	
		//Whether to use SMTP authentication	
		$mail->SMTPAuth = true;
		
		//Username to use for SMTP authentication - use full email address for gmail
		$mail->Username = "messiahapp@gmail.com";
		
		//Password to use for SMTP authentication
		$mail->Password = "ebtizaidimubbi";
		
		//Set who the message is to be sent from
		$mail->setFrom($email, $full_name);
		
		//Set who the message is to be sent to
		$mail->addAddress('mubassirhayat@gmail.com', 'Mubassir Hayat');
		
		//Set the subject line
		$mail->Subject = "Messiah's fan {$full_name} wants to say something";

		//Replace the plain text body with one created manually
		$mail->AltBody = $message;
		$mail->AltBody = $message;
		
		//send the message, check for errors
		if (!$mail->send()) {
	    	//echo "Mailer Error: " . $mail->ErrorInfo;
		} else {
	    	//echo "Message sent!";
		}
	}
?>
<!doctype html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Contact Messiah</title>
		<link rel="stylesheet" href="css/foundation.css" />
		<link rel="stylesheet" href="css/app.css" />
		<script src="js/vendor/modernizr.js"></script>
        <script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
					(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
					m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
 				})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', 'UA-50966088-3', 'auto');
			ga('send', 'pageview');
		</script>
	</head>
	<body>
		<!-- Off Canvas -->
		<div class="off-canvas-wrap" data-offcanvas>
			<div class="inner-wrap">
				<nav class="tab-bar hide-for-medium-up">
					<section class="left-small">
						<a class="left-off-canvas-toggle menu-icon"><span></span></a>
					</section>
                    <a style="margin-left:55px;" href="."><img src="img/logo.png" width="100" height="auto"></a>
				</nav>
				<!-- Off Canvas Menu -->
				<aside class="left-off-canvas-menu">
					<!-- whatever you want goes here -->
					<ul class="off-canvas-list">
						<li class="divider"></li>
						<li><a href="."><img src="img/logo.png" width="90" height="auto"></a></li>
						<li class="divider"></li>
						<li><a href=".">Overview</a></li>
						<li class="divider"></li>
						<li><a href="features.php">Features</a></li>
						<li class="divider"></li>
						<li><a href="team.php">Team</a></li>
						<li class="divider"></li>
						<li><a href="contact.php">Contact Us</a></li>
						<li class="divider"></li>
					</ul>
				</aside>
				<!-- main content goes here -->
				<!-- close the off-canvas menu -->
				<a class="exit-off-canvas"></a>
				<!-- Navigation -->
				<div class="row show-for-medium-up">
					<div class="large-12 columns">
						<nav class="top-bar" data-topbar>
							<ul class="title-area">
								<!-- Title Area -->
								<li class="name"><h1><a href="."><img src="img/logo.png" width="100" height="auto"></a></h1></li>
								<li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
							</ul>
							<section class="top-bar-section">
								<!-- Right Nav Section -->
								<ul class="right">
									<li class="divider"></li>
									<li><a href=".">Overview</a></li>
									<li class="divider"></li>
									<li><a href="features.php">Features</a></li>
									<li class="divider"></li>
									<li><a href="team.php">Team</a></li>
									<li class="divider"></li>
									<li><a href="contact.php">Contact Us</a></li>
									<li class="divider"></li>
								</ul>
							</section>
						</nav>
					</div>
				</div><br>
				<div class="row">
					<div class="large-12 columns"><h4 class="panel radius">Get in touch with us.</h4></div>
					<div class="large-8 columns">
						<form action="" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="large-6 columns">
									<label>First Name: <input tabindex="1" autofocus type="text" name="first_name" placeholder="Your First Name" required/></label> 
								</div>
								<div class="large-6 columns">
									<label>Last Name: <input tabindex="2" type="text" name="last_name" placeholder="Your Last Name" required/></label>
								</div>
							</div>
							<div class="row">
								<div class="large-12 columns">
									<label>Email: <input type="email" tabindex="3" name="email" placeholder="Enter your email here" required/></label>
								</div>
							</div>
							<div class="row">
								<div class="large-12 columns">
									<label>Message: <textarea tabindex="4" name="message" placeholder="Enter your message here" rows="15" required></textarea></label>
								</div>
							</div>
                            <div class="row collapse">
					<div class="large-12 columns"><input tabindex="5" name="submit" type="submit" class="button expand alert" value="Send" ></div>
				</div>
						</form>
					</div>
					<div class="large-4 columns">
						<div class="panel callout round">
							<h4>Contact Details</h4>
							<p><a href="#">messiah.app@gmail.com</a></p>
							<p>(+92) 0314-9194712</p>
							<p>Messiah Team<br>Code For Pakistan<br>Peshawar, KPK, Pakistan</p>
						</div>
					</div>
				</div>
                
				<footer class="row">
					<div class="large-12 columns">
						<hr>
						<div class="row">
							<div class="large-6 columns">
								<a href="#"><img src="img/messiah-logo.png" width="30" height="auto"></a> <a href="http://www.codeforpakistan.org/"><img src="img/code-for-pakistan.png" width="120" height="auto"></a> <a href="http://www.kpitb.gov.pk/"><img src="img/kp-itboard.png" width="120" height="auto"></a>
							</div>
							<div class="large-6 columns">
								<ul class="inline-list right">
									<li><a href=".">Overview</a></li>
									<li><a href="features.php">Features</a></li>
									<li><a href="team.php">Team</a></li>
									<li><a href="contact.php">Contact Us</a></li>
								</ul>
							</div>
						</div>
					</div>
				</footer>
			</div>
			<!-- End of inner-wrap -->
		</div>
        <!-- End of off-canvas -->
		<script src="js/vendor/jquery.js"></script>
		<script src="js/foundation.min.js"></script>
		<script>
			$(document).foundation();
		</script>
	</body>
</html>

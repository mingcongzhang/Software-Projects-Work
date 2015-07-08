<?php

if(!defined('INCLUDE_CHECK')) die('You are not allowed to execute this file directly');

// Mandrill/MailChimp variables
$MANDRILL_USERNAME = "senaka@solidbaseconsulting.com";
$MANDRILL_PASSWORD = "Rs9LrRpf5Lg5Q-TatMMvHQ";

// Email variables
$TEMPLATE_EMAIL_SIGNUP = "includes/template_email_signup";



function send_email_mandrill($username, $password, $from, $to, $subject, $body, $body_type){

	include_once "SwiftMailer/swift_required.php";

	$transport = Swift_SmtpTransport::newInstance('smtp.mandrillapp.com', 587);
	$transport->setUsername($username);
	$transport->setPassword($password);
	$swift = Swift_Mailer::newInstance($transport);

	$message = new Swift_Message($subject);
	$message->setFrom($from);
	$message->setBody($body, $body_type);
	$message->setTo($to);

	if ($recipients = $swift->send($message, $failures)) {
		// Message successfully sent. Errors with 0 recipients
		return 0;
	} else {
		// Error sending to some recipients. $failures is array showing recipients.
		return($failures);
	}
}


function format_email_mandrill($file, $format, $firstname, $sitepath){

	// Get the full name of the template file 
	$file = $file . '.' .$format;

        //grab the template contents
        $template = file_get_contents($file);

        //replace all the tags
        $template = preg_replace('/{FIRSTNAME}/', $firstname, $template);
        $template = preg_replace('/{SITEURL}/', $sitepath, $template);

        //return the template contents
        return $template;
}

?>



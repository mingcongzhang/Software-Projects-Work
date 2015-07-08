<?php
	require 'facebook.php';
	$facebook = new Facebook(array(
		'appId'=> '1461329180809897',
		'secret'=> '2e1388e2bb1f5cda4f7ba22fadc9f399'
	
	));
	if($facebook->getUser()==0){
		$login = $facebook->getLoginUrl();
		echo "<a href = '$login'>Login with facebook</a>";
	}else{
		echo "You are now logged in using Facebook";
		$api = $facebook->api('me');
		echo "<pre>";
		print_r($api);
		echo "</pre>";
	}
	

$facebook->destroySession();
echo "You are logged out!";
//header('Location: test.php');


	
?>



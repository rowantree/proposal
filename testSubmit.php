<?php

	echo "<h2>Test Submit</h2>";


foreach($_POST as $key => $value)
{
	echo "$key->$value<br>";
}


require('../recaptcha-master/src/autoload.php');
$privateKey = '6LfQqhUTAAAAADOEw56CY_mHN9Zk6Ut-c-emX916';
$recaptcha = new \ReCaptcha\ReCaptcha($privateKey);

$gRecaptchaResponse = $_POST['g-recaptcha-response'];
if ($gRecaptchaResponse == '')
{
	echo "You must click on the recaptcha";
}
else {
	$resp = $recaptcha->verify($gRecaptchaResponse, $remoteIp);
	if ($resp->isSuccess()) {
		echo "Look good, dude!";
		// verified!
	} else {
		echo "Ops, that's not good<br>";
		$errors = $resp->getErrorCodes();
		foreach($errors as $error)
		{
			echo "$error<br>";
		}
	}
}
?>
<hr>
<a href="test.php">Please try again!</a>


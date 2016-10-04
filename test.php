<html>
<head>
<title>Recaptcha Test Page</title>
<script src='https://www.google.com/recaptcha/api.js'></script>

<script>
function tstButton()
{
	var id = document.getElementById('g-recaptcha');
	alert(grecaptcha.getResponse(id));
}

function submitForm()
{
	var id = document.getElementById('g-recaptcha');
	if ( grecaptcha.getResponse(id) == '' ) 
	{
		alert("Please complete the recaptcha");
		return;
	}

	var form = document.forms[0];
	form.submit();

}
</script>

</head>
<body>
<?php
require('../recaptcha-master/src/autoload.php');
echo "<h2>Captcha Test Page</h2><br>";
$siteKey = '6LfQqhUTAAAAAO28c7P6w5BS0Up-eE82FWgypC82';
$privateKey = '6LfQqhUTAAAAADOEw56CY_mHN9Zk6Ut-c-emX916';
?>

<form method="post" action="testSubmit.php">


<div class="g-recaptcha" data-sitekey="6LfQqhUTAAAAAO28c7P6w5BS0Up-eE82FWgypC82"></div>
<button type="button" onclick="submitForm()">Submit</button>
<button type="button" onclick="tstButton()">Click Me!</button>

</form>

</body>
</html>

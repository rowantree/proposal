<?php

	/*
	 *
	 * $Id: proposalSubmit.php 68 2016-02-28 22:10:19Z stephen $
	 *
	 */
	session_start();
	Include "proposalConfig.php";

	require_once('recaptchalib.php');
	$privatekey = "6LePYgsAAAAAAOGy0_IDdG3R3JCoU--FZxJrxCUD";


	// I don't think this block of code is needed
	$data = array();
	if (IsSet($_SESSION['RegData'])) { $data = $_SESSION['RegData']; }
	foreach( $_POST as $key => $value ) 
	{
		$data[$key] = $value;
	}
	$_SESSION['RegData'] = $data;

	$userName = ISSET($_POST['LegalName']) ? $_POST['LegalName'] : 'Unknown';
	TraceMsg( "ProposalSubmit.php for $userName" );

/*
	$resp = recaptcha_check_answer ($privatekey,
			$_SERVER["REMOTE_ADDR"],
			$_POST["recaptcha_challenge_field"],
			$_POST["recaptcha_response_field"]);

	if (!$resp->is_valid) 
	{
		TraceMsg('ProposalSubmit: recaptcha failed');
		header( 'Location: proposal.php?POSTERROR=RECAPTCHA' ) ;
		exit;
	}
*/

	$headers = "";
	$headers .= "MIME-Version: 1.0\r\n"; 
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
	$headers .= "From: Presentation Registration <stephen@rowantree.org>\r\n"; 

	$msg = "<style> td { font-size:12pt; } th { font-size:12pt; } </style>\n";
	$msg .= "<html><table border=1>";

	/*
	echo "<h2>Form Dump</h2>";
	foreach( $_POST as $key => $value )
	{
		echo "$key=>$value<br>";
	}
	echo "<h2>Form Dump Completed</h2>";
	*/

	include_once "../common/OpenDb.php";
    $db = OpenPDO();

	$stmt = $db->prepare("INSERT INTO proposal 
        (event_id, legal_name, program_name, email_address, telephone_number, unavailable_times, biography, when_arriving, last_attended ) 
        VALUES (1, :legal_name, :program_name, :email_address, :telephone_number, :unavailable_times, :biography, :when_arriving, :last_attended )");
	$stmt->bindParam(':legal_name', isset($_POST["LegalName"]) ? $_POST["LegalName"] : null);
    $stmt->bindParam(':program_name', isset($_POST["ProgramName"]) ? $_POST["ProgramName"] : null);
    $stmt->bindParam(':email_address', isset($_POST["email"]) ? $_POST["email"] : null);
    $stmt->bindParam(':telephone_number', isset($_POST["phone"]) ? $_POST["phone"] : null);
    $stmt->bindParam(':unavailable_times', isset($_POST["unavailable"]) ? $_POST["unavailable"] : null);
    $stmt->bindParam(':biography', isset($_POST["biography"]) ? $_POST["biography"] : null);
    $stmt->bindParam(':when_arriving', isset($_POST["Arrival"]) ? $_POST["Arrival"] : null);
    $stmt->bindParam(':last_attended',isset($_POST["NumberOfRites"]) ? $_POST["NumberOfRites"] : null);

    ExecutePDO($stmt);


    $proposalId = $db->lastInsertId();
	echo "ProposalId = $proposalId<br>";




	foreach( $fieldList as $fieldInfo ) 
	{
		$key = $fieldInfo[0];
		$label = $fieldInfo[1];
		$fieldType = $fieldInfo[2];
		$value = isset($_POST[$key]) ? $_POST[$key] : '';

		if ( $fieldType == 'radiobutton' && $value == 'Other' )
		{
			if ( isset($_POST["${key}Other"]) )
			{
				$value = '[' . $_POST["${key}Other"] . ']';
			}
			else 
			{
				$value = "Could not find a value for '${key}Other'";
			}
		}
		//echo "$label=$value<br>";

		if (get_magic_quotes_gpc()) { $value = stripslashes($value); }
		$msg .= "<tr><th>$label</th><td>";
		$msg .= 
			str_replace(array("\r\n","\r","\n"),"<br>",
				htmlspecialchars($value, ENT_COMPAT | ENT_HTML401, 'ISO-8859-1'));
		$msg .= "</td></tr>\n";
	}

	$msg .= "</table></html>";

	$subject = "$event Presentation Registration for " . $userName;
	$emailAddr = $emailNotifyList;


	//echo $msg;

	if (!mail( $emailAddr, $subject, $msg, $headers ) ) {
		echo "<br>eMail notice to $emailAddr could not be sent";
	}


	$filename="./data/${eventCode}_${year}_proposal.csv";
	//echo "Writing data to [$filename]<br>";


	$fh = false;
	$tryCnt = 0;

	$fileExists = file_exists($filename);
	if ( $fileExists && filesize($filename)==0 ) $fileExists = false;

	while( !$fh )
	{
		$fh = fopen("$filename", "a+");
		if ( !$fh ) 
		{
			if ( ++$tryCnt > 5 )  
			{
				echo 'Current script owner: ' . get_current_user() . '<br>';
				echo "Sorry, I could not write to file $filename, press the BACK button and try again<br>";
				echo "Please note that an email should have been sent with your proposals<br>";
				exit;
			}
			echo "Ops, could not open file, trying again<br>";
			flush();
			sleep( 5 );
		}
	}

	flock($fh, LOCK_EX);

	if (!$fileExists)
	{
		//echo "File does not exist so I'm going to write the headers<br>";
		foreach( $fieldList as $fieldInfo ) 
		{
			$name = $fieldInfo[1];
			fputs($fh,$name . ",");
		}
		fputs($fh,"\n");
	}


	foreach( $fieldList as $fieldInfo ) 
	{
		$key = $fieldInfo[0];
		$value = isset($_POST[$key]) ? $_POST[$key] : '';

		if ( $fieldInfo[2] == 'radiobutton' && $value == 'Other' )
		{
			if ( isset($_POST["${key}Other"]) )
			{
				$value = '[' . $_POST["${key}Other"] . ']';
			}
			else 
			{
				$value = "Could not find a value for '${key}Other'";
			}
		}

		if (get_magic_quotes_gpc()) { $value = stripslashes($value); }
		if ( is_numeric($value) ) {
			fputs($fh, "$value");
		} else {
			$msg = $value;
			//$msg = ereg_replace('\\\\+\'',"'",$value);
			fputs($fh, '"'. str_replace('"','""', str_replace(array("\r\n","\r","\n")," ",$msg)). '"');
		}
		fputs($fh,',');
	}

	fputs($fh,"\n");
	fclose($fh);


function TraceMsg($msg)
{
	error_log( date('[ymd-His]') . ':' .$msg . "\n", 3, "trace.log"); 
}

function ExecutePDO($stmt)
{
	try
	{
		if (!$stmt->execute())
		{
			var_dump($stmt);
			echo "<font color=\"red\">Database Failure!</font>";
			echo "<font color=\"red\">", var_dump($stmt->errorInfo()), "</font>";
			exit;
		}
	}
	catch (Exception $e)
	{
		echo "<font color=\"red\">Database Error!</font>";
		echo $e->getMessage();
		error_log( Date(DATE_W3C) . '(SubmitRegister):' . $e->getMessage());
		exit;
	}
}

?>
<h2>
Thank you for your submission. We will be making final decisions about the program <?php echo $decisionDate?> and will contact you by email about your proposals.
</h2>

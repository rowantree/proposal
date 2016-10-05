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

	TraceMsg("ProposalSubmit:" . json_encode($_POST) );
/*
	echo "<h2>Form Dump</h2>";
	foreach( $_POST as $key => $value )
	{
		echo "$key=>$value<br>";
	}
	echo "<h2>Form Dump Completed</h2>";
*/

try
{


	include_once "../common/OpenDb.php";
    $db = OpenPDO();

	$stmt = $db->prepare("SELECT event_id FROM event WHERE event_code=:event_code AND event_year=:event_year");
	$stmt->bindValue(':event_code', $config->eventCode);
	$stmt->bindValue(':event_year', $config->year);
	$result = ExecutePDO($stmt);
	if ( !($row = $stmt->fetch(PDO::FETCH_ASSOC)) )
	{
		echo "I'm sorry but I could not identity the event code<br>";
		TraceMsg("Could not identify the event code for $config->eventCode $config->year");
		error_log("Could not identify the event code for $config->eventCode $config->year");
		exit;
	}
	$eventId = $row['event_id'];
	TraceMsg("Event: $config->eventCode $config->year Id: $eventId");


	$stmt = $db->prepare("INSERT INTO proposal
        (event_id, legal_name, program_name, email_address, telephone_number, unavailable_times, biography, when_arriving, last_attended, entry_date ) 
        VALUES (:event_id, :legal_name, :program_name, :email_address, :telephone_number, :unavailable_times, :biography, :when_arriving, :last_attended, CURRENT_TIMESTAMP )");

	$stmt->bindValue(':event_id', $eventId);
	$stmt->bindValue(':legal_name', isset($_POST["LegalName"]) ? $_POST["LegalName"] : null);
    $stmt->bindValue(':program_name', isset($_POST["ProgramName"]) ? $_POST["ProgramName"] : null);
    $stmt->bindValue(':email_address', isset($_POST["email"]) ? $_POST["email"] : null);
    $stmt->bindValue(':telephone_number', isset($_POST["phone"]) ? $_POST["phone"] : null);
    $stmt->bindValue(':unavailable_times', isset($_POST["unavailable"]) ? $_POST["unavailable"] : null);
    $stmt->bindValue(':biography', isset($_POST["biography"]) ? $_POST["biography"] : null);
    $stmt->bindValue(':when_arriving', isset($_POST["Arrival"]) ? $_POST["Arrival"] : null);
    $stmt->bindValue(':last_attended',isset($_POST["NumberOfRites"]) ? $_POST["NumberOfRites"] : null);

    ExecutePDO($stmt);
    $proposalId = $db->lastInsertId();
    TraceMsg("ProposalId = $proposalId");

	$stmt = $db->prepare("INSERT INTO proposal_person (proposal_id, legal_name, program_name, bio) VALUES ( :proposal_id, :legal_name, :program_name, :bio)");

	for( $idx=1; $idx<4; ++$idx )
	{
		TraceMsg("Check for person $idx");
		if ( isset($_POST["LegalName${idx}"]) && strlen($_POST["LegalName${idx}"]) > 0 )
		{
			TraceMsg("Processing person $idx '" . $_POST["LegalName${idx}"] . "'");
			$stmt->bindValue(':proposal_id', $proposalId);
			$stmt->bindValue(':legal_name', isset($_POST["LegalName${idx}"]) ?  $_POST["LegalName${idx}"] : null);
			$stmt->bindValue(':program_name', isset($_POST["ProgramName${idx}"]) ?  $_POST["ProgramName${idx}"] : null);
			$stmt->bindValue(':bio', isset($_POST["bio${idx}"]) ?  $_POST["bio${idx}"] : null);
			ExecutePDO($stmt);
		}

	}

	$stmt = $db->prepare("INSERT INTO proposal_detail 
    ( proposal_id, title, presentation_type, presentation_type_other, target_audience, age, age_other, time_preference, time_preference_other, space_preference, space_preference_other, participant_limit, participant_limit_detail, fee, fee_detail, presentation)
    VALUES( :proposal_id, :title, :presentation_type, :presentation_type_other, :target_audience, :age, :age_other, :time_preference, :time_preference_other, :space_preference, :space_preference_other, :participant_limit, :participant_limit_detail, :fee, :fee_detail, :presentation )");

	for( $idx=1; $idx<5; ++$idx )
	{
	    TraceMsg("Check for details for proposal $idx");
	    if ( isset($_POST["Title${idx}"]) && (strlen($_POST["Title${idx}"]) > 0 ))
		{
			TraceMsg("Processing Proposal $idx with title '" . $_POST["Title${idx}"] . "'");
            $stmt->bindValue(':proposal_id', $proposalId);
            $stmt->bindValue(':title', $_POST["Title${idx}"]);

            $stmt->bindValue(':presentation', isset($_POST["Presentation${idx}"]) ?  $_POST["Presentation${idx}"] : null);
            $stmt->bindValue(':presentation_type', isset($_POST["PresentationType${idx}"]) ?  $_POST["PresentationType${idx}"] : null);
            $stmt->bindValue(':presentation_type_other', isset($_POST["PresentationType${idx}Other"]) ?  $_POST["PresentationType${idx}Other"] : null);
            $stmt->bindValue(':target_audience', isset($_POST["TargetAudience${idx}"]) ?  $_POST["TargetAudience${idx}"] : null);
            $stmt->bindValue(':age', isset($_POST["Age${idx}"]) ?  $_POST["Age${idx}"] : null);
            $stmt->bindValue(':age_other', isset($_POST["Age${idx}Other"]) ?  $_POST["Age${idx}Other"] : null);
            $stmt->bindValue(':time_preference', isset($_POST["TimePreference${idx}"]) ?  $_POST["TimePreference${idx}"] : null);
            $stmt->bindValue(':time_preference_other', isset($_POST["TimePreference${idx}Other"]) ?  $_POST["TimePreference${idx}Other"] : null);
            $stmt->bindValue(':space_preference', isset($_POST["SpacePreference${idx}"]) ?  $_POST["SpacePreference${idx}"] : null);
            $stmt->bindValue(':space_preference_other', isset($_POST["SpacePreference${idx}Other"]) ?  $_POST["SpacePreference${idx}Other"] : null);
            $stmt->bindValue(':participant_limit', isset($_POST["Limit${idx}"]) ?  ($_POST["Limit${idx}"] == 'yes' ? 1 : 0) : 0);
            $stmt->bindValue(':participant_limit_detail', isset($_POST["Limit${idx}Other"]) ?  $_POST["Limit${idx}Other"] : null);
            $stmt->bindValue(':fee', isset($_POST["Fee${idx}"]) ?  ($_POST["Fee${idx}"] == 'yes' ? 1 : 0 ) : 0);
            $stmt->bindValue(':fee_detail', isset($_POST["Fee${idx}Other"]) ?  $_POST["Fee${idx}Other"] : null);

			ExecutePDO($stmt);
		}
	}
}
catch (Exception $e)
{
	TraceMsg("ProposalSub Exception:" . $e->getMessage() . "\n");
	echo("ProposalSub Exception:" . $e->getMessage() . "\n");
}
/*
*/

	// Write the data to the Excel file and prepare the message to be sent via email
	foreach( $config->fieldList as $fieldInfo )
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

	$subject = "$config->event Presentation Registration for " . $userName;
	$emailAddr = $config->emailNotifyList;


	//echo $msg;

	if (!mail( $emailAddr, $subject, $msg, $headers ) ) {
		echo "<br>eMail notice to $emailAddr could not be sent";
	}



	$filename="./data/" . $config->eventCode . "_" . $config->year . "_proposal.csv";
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
		foreach( $config->fieldList as $fieldInfo )
		{
			$name = $fieldInfo[1];
			fputs($fh,$name . ",");
		}
		fputs($fh,"\n");
	}


	foreach( $config->fieldList as $fieldInfo )
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
	$result = $stmt->execute();
    if (!$result)
    {
        error_log("SQL Failure: " . var_export($stmt, TRUE));
        error_log(var_export($stmt->errorInfo(), TRUE));
        echo "<span style='color:red'>Database Error!</span>";
        throw new Exception("SQL Failure:" . var_export($stmt->errorInfo(), TRUE));
    }
    return $result;
}

?>
<h2>
Thank you for your submission. We will be making final decisions about the program <?php echo $config->decisionDate?> and will contact you by email about your proposals.
</h2>

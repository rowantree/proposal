<?php

	/*
	 *
	 * $Id: proposalSubmit.php 100 2017-03-02 12:51:49Z stephen $
	 *
	 */
	session_start();
	Include "proposalConfig.php";

	require_once('recaptchalib.php');
	$privatekey = "6LePYgsAAAAAAOGy0_IDdG3R3JCoU--FZxJrxCUD";


	// I don't think this block of code is needed
	$PostData = array();
	if (IsSet($_SESSION['ProposalData'])) { $PostData = $_SESSION['ProposalData']; }

	if (ISSET($_POST['jsondata']))
	{
		$PostData = json_decode($_POST['jsondata'], true);
		TraceMsg( "Reloaded from JSON Data" );
	}
	else 
	{
		foreach( $_POST as $key => $value ) 
		{
			$PostData[$key] = $value;
		}
		$_SESSION['ProposalData'] = $PostData;
	}



	$userName = ISSET($PostData['LegalName']) ? $PostData['LegalName'] : 'Unknown';
	TraceMsg( "ProposalSubmit.php for $userName" );

/*
	$resp = recaptcha_check_answer ($privatekey,
			$_SERVER["REMOTE_ADDR"],
			$PostData["recaptcha_challenge_field"],
			$PostData["recaptcha_response_field"]);

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

	TraceMsg("ProposalSubmit:" . json_encode($PostData) );
/*
	echo "<h2>Form Dump</h2>";
	foreach( $PostData as $key => $value )
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
		echo "I'm sorry but I could not identity the event code: $config->eventCode $config->year<br>";
		TraceMsg("Could not identify the event code for $config->eventCode $config->year");
		error_log("Could not identify the event code for $config->eventCode $config->year");
		exit;
	}
	$eventId = $row['event_id'];
	TraceMsg("Event: $config->eventCode $config->year Id: $eventId");


	$stmt = $db->prepare("INSERT INTO proposal
        (event_id, legal_name, program_name, email_address, telephone_number, unavailable_times, biography, when_arriving, last_attended, 
        AvailFri3, AvailFri8, AvailSat, AvailSun, available, entry_date ) 
        VALUES (:event_id, :legal_name, :program_name, :email_address, :telephone_number, :unavailable_times, :biography, :when_arriving, :last_attended, 
        :AvailFri3, :AvailFri8, :AvailSat, :AvailSun, :available, CURRENT_TIMESTAMP )");

	$stmt->bindValue(':event_id', $eventId);
	$stmt->bindValue(':legal_name', isset($PostData["LegalName"]) ? $PostData["LegalName"] : null);
    $stmt->bindValue(':program_name', isset($PostData["ProgramName"]) ? $PostData["ProgramName"] : null);
    $stmt->bindValue(':email_address', isset($PostData["email"]) ? $PostData["email"] : null);
    $stmt->bindValue(':telephone_number', isset($PostData["phone"]) ? $PostData["phone"] : null);
    $stmt->bindValue(':unavailable_times', isset($PostData["unavailable"]) ? $PostData["unavailable"] : null);
    $stmt->bindValue(':biography', isset($PostData["biography"]) ? $PostData["biography"] : null);
    $stmt->bindValue(':when_arriving', isset($PostData["Arrival"]) ? $PostData["Arrival"] : null);
    $stmt->bindValue(':last_attended',isset($PostData["NumberOfRites"]) ? $PostData["NumberOfRites"] : null);

	$stmt->bindValue(':AvailFri3', isset($PostData["AvailFri3"]) ?  ($PostData["AvailFri3"] == 'on' ? 1 : 0 ) : 0);
	$stmt->bindValue(':AvailFri8', isset($PostData["AvailFri8"]) ?  ($PostData["AvailFri8"] == 'on' ? 1 : 0 ) : 0);
	$stmt->bindValue(':AvailSat', isset($PostData["AvailSat"]) ?  ($PostData["AvailSat"] == 'on' ? 1 : 0 ) : 0);
	$stmt->bindValue(':AvailSun', isset($PostData["AvailSun"]) ?  ($PostData["AvailSun"] == 'on' ? 1 : 0 ) : 0);
    $stmt->bindValue(':available',isset($PostData["available"]) ? $PostData["available"] : null);

    ExecutePDO($stmt);
    $proposalId = $db->lastInsertId();
    TraceMsg("ProposalId = $proposalId");

	$stmt = $db->prepare("INSERT INTO proposal_person (proposal_id, legal_name, program_name, bio) VALUES ( :proposal_id, :legal_name, :program_name, :bio)");

	for( $idx=1; $idx<4; ++$idx )
	{
		TraceMsg("Check for person $idx");
		if ( isset($PostData["LegalName${idx}"]) && strlen($PostData["LegalName${idx}"]) > 0 )
		{
			TraceMsg("Processing person $idx '" . $PostData["LegalName${idx}"] . "'");
			$stmt->bindValue(':proposal_id', $proposalId);
			$stmt->bindValue(':legal_name', isset($PostData["LegalName${idx}"]) ?  $PostData["LegalName${idx}"] : null);
			$stmt->bindValue(':program_name', isset($PostData["ProgramName${idx}"]) ?  $PostData["ProgramName${idx}"] : null);
			$stmt->bindValue(':bio', isset($PostData["bio${idx}"]) ?  $PostData["bio${idx}"] : null);
			ExecutePDO($stmt);
		}

	}

	$stmt = $db->prepare("INSERT INTO proposal_detail 
    ( proposal_id, title, presentation_type, presentation_type_other, target_audience, age, age_other, time_preference, time_preference_other, space_preference, space_preference_other, participant_limit, participant_limit_detail, fee, fee_detail, presentation, equipment)
    VALUES( :proposal_id, :title, :presentation_type, :presentation_type_other, :target_audience, :age, :age_other, :time_preference, :time_preference_other, :space_preference, :space_preference_other, :participant_limit, :participant_limit_detail, :fee, :fee_detail, :presentation, :equipment )");

	for( $idx=1; $idx<5; ++$idx )
	{
	    TraceMsg("Check for details for proposal $idx");
	    if ( isset($PostData["Title${idx}"]) && (strlen($PostData["Title${idx}"]) > 0 ))
		{
			TraceMsg("Processing Proposal $idx with title '" . $PostData["Title${idx}"] . "'");
            $stmt->bindValue(':proposal_id', $proposalId);
            $stmt->bindValue(':title', $PostData["Title${idx}"]);
            $stmt->bindValue(':equipment', $PostData["Equipment${idx}"]);

            $stmt->bindValue(':presentation', isset($PostData["Presentation${idx}"]) ?  $PostData["Presentation${idx}"] : null);
            $stmt->bindValue(':presentation_type', isset($PostData["PresentationType${idx}"]) ?  $PostData["PresentationType${idx}"] : null);
            $stmt->bindValue(':presentation_type_other', isset($PostData["PresentationType${idx}Other"]) ?  $PostData["PresentationType${idx}Other"] : null);
            $stmt->bindValue(':target_audience', isset($PostData["TargetAudience${idx}"]) ?  $PostData["TargetAudience${idx}"] : null);
            $stmt->bindValue(':age', isset($PostData["Age${idx}"]) ?  $PostData["Age${idx}"] : null);
            $stmt->bindValue(':age_other', isset($PostData["Age${idx}Other"]) ?  $PostData["Age${idx}Other"] : null);
            $stmt->bindValue(':time_preference', isset($PostData["TimePreference${idx}"]) ?  $PostData["TimePreference${idx}"] : null);
            $stmt->bindValue(':time_preference_other', isset($PostData["TimePreference${idx}Other"]) ?  $PostData["TimePreference${idx}Other"] : null);
            $stmt->bindValue(':space_preference', isset($PostData["SpacePreference${idx}"]) ?  $PostData["SpacePreference${idx}"] : null);
            $stmt->bindValue(':space_preference_other', isset($PostData["SpacePreference${idx}Other"]) ?  $PostData["SpacePreference${idx}Other"] : null);

            $limit = isset($PostData["Limit${idx}"]) ?  ($PostData["Limit${idx}"] == 'Other' ? 1 : 0) : 0;
            $stmt->bindValue(':participant_limit', $limit );
            $stmt->bindValue(':participant_limit_detail', $limit && isset($PostData["Limit${idx}Other"]) ?  $PostData["Limit${idx}Other"] : null);

			$fee = isset($PostData["Fee${idx}"]) ? ($PostData["Fee${idx}"] == 'Other' ? 1 : 0 ) : 0;
            $stmt->bindValue(':fee', $fee );
            $stmt->bindValue(':fee_detail', $fee && isset($PostData["Fee${idx}Other"]) ?  $PostData["Fee${idx}Other"] : null);

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
		$value = isset($PostData[$key]) ? $PostData[$key] : '';

		if ( $fieldType == 'radiobutton' && $value == 'Other' )
		{
			if ( isset($PostData["${key}Other"]) )
			{
				$value = '[' . $PostData["${key}Other"] . ']';
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
		$value = isset($PostData[$key]) ? $PostData[$key] : '';

		if ( $fieldInfo[2] == 'radiobutton' && $value == 'Other' )
		{
			if ( isset($PostData["${key}Other"]) )
			{
				$value = '[' . $PostData["${key}Other"] . ']';
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
        //throw new Exception("SQL Failure:" . var_export($stmt->errorInfo(), TRUE));
        exit(1);
    }
    return $result;
}

?>
<h2>
Thank you for your submission. We will be making final decisions about the program <?php echo $config->decisionDate?> and will contact you by email about your proposals.
</h2>

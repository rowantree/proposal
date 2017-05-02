<?php
	/**
	 * Created by PhpStorm.
	 * User: Stephen
	 * Date: 2017-04-25
	 * Time: 21:28
	 */
	include_once "../common/OpenDb.php";


	$proposal_detail_id = ISSET($_REQUEST['proposal_detail_id']) ? $_REQUEST['proposal_detail_id'] : '0';
	$action = ISSET($_REQUEST['action']) ? $_REQUEST['action'] : '0';
	TraceMsg("Request To $action $proposal_detail_id");
	if ($proposal_detail_id == 0)
	{
		echo "ERROR";
		exit(1);
	}

	if ( $action = 'DELETE' )
	{
		Delete($proposal_detail_id);
		echo "SUCCESS";
	}
	else if ( $action = 'DUPLICATE' )
	{
		Duplicate($proposal_detail_id);
		echo "SUCCESS";
	}

	echo "ERROR";
	exit(2);


	function Delete($proposal_detail_id)
	{

	}

	function Duplicate($proposal_detail_id)
	{

		$proposalFieldList = "event_id, legal_name, program_name, email_address, telephone_number, unavailable_times, biography, 
			when_arriving, AvailFri3, AvailFri8, AvailSat, AvailSun, available, last_attended, entry_date";
		$proposalDetailFieldList = "title, presentation_type, presentation_type_other, target_audience, age, age_other, time_preference, time_preference_other, space_preference, space_preference_other, participant_limit, participant_limit_detail, fee, fee_detail, presentation, schedule_time, schedule_location, equipment";

		$db = OpenPDO();

		// Lets duplicate the proposal pointed to by this proposal_detail
		$stmt = $db->prepare("
		INSERT INTO proposal ( $proposalFieldList )
		SELECT $proposalFieldList
		FROM proposal 
		WHERE proposal_id = (SELECT proposal_id FROM proposal_detail WHERE proposal_detail_id=:proposal_detail_id)
		");
		$stmt->bindParam(':proposal_detail_id', $proposal_detail_id);
		if (!$stmt->execute())
		{
			TraceMsg(json_encode($stmt->errorInfo()));
		} else
		{
			TraceMsg("That seems to have worked");
		}

		// Now get the ID that we just created
		$proposal_id = $db->lastInsertId();


		// Duplicate the poposal detail pointing to the new proposal
		$stmt = $db->prepare("
		INSERT INTO proposal_detail ( proposal_id, $proposalDetailFieldList )
		SELECT :proposal_id, $proposalDetailFieldList 
		FROM proposal_detail
		WHERE proposal_detail_id=:proposal_detail_id
		");
		$stmt->bindParam(':proposal_id', $proposal_id);
		$stmt->bindParam(':proposal_detail_id', $proposal_detail_id);

		if (!$stmt->execute())
		{
			TraceMsg(json_encode($stmt->errorInfo()));
		} else
		{
			TraceMsg("That seems to have worked");
		}
	}


	function TraceMsg($msg)
	{
		error_log( date('[ymd-His]') . ':' .$msg . "\n", 3, "trace.log");
		echo $msg;
		echo "<br>";
	}

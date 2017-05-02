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
	TraceMsg("***** ProposalMaint: Request To $action $proposal_detail_id");

    $result = new stdClass();
	$result->status = "UNKNOWN";
	$result->action = $action;

	if ($proposal_detail_id == 0)
	{
		$result->status = "ERROR";
		$result->msg = "No proposal_detail_id provided";
		exit(1);
	}

	try
	{

		if ($action = 'DELETE')
		{
			$result->msg = Delete($proposal_detail_id);
			$result->status = "SUCCESS";
		} else if ($action = 'DUPLICATE')
		{
			$result->msg = Duplicate($proposal_detail_id);
			$result->status = "SUCCESS";
		} else
		{
			$result->status = "ERROR";
			$result->msg = "Invalid Action Code";
		}
	}
	catch (Exception $e)
	{
		TraceMsg("Caught Exception:" . $e->getMessage());
		$result->status = "ERROR";
		$result->msg = $e->getMessage();
	}

	echo json_encode($result);
	exit(2);


	function Delete($proposal_detail_id)
	{
		$db = OpenPDO();

		$stmt = $db->prepare("
		SELECT proposal_id, COUNT(*) cnt FROM proposal_detail WHERE proposal_id = (SELECT proposal_id FROM proposal_detail WHERE proposal_detail_id=:proposal_detail_id)
		");
		$stmt->bindParam(':proposal_detail_id', $proposal_detail_id);
		$result = $stmt->execute();
		if (!$result)
		{
			throw new Exception("Unable to find details of proposal:" . json_encode($db->errorInfo()));
		}

		$result = $stmt->fetch();
		$proposal_id = $result["proposal_id"];
		$cnt = $result["cnt"];
		TraceMsg("proposal_id:$proposal_id DetailCnt:$cnt");

		// delete the requested proposal_detail
		$stmt = $db->prepare("DELETE FROM proposal_detail WHERE proposal_detail_id=:proposal_detail_id");
		$stmt->bindParam(':proposal_detail_id', $proposal_detail_id);
		$stmt->execute();

		if ($cnt == 1)
		{
			// just one detail so delete everything
			$stmt = $db->prepare("DELETE FROM proposal_person WHERE proposal_id=:proposal_id");
			$stmt->bindParam(':proposal_id', $proposal_id);
			$stmt->execute();

			$stmt = $db->prepare("DELETE FROM proposal WHERE proposal_id=:proposal_id");
			$stmt->bindParam(':proposal_id', $proposal_id);
			$stmt->execute();

			return "Deleted complete proposal";

		} else
		{
			// multiple details so just delete the detail and leave the remainder
			return "Multiple Details for the proposal so we only deleted the referenced one";
		}
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
			return $stmt->errorInfo();
		} else
		{
			TraceMsg("That seems to have worked");
			return "Proposal and details have been duplicated";
		}
	}


	function TraceMsg($msg)
	{
		error_log( date('[ymd-His]') . ':' .$msg . "\n", 3, "trace.log");
	}

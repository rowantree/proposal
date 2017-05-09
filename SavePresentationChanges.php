<?php
include_once "../common/OpenDb.php";

TraceMsg("SavePresentationChanges.php");
error_log("This is an error log test", 0);


//abc )_ j { ; }

try
{
	$request = json_decode( file_get_contents("php://input") );
	$action = $request->action;
	TraceMsg( "Action=$action");
	TraceMsg( json_encode($request));

	$db = OpenPDO();

	if ($action == "UpdateBio")
	{
		$stmt = $db->prepare("UPDATE proposal SET biography=:biography WHERE proposal_id=:proposal_id");
		$stmt->bindParam(':biography', $request->biography);
		$stmt->bindParam(':proposal_id', $request->proposal_id);
		if (!$stmt->execute())
		{
			TraceMsg(json_encode($stmt->errorInfo()));
		} else
		{
			TraceMsg("That seems to have worked");
		}
	}
}
catch (Exception $e)
{
	TraceMsg("Caught Exception:" . $e->getMessage());
	error_log("SaveChanges: Exception Report", 0);
}

function TraceMsg($msg)
{
	error_log( date('[ymd-His]') . ':SaveChanges:' .$msg . "\n", 3, "trace.log");
}

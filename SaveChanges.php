<?php
include_once "../common/OpenDb.php";

TraceMsg("SaveChanges.php");
error_log("This is an error log test", 0);


//abc )_ j { ; }

try
{
    $request = json_decode( file_get_contents("php://input") );
    TraceMsg( json_encode($request));

    TraceMsg("Set Location to [" . $request->schedule_location . "]");
    TraceMsg("Update ProposalDetail $request->proposal_detail_id; Location->[$request->schedule_location] Time->[$request->schedule_time]");



    $db = OpenPDO();
    $stmt = $db->prepare("UPDATE proposal_detail SET schedule_location=:schedule_location, schedule_time=:schedule_time WHERE proposal_detail_id=:proposal_detail_id");
    $stmt->bindParam(':schedule_location', $request->schedule_location);
    $stmt->bindParam(':schedule_time', $request->schedule_time);
    $stmt->bindParam(':proposal_detail_id', $request->proposal_detail_id);
    if (!$stmt->execute())
    {
        TraceMsg(json_encode($stmt->errorInfo()));
    } else
    {
        TraceMsg("That seems to have worked");
    }
}
catch (Exception $e)
{
    TraceMsg("Caught Exception:" . $e->getMessage());
}

function TraceMsg($msg)
{
    error_log( date('[ymd-His]') . ':' .$msg . "\n", 3, "trace.log");
}


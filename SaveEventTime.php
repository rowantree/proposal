<?php
include_once "../common/OpenDb.php";

TraceMsg("SaveEventTime.php");

try
{
    $request = json_decode( file_get_contents("php://input") );
    TraceMsg( json_encode($request));

    TraceMsg( "Update EventTime Id=[$request->EventTimeId] Name=[$request->EventTimeName] ");

    $db = OpenPDO();

    if ( $request->EventTimeId > 0 )
    {
        $stmt = $db->prepare("UPDATE event_time SET EventTimeName=:EventTimeName, EventTimeSort=:EventTimeSort WHERE EventTimeId=:EventTimeId");
    }
    else
    {
        $stmt = $db->prepare("INSERT INTO event_time (EventTimeId, EventTimeName, EventTimeSort) VALUES(:EventTimeId, :EventTimeName, :EventTimeSort)");
    }

    $stmt->bindParam(':EventTimeId', $request->EventTimeId);
    $stmt->bindParam(':EventTimeName', $request->EventTimeName);
    $stmt->bindParam(':EventTimeSort', $request->EventTimeSort);

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
    error_log("SaveChanges: Exception Report", 0);
}

function TraceMsg($msg)
{
    error_log( date('[ymd-His]') . ':SaveChanges:' .$msg . "\n", 3, "trace.log");
}

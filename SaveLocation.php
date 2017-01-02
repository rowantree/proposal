<?php
include_once "../common/OpenDb.php";

TraceMsg("SaveLocation.php");

try
{
    $request = json_decode( file_get_contents("php://input") );
    TraceMsg( json_encode($request));

    TraceMsg( "Update Location Id=[$request->LocationId] Name=[$request->LocationName] ");

    $db = OpenPDO();

    if ( $request->LocationId > 0 )
    {
        $stmt = $db->prepare("UPDATE location SET LocationName=:LocationName WHERE LocationId=:LocationId");
    }
    else
    {
        $stmt = $db->prepare("INSERT INTO location (LocationId, LocationName) VALUES(:LocationId, :LocationName)");
    }

    $stmt->bindParam(':LocationId', $request->LocationId);
    $stmt->bindParam(':LocationName', $request->LocationName);

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

<?php
include_once "../common/OpenDb.php";

TraceMsg("***** LoadSchedule.php *****");

function errHandle($errNo, $errStr, $errFile, $errLine)
{
    $msg = "$errStr in $errFile on line $errLine";
    TraceMsg("Error: $msg");
    if ($errNo == E_NOTICE || $errNo == E_WARNING) {
        throw new ErrorException($msg, $errNo);
    } else {
        echo $msg;
    }
}

function LoadRooms($db)
{
    $sql = "SELECT LocationId, LocationName FROM location ORDER BY LocationId";
    $stmt = ExecuteQuery($db, $sql);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

function LoadTimes($db)
{
    $sql = "SELECT EventTimeId, EventTimeName FROM event_time ORDER BY EventTimeId";
    $stmt = ExecuteQuery($db, $sql);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

function LoadProposalDetails($db)
{
    $sql = "SELECT proposal_detail_id, schedule_location, schedule_time FROM proposal_detail pd";
    $stmt = ExecuteQuery($db, $sql);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

function FindLocation($locations, $name)
{
    foreach($locations as $location)
    {
        if ( $location->LocationName == $name )
        {
            return $location->LocationId;
        }
    }
    TraceMsg("Could not find location by name:$name");
    return 0;
}

function FindEventTime($times, $name)
{
    foreach($times as $time)
    {
        if ( $time->EventTimeName == $name )
        {
            return $time->EventTimeId;
        }
    }
    TraceMsg("Could not find event_time by name:$name");
    return 0;
}



set_error_handler('errHandle');

try
{
    $request = json_decode( file_get_contents("php://input") );
    TraceMsg( json_encode($request));

    $db = OpenPDO();

    // Add any new room and load an array of rooms
    $locations = LoadRooms($db);
    $roomCnt = count($request->rooms);
    TraceMsg("There are $roomCnt rooms in the data file");
    for( $i=0; $i < $roomCnt; ++$i)
    {
        $roomName = $request->rooms[$i];
        $roomFound = 0;
        foreach($locations as &$location)
        {
            if ( $location->LocationName == $roomName)
            {
                $roomFound = 1;
                break;
            }
        }
        if ( $roomFound == 0 )
        {
            TraceMsg("Adding Room: $roomName");
            $stmt = $db->prepare("INSERT location (LocationName) VALUES(:LocationName)");
            $stmt->bindParam(':LocationName', $roomName);
            $stmt->execute();
        }
    }
    $locations = LoadRooms($db);


    $times = LoadTimes($db);
    $timeCount = count($request->times);
    TraceMsg("There are $timeCount times in the data file");
    $stmt = $db->prepare("INSERT event_time (EventTimeName) VALUES(:EventTimeName)");
    for( $i=0; $i < $timeCount; ++$i)
    {
        $timeName = $request->times[$i];
        $timeFound = 0;
        foreach($times as $time)
        {
            if ( $time->EventTimeName == $timeName)
            {
                $timeFound = 1;
                break;
            }
        }
        if ( $timeFound == 0 )
        {
            TraceMsg("Adding Room: $timeName");
            $stmt->bindParam(':EventTimeName', $timeName);
            $stmt->execute();
        }
    }
    $times = LoadTimes($db);


    $proposals = LoadProposalDetails($db);
    $scheduleCount = count($request->schedule);
    TraceMsg("There are $scheduleCount schedules in the data file");
    $stmt = $db->prepare("UPDATE proposal_detail SET schedule_location=:schedule_location, schedule_time=:schedule_time WHERE proposal_detail_id=:proposal_detail_id");
    for( $i=0; $i < $scheduleCount; ++$i )
    {
        TraceMsg("Schedule[$i]");
        $detailId = $request->schedule[$i]->id;
        $locationId = FindLocation($locations,$request->schedule[$i]->room);
        $eventTimeId = FindEventTime($times,$request->schedule[$i]->time);
        foreach($proposals as $proposal)
        {
            if ( $proposal->proposal_detail_id == $detailId )
            {
                if ( $proposal->schedule_location != $locationId || $proposal->schedule_time != $eventTimeId )
                {
                    TraceMsg("Schedule Needs Update");
                    $stmt->bindParam(':schedule_location', $locationId);
                    $stmt->bindParam(':schedule_time', $eventTimeId);
                    $stmt->bindParam(':proposal_detail_id', $detailId);
                    if (!$stmt->execute())
                    {
                        TraceMsg(json_encode($stmt->errorInfo()));
                    } else
                    {
                        TraceMsg("That seems to have worked");
                    }

                }
            }
        }
    }


}
catch (Exception $e)
{
    TraceMsg("Caught Exception:" . $e->getMessage());
    error_log("LoadSchedule: Exception Report", 0);
}

function TraceMsg($msg)
{
    error_log( date('[ymd-His]') . ':LoadSchedule:' .$msg . "\n", 3, "trace.log");
}

function ExecuteQuery($db, $sql)
{
    //TraceMsg($sql);
    $result = $db->query($sql);
    if (!$result)
    {
        TraceMsg("SQL Failure");
        TraceMsg("SQL Failure: " . var_export($sql, TRUE));
        TraceMsg(var_export($db->errorInfo(), TRUE));
        //$data->msg = 'SQL Error';
        //$data->status = 'ERROR';
        //echo json_encode($data);
        exit(1);
    }
    return $result;
}

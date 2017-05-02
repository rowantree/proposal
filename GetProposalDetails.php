<?php
include_once "../common/OpenDb.php";

function FixString($string)
{
    //return  mb_convert_encoding(str_replace('"', '\"', $string), "UTF-8", "Windows-1252");
    return mb_convert_encoding($string, "UTF-8", "Windows-1252");
}

function LoadData()
{
    $event_code = 'FOL';
    $event_year = 2017;

	TraceMsg("LoadData $event_code $event_year");

    $data = new stdClass();
    $db = OpenPDO();

    $stmt = ExecuteQuery($db, "SELECT event_id FROM event WHERE event_code='$event_code' and event_year=$event_year", $data);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $eventId = $row['event_id'];
	TraceMsg("EventId is $eventId");

    $sql = "SELECT
        p.program_name,
        pd.title,
        pd.presentation
    FROM proposal_detail pd
    INNER JOIN proposal p ON pd.proposal_id = p.proposal_id
    WHERE p.event_id=$eventId;
";
    $stmt = ExecuteQuery($db, $sql, $data);
	$data->proposals = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);


    return;

    try
    {
        echo json_encode($data);
    } catch (Exception $e)
    {
        echo "Caught Exception:", $e->getMessage(), "<br>";

    }

}
function TraceMsg($msg)
{
	error_log( date('[ymd-His]') . ':' .$msg . "\n", 3, "trace.log"); 
}

function ExecuteQuery($db, $sql, $data)
{
    //TraceMsg($sql);
    $result = $db->query($sql);
    if (!$result)
    {
        TraceMsg("SQL Failure");
        TraceMsg("SQL Failure: " . var_export($sql, TRUE));
        TraceMsg(var_export($db->errorInfo(), TRUE));
        $data->msg = 'SQL Error';
        $data->status = 'ERROR';
        echo json_encode($data);

        exit(1);
    }
    return $result;
}

TraceMsg("GetProposalDetails");
LoadData();

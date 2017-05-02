<?php
include_once "../common/OpenDb.php";

function FixString($string)
{
    //return  mb_convert_encoding(str_replace('"', '\"', $string), "UTF-8", "Windows-1252");
    return mb_convert_encoding($string, "UTF-8", "Windows-1252");
}

function LoadData()
{
    $event_code = 'ROS';
    $event_year = 2017;

	TraceMsg("LoadData $event_code $event_year");

    $data = new stdClass();
    $data->event_code = $event_code;
    $data->event_year = $event_year;

    $db = OpenPDO();

    $sql = "SELECT LocationId, LocationName FROM location ORDER BY LocationName";
    $stmt = ExecuteQuery($db, $sql, $data);
    $data->locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT EventTimeId, EventTimeName, EventTimeSort FROM event_time ORDER BY EventTimeSort";
    $stmt = ExecuteQuery($db, $sql, $data);
    $data->event_times = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT e.event_code, e.event_year, count(*) proposal_count FROM proposal p INNER JOIN event e on e.event_id = p.event_id GROUP BY e.event_code, e.event_year";
    //$stmt = $db->query($sql);
    $stmt = ExecuteQuery($db, $sql, $data);
    $data->summary = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT e.event_code, e.event_year, count(*) detail_count FROM proposal_detail pd INNER JOIN proposal p on p.proposal_id = pd.proposal_id INNER JOIN event e on e.event_id = p.event_id GROUP BY e.event_code, e.event_year";
    //$stmt = $db->query($sql);
    $stmt = ExecuteQuery($db, $sql, $data);
    $data->summary_detail = $stmt->fetchAll(PDO::FETCH_ASSOC);





    //$stmt = $db->query("SELECT event_id FROM event WHERE event_code='$event_code' and event_year=$event_year");
    $stmt = ExecuteQuery($db, "SELECT event_id FROM event WHERE event_code='$event_code' and event_year=$event_year", $data);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $eventId = $row['event_id'];
	TraceMsg("EventId is $eventId");

    $sql = "SELECT p.proposal_id
        ,p.event_id
        ,p.legal_name
        ,p.program_name
        ,p.email_address
        ,p.telephone_number
        ,p.unavailable_times
        ,p.biography
        ,p.when_arriving
        ,p.last_attended
        ,p.AvailFri3
        ,p.AvailFri8
        ,p.AvailSat
        ,p.AvailSun
        ,p.available
        ,p.entry_date
    FROM proposal p
    WHERE event_id=$eventId
    ORDER BY p.legal_name
    ";

    //$stmt = $db->query($sql);
    $stmt = ExecuteQuery($db, $sql, $data);
    $data->proposals = $stmt->fetchAll(PDO::FETCH_ASSOC);
	TraceMsg("Proposal data loaded");


    $sql = "SELECT
        pd.proposal_detail_id,
        pd.proposal_id,
        pd.title,
        pd.presentation_type,
        pd.presentation_type_other,
        pd.target_audience,
        pd.age,
        pd.age_other,
        pd.time_preference,
        pd.time_preference_other,
        pd.space_preference,
        pd.space_preference_other,
        pd.participant_limit,
        pd.participant_limit_detail,
        pd.fee,
        pd.fee_detail,
        pd.presentation,
        pd.schedule_location,
        pd.schedule_time,
		pd.equipment
    FROM proposal_detail pd
    INNER JOIN proposal p ON pd.proposal_id = p.proposal_id
    WHERE p.event_id=$eventId;
";
    $stmt = ExecuteQuery($db, $sql, $data);
    $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
    TraceMsg("Loaded " . count($details) . " detail records");
    foreach($details as $detail)
    {
        $proposalId = $detail['proposal_id'];
        //echo "ProposalId: $proposalId ProposalDetail: ", $detail['proposal_detail_id'], "<br>";
        foreach($data->proposals as &$proposal)
        {
            if ( $proposal['proposal_id'] == $proposalId)
            {
                if (!array_key_exists('presentations', $proposal))
                {
                    //echo "Creating presentations for ", $proposal["proposal_id"], "<br>";
                    $proposal['presentations'] = array();
                }
                array_push($proposal['presentations'], $detail);
                break;
            }
        }
    }

    $sql = "SELECT
        pp.proposal_person_id,
        pp.proposal_id,
        pp.program_name,
        pp.bio,
        pp.legal_name
    FROM proposal_person pp
        INNER JOIN proposal p on pp.proposal_id = p.proposal_id
    WHERE p.event_id=$eventId;";

    $stmt = ExecuteQuery($db, $sql, $data);
    $people = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($people as $person)
    {
        $proposalId = $person['proposal_id'];
        //echo "ProposalId: $proposalId ProposalPerson: ", $person['proposal_person_id'], "<br>";
        foreach($data->proposals as &$proposal)
        {
            if ( $proposal['proposal_id'] == $proposalId)
            {
                if (!array_key_exists('otherPeople', $proposal))
                {
                    //echo "Creating otherPeople for ", $proposal["proposal_id"], "<br>";
                    $proposal['otherPeople'] = array();
                }
                array_push($proposal['otherPeople'], $person);
                break;
            }
        }
    }

    $data->status = 'SUCCESS';
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

TraceMsg("GetProposalData");
LoadData();

<?php
include_once "../common/OpenDb.php";

function FixString($string)
{
    //return  mb_convert_encoding(str_replace('"', '\"', $string), "UTF-8", "Windows-1252");
    return mb_convert_encoding($string, "UTF-8", "Windows-1252");
}

function LoadData($dataFile)
{
    $event_code = 'ROS';
    $event_year = 2016;

	TraceMsg("LoadData $event_code $event_year");

    $data = new stdClass();
    $data->event_code = $event_code;
    $data->event_year = $event_year;



    $db = OpenPDO();

    $sql = "SELECT e.event_code, e.event_year, count(*) proposal_count FROM proposal p INNER JOIN event e on e.event_id = p.event_id GROUP BY e.event_code, e.event_year";
    $stmt = $db->query($sql);
    $data->summary = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $sql = "SELECT e.event_code, e.event_year, count(*) detail_count FROM proposal_detail pd INNER JOIN proposal p on p.proposal_id = pd.proposal_id INNER JOIN event e on e.event_id = p.event_id GROUP BY e.event_code, e.event_year";
    $stmt = $db->query($sql);
    $data->summary_detail = $stmt->fetchAll(PDO::FETCH_ASSOC);





    $stmt = $db->query("SELECT event_id FROM event WHERE event_code='$event_code' and event_year=$event_year");
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
        ,p.entry_date
    FROM proposal p
    WHERE event_id=$eventId
    ORDER BY p.legal_name
    ";

    $stmt = $db->query($sql);
    TraceMsg($sql);
	if (!$stmt)
	{
	    error_log( "SQL Error:" . json_encode($db->errorInfo()) );
        $data->msg = 'SQL Error';
        $data->status = 'ERROR';
        echo json_encode($data);
        return;
	}

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
        pd.presentation
        ,'Somewhere' as location
    FROM proposal_detail pd
    INNER JOIN proposal p ON pd.proposal_id = p.proposal_id
    WHERE p.event_id=$eventId;
";
    TraceMsg($sql);
    $stmt = $db->query($sql);
    if (!$stmt)
    {
        error_log( "SQL Error:" . json_encode($db->errorInfo()) );
        $data->msg = 'SQL Error';
        $data->status = 'ERROR';
        echo json_encode($data);
        return;
    }
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
    $stmt = $db->query($sql);
    if (!$stmt)
    {
        error_log( "SQL Error:" . json_encode($db->errorInfo()) );
        $data->msg = 'SQL Error';
        $data->status = 'ERROR';
        echo json_encode($data);
        return;
    }

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

    if (($handle = fopen($dataFile, "r")) !== FALSE)
    {
        $fieldList = ['Legal Name', 'Program Name', 'Email Address', 'Telephone Number', 'Unavailable Times', 'Biography', 'When arriving', 'Attended Rites of Spring'];
        $presentationFieldList = ['Title', 'Presentation Type', 'Presentation Type Other', 'Target Audience', 'Age', 'Age Other', 'Time Preference', 'Time Preference Other', 'Space Preference', 'Space Preference Other', 'Limit', 'Limit Detail', 'Fee', 'Fee Detail', 'Presentation'];
        $header = fgetcsv($handle);

        $proposals = [];

        while (($row = fgetcsv($handle)) != FALSE)
        {
            $proposal = new stdClass();

            foreach ($header as $i => $header_i)
            {
                if ($header_i != '')
                {
                    $fieldName = str_replace(' ', '', $header_i);
                    $row_data[$fieldName] = $row[$i];
                }
            }

            foreach ($fieldList as $fieldName)
            {
                $fieldName = str_replace(' ', '', $fieldName);
                $proposal->$fieldName = FixString($row_data[$fieldName]);
            }

            $otherPeople = [];
            for ($i = 1; $i < 3; ++$i)
            {
                $legalName = $row_data["LegalName$i"];
                if (strlen($legalName) > 0)
                {
                    $person = new stdClass();
                    $person->LegalName = $legalName;
                    $person->ProgramName = FixString($row_data["ProgramName$i"]);
                    $person->Bio = FixString($row_data["Bio$i"]);
                    array_push($otherPeople, $person);
                }
            }
            $proposal->OtherPeople = $otherPeople;

            $presentations = [];
            for ($i = 1; $i < 4; ++$i)
            {
                $title = $row_data["Title$i"];
                if (strlen($title) > 0)
                {
                    $presentation = new stdClass();
                    foreach ($presentationFieldList as $fieldName)
                    {
                        $fieldName = str_replace(' ', '', $fieldName);
                        $presentation->$fieldName = FixString($row_data["$fieldName$i"]);
                    }
                    array_push($presentations, $presentation);
                }
            }
            $proposal->Presentations = $presentations;


            array_push($proposals, $proposal);
        }

        $data->status = 'SUCCESS';
        $data->info = 'Info';
        $data->proposals = $proposals;
    } else
    {
        $data->status = 'FAILURE';
        $data->msg = "Could not open file: $dataFile";
    }

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

$dataFile = "data/ROS_2016_proposal.csv";
LoadData($dataFile);

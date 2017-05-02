<?php
include_once "../common/OpenDb.php";

function FixString($string)
{
    //return  mb_convert_encoding(str_replace('"', '\"', $string), "UTF-8", "Windows-1252");
    return mb_convert_encoding($string, "UTF-8", "Windows-1252");
}

function LoadData()
{
?>
<html xmlns:office="urn:schemas-microsoft-com:office:office" xmlns:word="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40" >
<head>
<xml>
<word:WordDocument>
<word:View>Print</word:View>
<word:Zoom>90</word:Zoom>
<word:DoNotOptimizeForBrowser/>
</word:WordDocument>
</xml>
</head>
<body>
<?php




    $event_code = 'ROS';
    $event_year = 2017;

    $db = OpenPDO();

    $stmt = ExecuteQuery($db, "SELECT event_id FROM event WHERE event_code='$event_code' and event_year=$event_year");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $eventId = $row['event_id'];


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

    $stmt = ExecuteQuery($db, $sql);
    $proposals = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach($proposals as $proposal)
	{
		echo "<h2>" . $proposal["legal_name"] . "</h2><br>";
		echo "<h2>" . $proposal["program_name"] . "</h2><br>";
		echo "<h2>" . $proposal["email_address"] . "</h2><br>";
		echo "<hr>";
	}

	echo "</body>\n";
	echo "</html>";
}
function TraceMsg($msg)
{
	error_log( date('[ymd-His]') . ':' .$msg . "\n", 3, "trace.log"); 
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
        exit(1);
    }
    return $result;
}
LoadData();

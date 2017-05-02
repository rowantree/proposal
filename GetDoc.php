<?php
include_once "PHPWord.php";
include_once "../common/OpenDb.php";

function FixString($string)
{
    //return  mb_convert_encoding(str_replace('"', '\"', $string), "UTF-8", "Windows-1252");
    return mb_convert_encoding($string, "UTF-8", "Windows-1252");
}

function LoadData()
{

	$ch = curl_init();
    curl_setopt_array(
    	$ch, array(
    		CURLOPT_URL => "http://earthspirit.rowantree.org/proposal/GetProposalData.php",
            CURLOPT_RETURNTRANSFER => true
		)
	);
	$output = curl_exec($ch);
	curl_close($ch);

	$proposalData = json_decode($output);

	echo $proposalData->event_code;
	echo " ";
	echo $proposalData->event_year;

	foreach($proposalData->proposals as $proposal)
	{
		echo $proposal->proposal_id;
		echo $proposal->legal_name;
		echo $proposal->biography;
        echo "<br>";
	}




	$PHPWord = new PHPWord();

	$PHPWord->setDefaultFontName('Ariel');
	$PHPWord->setDefaultFontSize(12);

	// New portrait section
	$section = $PHPWord->createSection();

	// Add text elements
    /*
	$section->addText('Hello World!');
	$section->addTextBreak(2);

	$section->addText('I am inline styled.', array('name'=>'Verdana', 'color'=>'006699'));
	$section->addTextBreak(2);

	$PHPWord->addFontStyle('rStyle', array('bold'=>true, 'italic'=>true, 'size'=>16));
	$PHPWord->addParagraphStyle('pStyle', array('align'=>'center', 'spaceAfter'=>100));
	$section->addText('I am styled by two style definitions.', 'rStyle', 'pStyle');
	$section->addText('I have only a paragraph style definition.', null, 'pStyle');
    */



    $event_code = 'ROS';
    $event_year = 2017;
	$fileName = "${event_code}_${event_year}_Proposals.docx";

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
		$section->addText($proposal["legal_name"], array('size'=>14, 'style'=>'bold', 'color'=>'006699'));

		$table = $section->addTable();

		$table->addRow();
		$table->addCell(2000)->addText('Legal Name');
		$table->addCell(4000)->addText($proposal['legal_name']);

		$table->addRow();
		$table->addCell(2000)->addText('Program Name');
		$table->addCell(6000)->addText($proposal['program_name']);

		$table->addRow();
		$table->addCell(2000)->addText('Biography');
		$table->addCell(6000)->addText($proposal['biography']);



		/*
		$section->addText("Legal Name\t" . $proposal["legal_name"], array('name'=>'Verdana', 'color'=>'006699'));
		$section->addText("Program Name\t" . $proposal["program_name"], array('name'=>'Verdana', 'color'=>'006699'));
		$section->addText("Biography\t" . $proposal["biography"], array('name'=>'Verdana', 'color'=>'006699'));
		$section->addTextBreak();
		*/


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
        pd.schedule_time
    FROM proposal_detail pd
    INNER JOIN proposal p ON pd.proposal_id = p.proposal_id
    WHERE p.proposal_id = " . $proposal['proposal_id']
    ;
		// pd.equipment


		$stmt = ExecuteQuery($db, $sql);
		$details = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($details as $detail )
        {
			$section->addText($detail["title"], array('color'=>'006699'));

			$table = $section->addTable();

			$table->addRow();
			$table->addCell(2000)->addText('Title');
			$table->addCell(4000)->addText($detail['title']);

			$table->addRow();
			$table->addCell(2000)->addText('Description');
			$table->addCell(6000)->addText($detail['presentation']);
		}



		$section->addPageBreak();
	}


	// Save File
	$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
	$objWriter->save($fileName);

?>
<html>
<head>
<title>Proposal Document Download</title>
</head>
<body>
<?php
	echo "File <a href=\"$fileName\">$fileName</a> is ready for downloading";
?>
</body>
</html>
<?php

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

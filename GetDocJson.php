<?php
require __DIR__ . '/vendor/autoload.php';
include_once "../common/OpenDb.php";

// Creating the new document...

function xmlEntities($str)
{
    $xml = array('&#34;','&#38;','&#38;','&#60;','&#62;','&#160;','&#161;','&#162;','&#163;','&#164;','&#165;','&#166;','&#167;','&#168;','&#169;','&#170;','&#171;','&#172;','&#173;','&#174;','&#175;','&#176;','&#177;','&#178;','&#179;','&#180;','&#181;','&#182;','&#183;','&#184;','&#185;','&#186;','&#187;','&#188;','&#189;','&#190;','&#191;','&#192;','&#193;','&#194;','&#195;','&#196;','&#197;','&#198;','&#199;','&#200;','&#201;','&#202;','&#203;','&#204;','&#205;','&#206;','&#207;','&#208;','&#209;','&#210;','&#211;','&#212;','&#213;','&#214;','&#215;','&#216;','&#217;','&#218;','&#219;','&#220;','&#221;','&#222;','&#223;','&#224;','&#225;','&#226;','&#227;','&#228;','&#229;','&#230;','&#231;','&#232;','&#233;','&#234;','&#235;','&#236;','&#237;','&#238;','&#239;','&#240;','&#241;','&#242;','&#243;','&#244;','&#245;','&#246;','&#247;','&#248;','&#249;','&#250;','&#251;','&#252;','&#253;','&#254;','&#255;');
    $html = array('&quot;','&amp;','&amp;','&lt;','&gt;','&nbsp;','&iexcl;','&cent;','&pound;','&curren;','&yen;','&brvbar;','&sect;','&uml;','&copy;','&ordf;','&laquo;','&not;','&shy;','&reg;','&macr;','&deg;','&plusmn;','&sup2;','&sup3;','&acute;','&micro;','&para;','&middot;','&cedil;','&sup1;','&ordm;','&raquo;','&frac14;','&frac12;','&frac34;','&iquest;','&Agrave;','&Aacute;','&Acirc;','&Atilde;','&Auml;','&Aring;','&AElig;','&Ccedil;','&Egrave;','&Eacute;','&Ecirc;','&Euml;','&Igrave;','&Iacute;','&Icirc;','&Iuml;','&ETH;','&Ntilde;','&Ograve;','&Oacute;','&Ocirc;','&Otilde;','&Ouml;','&times;','&Oslash;','&Ugrave;','&Uacute;','&Ucirc;','&Uuml;','&Yacute;','&THORN;','&szlig;','&agrave;','&aacute;','&acirc;','&atilde;','&auml;','&aring;','&aelig;','&ccedil;','&egrave;','&eacute;','&ecirc;','&euml;','&igrave;','&iacute;','&icirc;','&iuml;','&eth;','&ntilde;','&ograve;','&oacute;','&ocirc;','&otilde;','&ouml;','&divide;','&oslash;','&ugrave;','&uacute;','&ucirc;','&uuml;','&yacute;','&thorn;','&yuml;');
    $str = str_replace($html,$xml,$str);
    $str = str_ireplace($html,$xml,$str);
    return $str;
}
function FixString($string)
{
    //return  mb_convert_encoding(str_replace('"', '\"', $string), "UTF-8", "Windows-1252");
    //$string = mb_convert_encoding($string, "UTF-8", "Windows-1252");


    /*
    echo "Start FixString: $string<br>\n";
    $string = xmlEntities($string);
    echo "$string<br>\n";
    $string = htmlentities($string);
    echo "$string<br>\n";
    $string = str_replace("&rsquo;", "'", $string);
    echo "$string<br>\n";
    $string = str_replace("&lsquo;", "'", $string);
    echo "$string<br>\n";
    $string = str_replace("&rdquo;", '"', $string);
    echo "$string<br>\n";
    $string = str_replace("&ldquo;", '"', $string);
    echo "$string<br>\n";
    $string = str_replace("&quot;", "'", $string);
    echo "$string<br>\n";
    $string = str_replace("&hellip;", '...', $string);
    echo "$string<br>\n";
    $string = str_replace("&aacute;", "a", $string);
    echo "Done FixString: $string<br>\n";
    */
    return $string;
}

function Other($main, $other)
{
    return $main=='Other' ? $other : $main;
}

function AddTableRow($table, $label, $text)
{
    $table->addRow();
    $table->addCell(1500)->addText($label, null, 'tableText');
    $table->addCell(8000)->addText(FixString($text), null, 'tableText');
}
function AddDoubleTableRow($table, $label, $text, $label2, $text2)
{
    $table->addRow();
    $table->addCell(1500)->addText($label, 'TableHdrStyle', 'tableText');
    $table->addCell(3250)->addText(FixString($text), null, 'tableText');
    $table->addCell(1500)->addText($label2, 'TableHdrStyle', 'tableText');
    $table->addCell(3250)->addText(FixString($text2), null, 'tableText');
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

    $event_code = $proposalData->event_code;
    $event_year = $proposalData->event_year;


    \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
    $PHPWord = new \PhpOffice\PhpWord\PhpWord();

    $PHPWord->setDefaultFontName('Ariel');
    $PHPWord->setDefaultFontSize(12);

    // Tabs Limit
    $PHPWord->addParagraphStyle( 'titleTab',
        array( 'tabs' => array( new \PhpOffice\PhpWord\Style\Tab('right', 9000) ) ) );

	$PHPWord->addParagraphStyle( 'tableText',
		array( 'spaceBefore'=> 0, 'spaceAfter'=>0, 'lineHeight'=>1 ));



    $peopleTitleStyle = array(
		'size' => 16,
		'bold' => true,
        'color' => '0040ff'
	);

    $titleStyle = array(
        'size' => 16,
		'color' => '751aff',
        'bold' => true
    );
	//'color' => '00334D'

	$tableHdrStyle = array (
        'bold' => true
	);
    $redFontStyle = array (
            'size' => 34,
        'color' => 'e60000'
    );
    $PHPWord->addFontStyle("redFontStyle", $redFontStyle);

	$PHPWord->addFontStyle('TableHdrStyle', $tableHdrStyle);


	$styleTable = array('borderSize' => 9, 'borderColor' => '00334D');
	$PHPWord->addTableStyle('Table Style', $styleTable);

    // New portrait section
    $section = $PHPWord->addSection();

    $fileName = "${event_code}_${event_year}_Proposals.docx";

    $count = 0;
    foreach($proposalData->proposals as $proposal)
    {
        //if ( ++$count > 5 ) break;

        $section->addText($proposal->legal_name . "\t" . $proposal->program_name, $peopleTitleStyle, 'titleTab');

        $section->addText(FixString($proposal->biography));

        //$table = $section->addTable('Table Style');
        //AddTableRow($table, 'Biography', $proposal->biography);
        //AddTableRow($table, 'Unavailable', $proposal->unavailable_times);
        $table = $section->addTable('Table Style');
		AddDoubleTableRow($table,'Unavailable:' , FixString($proposal->unavailable_times), '', '');
        AddDoubleTableRow($table, 'Arriving', $proposal->when_arriving, 'Last Attended', $proposal->last_attended);
        AddDoubleTableRow($table, 'Email', $proposal->email_address, 'Phone', $proposal->telephone_number);

        if (property_exists($proposal, "otherPeople"))
        {
            foreach ($proposal->otherPeople as $people)
            {
                $section->addText($people->legal_name . "\t" . $people->program_name, $peopleTitleStyle, 'titleTab');
                $section->addText(FixString($people->bio));
            }
        }


        foreach($proposal->presentations as $detail)
        {
            $section->addText(
                FixString($detail->title) . "\t" . Other($detail->presentation_type, $detail->presentation_type_other),
                $titleStyle, "titleTab");

            $section->addText($detail->presentation);
            $table = $section->addTable('Table Style');
            AddDoubleTableRow($table, 'Audience', $detail->target_audience, 'Age', Other($detail->age, $detail->age_other));
            AddDoubleTableRow($table, 'Time Pref.', Other($detail->time_preference, $detail->time_preference_other), 'Space Pref.', Other($detail->space_preference, $detail->space_preference_other));
            AddDoubleTableRow($table, 'Participants', $detail->participant_limit, 'Fee Limit', $detail->fee);
            AddDoubleTableRow($table, 'Equipment', $detail->equipment, 'Id Number', '#' . $detail->proposal_detail_id);
        }
        $section->addPageBreak();
    }


    // Save File
    echo "Call IOFactory<br>";
    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($PHPWord, 'Word2007');
    echo "Save the data to $fileName<br>";
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

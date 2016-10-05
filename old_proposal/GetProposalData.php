<?php
/**
 * Created by PhpStorm.
 * User: smorley
 * Date: 2016-09-21
 * Time: 13:57
 */

function FixString($string)
{
    //return  mb_convert_encoding(str_replace('"', '\"', $string), "UTF-8", "Windows-1252");
    return mb_convert_encoding($string, "UTF-8", "Windows-1252");
}

function LoadData($dataFile)
{
    $data = new stdClass();

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

$dataFile = "data/ROS_2016_proposal.csv";
@LoadData($dataFile);

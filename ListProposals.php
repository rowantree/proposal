<?php
include_once "../common/OpenDb.php";

    $event_code = 'FOL';
    $event_year = 2017;

    TraceMsg("ListProposal $event_code $event_year");

    $db = OpenPDO();


    $sql = "SELECT
        pd.proposal_detail_id,
        pd.title,
        p.legal_name
    FROM proposal_detail pd
    INNER JOIN proposal p ON pd.proposal_id = p.proposal_id
";
    $stmt = ExecuteQuery($db, $sql);
    $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($details as $detail)
    {
        echo "#", $detail['proposal_detail_id'], " ", $detail['title'], " [", $detail['legal_name'],  "]<br>";
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
        echo var_export($db->errorInfo(), TRUE);
        exit(1);
    }
    return $result;
}

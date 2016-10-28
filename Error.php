<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

if(error_log("This is a test."))
{
    echo "Error logged successfully to: " . ini_get('error_log') . "<br>";
}



echo("I reported an error");
?>

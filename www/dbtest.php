<pre>
<?php

require_once "nanodb.php";

$db = new nanoDb("data/nanodb.json", 10);

$record = Array();
$record[date("Y-m-d")] = Array(Array("name", "Fungi"), Array("locality", "Latokaski"));

$db->addRecord($record);

$allDataArr = $db->getAll();
krsort($allDataArr);
print_r($allDataArr);

echo "\n\nEND";
<pre>
<?php

require_once "nanodb.php";

$db = new nanoDb("data/nanodb.json", 10);

$record = Array(Array("name", "Fungi"), Array("locality", "Latokaski"));
$id = date("Y-m-d");

$db->addRecord($id, $record);

$allDataArr = $db->getAll();
krsort($allDataArr);
print_r($allDataArr);

echo "\n\nEND";
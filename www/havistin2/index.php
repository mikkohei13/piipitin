<pre>
<?php

require_once "log2_SLAVE.php";
require_once "finbif.php";
require_once "_secrets.php";

log2("START", "-------------------------------------------------------", "logs/havistin.log");

$personToken = $_GET['personToken'];

$fin = new finbif(API_TOKEN, $personToken);

//$fin->test();

$fin->myDocuments("1990");


log2("NOTICE", "mgs", "logs/havistin.log");

echo "END";


<?php

require_once "log2_SLAVE.php";
require_once "finbif.php";
require_once "_secrets.php";

$personToken = $_GET['personToken'];

$fin = new finbif(API_TOKEN, $personToken);
$fin->test();

log2("NOTICE", "mgs", "logs/havistin.log");

echo "END";


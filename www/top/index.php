<pre>
<?php
require_once "config/env.php";
require_once "logger.php";
require_once "lajifi.php";

foreach ($names as $i => $name) {
  $nameUrlencoded = urlencode($name);
  $url = "https://api.laji.fi/v0/warehouse/query/aggregate?aggregateBy=unit.linkings.taxon.scientificName&geoJSON=false&onlyCount=true&pairCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=100&page=1&cache=true&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&taxonRankId=MX.species&countryId=ML.206&time=2019&individualCountMin=1&teamMember=" . $nameUrlencoded . "&qualityIssues=NO_ISSUES&access_token=" . LAJIFI_TOKEN;

  $json = getDataFromLajifi($url);
  $arr = json_decode($json, TRUE);
  echo $name . ": " . $arr['total'] . "\n";
}


echo "\n\nEND";

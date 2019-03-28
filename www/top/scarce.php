<h1>Uusia lajeja Suomesta</h1>
<p></p>
<hr>
<pre>
<?php

//$namesObservations = Array();

$url = "https://api.laji.fi/v0/warehouse/query/aggregate?aggregateBy=unit.linkings.originalTaxon.id&orderBy=count&geoJSON=false&onlyCount=true&pairCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=100&page=1&cache=false&taxonId=MX.53695&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&taxonRankId=MX.species&countryId=ML.206&individualCountMin=1&qualityIssues=NO_ISSUES&access_token=" . LAJIFI_TOKEN;

$speciesJson = getDataFromLajifi($url);
$speciesArr = json_decode($speciesJson, TRUE);

$threshold = 1;
$limit = 20;

$limit = 2; // debug

foreach ($speciesArr['results'] as $i => $species) {
  if ($species['count'] > $threshold) {
    break;
  }
  if ($i >= $limit) {
    break;
  }

  $taxonId = $species['aggregateBy']['unit.linkings.originalTaxon.id'];
  $taxonData = getTaxonData($taxonId);
  $obs = getObservationUnitId($taxonId);

  // debug:
  echo $taxonData['family'] . ": <a href='" . $taxonId . "'>" . $taxonData['species'] . "</a> (" . $taxonData['speciesVernacular'] . ") <a href='" . $obs['unit']['unitId'] . "'>OBS</a>\n";
  echo $obs['gathering']['displayDateTime'] . "\n\n";
}

print_r ($speciesArr);

//arsort($namesObservations);

//echoTable($namesObservations);



function getObservationUnitId($taxonId) {
  /*
 https://api.laji.fi/v0/warehouse/query/list?pageSize=10&page=1&cache=false&taxonId=MX.204861&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&countryId=ML.206&individualCountMin=1&qualityIssues=NO_ISSUES&access_token=
  */

  $taxonQname = str_replace("http://tun.fi/", "", $taxonId);

  $url = "https://api.laji.fi/v0/warehouse/query/list?pageSize=10&page=1&cache=false&taxonId=" . $taxonQname . "&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&countryId=ML.206&individualCountMin=1&qualityIssues=NO_ISSUES&access_token=" . LAJIFI_TOKEN;
  $obsJson = getDataFromLajifi($url);
  $obsArr = json_decode($obsJson, TRUE);

  // Todo: check that really only 1 result

  return $obsArr['results'][0];
}

function getTaxonData($taxonId) {
  /*
  https://api.laji.fi/v0/taxa/MX.204861?langFallback=true&maxLevel=0&includeHidden=false&includeMedia=false&includeDescriptions=false&includeRedListEvaluations=false&sortOrder=taxonomic&access_token=
  */

  $taxonQname = str_replace("http://tun.fi/", "", $taxonId);

  $url = "https://api.laji.fi/v0/taxa/" . $taxonQname . "?langFallback=true&maxLevel=0&includeHidden=false&includeMedia=false&includeDescriptions=false&includeRedListEvaluations=false&sortOrder=taxonomic&access_token=" . LAJIFI_TOKEN;
  $taxonJson = getDataFromLajifi($url);
  $taxonArr = json_decode($taxonJson, TRUE);

  $taxonData = Array();
  @$taxonData['speciesVernacular'] = $taxonArr['vernacularName'];
  @$taxonData['species'] = $taxonArr['scientificName'];
  @$taxonData['family'] = $taxonArr['parent']['family']['scientificName'];

  return $taxonData;
}


?>

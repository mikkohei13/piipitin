<h1>Uusia lajeja Suomesta</h1>
<p></p>
<hr>
<?php

//$namesObservations = Array();

$url = "https://api.laji.fi/v0/warehouse/query/aggregate?aggregateBy=unit.linkings.originalTaxon.id&orderBy=count&geoJSON=false&onlyCount=true&pairCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=100&page=1&cache=false&taxonId=MX.53695&useIdentificationAnnotations=false&includeSubTaxa=true&includeNonValidTaxa=false&taxonRankId=MX.species&countryId=ML.206&individualCountMin=1&qualityIssues=NO_ISSUES&access_token=" . LAJIFI_TOKEN;

$speciesJson = getDataFromLajifi($url);
$speciesArr = json_decode($speciesJson, TRUE);

$threshold = 1;
$limit = 20;

$limit = 100; // debug

$obsArray = Array();

// Build observations with related data into an array
foreach ($speciesArr['results'] as $i => $species) {
  if ($species['count'] > $threshold) {
    break;
  }
  if ($i >= $limit) {
    break;
  }

  $taxonId = $species['aggregateBy']['unit.linkings.originalTaxon.id'];
  $taxonData = getTaxonData($taxonId);
  $obs = getObservationUnit($taxonId);
  $combination = Array();

  $combination['taxonId'] = $taxonId;
  $combination['taxon'] = $taxonData;
  $combination['obs'] = $obs;

//  print_r ($combination); // debug

  $obsArray[] = $combination; // obsArr = ?

}

// Sort by date
usort($obsArray, function($b, $a) {
  $retval = $a['obs']['gathering']['eventDate']['begin'] <=> $b['obs']['gathering']['eventDate']['begin'];
  return $retval;
});
  
// Echo each obs
foreach ($obsArray as $a => $obs) {
  echo "<p><strong>";
  // debug:
  echo $obs['taxon']['family'] . ": <a href='" . $obs['taxonId'] . "'><em>" . $obs['taxon']['species'] . "</em></a> (" . $obs['taxon']['speciesVernacular'] . ") <a href='" . $obs['obs']['unit']['unitId'] . "'>OBS</a></strong><br>\n";
  echo $obs['obs']['gathering']['displayDateTime'] . "<br>\n";
  echo $obs['obs']['gathering']['province'] . " " . $obs['obs']['gathering']['municipality'] . " " . $obs['obs']['gathering']['locality'] . "<br>\n";
  echo $obs['obs']['teamString'] . "<br>\n";
  echo "</p>\n\n";
}

//print_r ($speciesArr);

//arsort($namesObservations);

//echoTable($namesObservations);



function getObservationUnit($taxonId) {

  $taxonQname = str_replace("http://tun.fi/", "", $taxonId);

  $url = "https://api.laji.fi/v0/warehouse/query/list?selected=unit.unitId%2Cgathering.country%2Cgathering.displayDateTime%2Cgathering.eventDate.begin%2Cgathering.eventDate.end%2Cgathering.locality%2Cgathering.municipality%2Cgathering.province%2Cgathering.team%2Cunit.abundanceString%2Cunit.annotationCount%2Cunit.linkings.originalTaxon.finnish%2Cunit.linkings.originalTaxon.id%2Cunit.linkings.originalTaxon.scientificName&pageSize=2&page=1&cache=false&taxonId=" . $taxonQname . "&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&countryId=ML.206&individualCountMin=1&qualityIssues=NO_ISSUES&access_token=" . LAJIFI_TOKEN;
  $obsJson = getDataFromLajifi($url);
  $obsArr = json_decode($obsJson, TRUE);

  // Todo: check that really only 1 result

  $ret = $obsArr['results'][0];

  if (!isset($ret['gathering']['province'])) {
    $ret['gathering']['province'] = "";
  }
  if (!isset($ret['gathering']['municipality'])) {
    $ret['gathering']['municipality'] = "";
  }
  if (!isset($ret['gathering']['locality'])) {
    $ret['gathering']['locality'] = "";
  }

  // Todo: with a function
  $teamString = "";
  foreach ($ret['gathering']['team'] as $i => $person) {
    $teamString .= $person . ", ";
  }
  $ret['teamString'] = trim($teamString, ", ");

//  print_r($ret);

  return $ret;
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

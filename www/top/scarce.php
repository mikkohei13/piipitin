<?php

//$namesObservations = Array();

/*
$taxonQname = "MX.53695"; $title = "Perhoset";
$taxonQname = "MX.43121"; $title = "Kovakuoriaiset";
$taxonQname = "MX.53066"; $title = "Kantasienet";
$taxonQname = "MX.37612"; $title = "Nisäkkäät";
$taxonQname = "MX.53078"; $title = "Putkilokasvit";
$taxonQname = "MX.43122"; $title = "Pistiäiset";
$taxonQname = "MX.229577"; $title = "Luteet";
$taxonQname = "MX.37580"; $title = "Linnut";
$taxonQname = "MX.1"; $title = "Sudenkorennot";

*/
$taxonQname = "MX.37580"; $title = "Linnut";





echo "<h1>Kerran Suomesta kirjatut: $title</h1><p>Tämä luettelo näyttää lajit, joista on Lajitietokeskuksessa vain yksi havainto Suomesta. <strong>PROTOTYYPPI, päivitetty " . date("d.m.Y") . "</strong></p><hr>";

// Get aggregate
$url = "https://api.laji.fi/v0/warehouse/query/aggregate?aggregateBy=unit.linkings.originalTaxon.id&orderBy=count&geoJSON=false&onlyCount=true&pairCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=100&page=1&cache=false&taxonId=" . $taxonQname . "&useIdentificationAnnotations=false&includeSubTaxa=true&includeNonValidTaxa=false&taxonRankId=MX.species&countryId=ML.206&individualCountMin=1&qualityIssues=NO_ISSUES&access_token=" . LAJIFI_TOKEN;

$speciesJson = getDataFromLajifi($url);
$speciesArr = json_decode($speciesJson, TRUE);

$threshold = 1;
$limit = 20;

$limit = 10; // debug

$obsArray = Array();

// Build observations with related data into an array
// Pick those species that only have one observation
foreach ($speciesArr['results'] as $i => $species) {
  if ($species['count'] > $threshold) {
    break;
  }
  if ($i >= $limit) {
    break;
  }

  // Fill in taxon data
  $taxonId = $species['aggregateBy']['unit.linkings.originalTaxon.id'];
  $taxonData = getTaxonData($taxonId);

  // Get the observation by taxon name & country
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
  echo $obs['taxon']['family'] . ": <a href='" . $obs['taxonId'] . "'><em>" . $obs['taxon']['species'] . "</em></a> (" . $obs['taxon']['speciesVernacular'] . ") <a href='" . $obs['obs']['document']['documentId'] . "'>OBS</a></strong><br>\n";
  echo $obs['obs']['gathering']['displayDateTime'] . ", Luonnonvaraisuus: " . $obs['obs']['unit']['nativeOccurrence'] . "<br>\n";
  echo $obs['obs']['gathering']['province'] . " " . $obs['obs']['gathering']['municipality'] . " " . $obs['obs']['gathering']['locality'] . "<br>\n";
  echo $obs['obs']['teamString'] . "<br>\n";
  echo "</p>\n\n";
}

//print_r ($speciesArr);

//arsort($namesObservations);

//echoTable($namesObservations);



function getObservationUnit($taxonId) {

  $taxonQname = str_replace("http://tun.fi/", "", $taxonId);

  $url = "https://api.laji.fi/v0/warehouse/query/list?selected=unit.nativeOccurrence%2Cdocument.documentId%2Cunit.unitId%2Cgathering.country%2Cgathering.displayDateTime%2Cgathering.eventDate.begin%2Cgathering.eventDate.end%2Cgathering.locality%2Cgathering.municipality%2Cgathering.province%2Cgathering.team%2Cunit.abundanceString%2Cunit.annotationCount%2Cunit.linkings.originalTaxon.finnish%2Cunit.linkings.originalTaxon.id%2Cunit.linkings.originalTaxon.scientificName&pageSize=2&page=1&cache=false&taxonId=" . $taxonQname . "&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&countryId=ML.206&individualCountMin=1&qualityIssues=NO_ISSUES&access_token=" . LAJIFI_TOKEN;
  $obsJson = getDataFromLajifi($url);
  $obsArr = json_decode($obsJson, TRUE);

  // Todo: check that really only 1 result

  $ret = $obsArr['results'][0];

  // Note: Especially old observations often lack info, e.g. locality names, date or collector.
  // Fill in here if missing

  if (!isset($ret['unit']['nativeOccurrence'])) {
    $ret['unit']['nativeOccurrence'] = "not selected";
  }

  // Locality names
  if (!isset($ret['gathering']['province'])) {
    $ret['gathering']['province'] = "";
  }
  if (!isset($ret['gathering']['municipality'])) {
    $ret['gathering']['municipality'] = "";
  }
  if (!isset($ret['gathering']['locality'])) {
    $ret['gathering']['locality'] = "";
  }

  // Date
  if (!isset($ret['gathering']['eventDate']['begin'])) {
    $ret['gathering']['eventDate']['begin'] = "0000-00-00";
    $ret['gathering']['displayDateTime'] = "0000-00-00";
  }
/*  if (!isset($ret['gathering']['displayDateTime'])) {
    $ret['gathering']['displayDateTime'] = "0000-00-00";
  }
*/


  // Team
  // Todo: with a function
  $teamString = "";
  if (!isset($ret['gathering']['team'])) {
    $ret['gathering']['team'][0] = "tuntematon";
  }
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

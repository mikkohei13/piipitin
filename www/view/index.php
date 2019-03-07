<h1>Viimeksi tallennetut "mielenkiintoiset" havainnot Lajitietokeskuksesta (DEMO)</h2>

<?php
/*
- sort descending
- limit 50
- show points & details etc.
*/

require_once "../config/env.php";


$dataJson = file_get_contents("../data/" . DATAFILE_RARITIES);
$dataArr = json_decode($dataJson, TRUE);

$observationCount = 0;

foreach ($dataArr as $element) {
  echo "<p>\n";
  echo $element['rarityScore']['total'] . " pistettä<br>\n";

  echo "<strong>" . @$element['unit']['linkings']['taxon']['vernacularName']['fi'] . " (<em>";
  echo @$element['unit']['linkings']['taxon']['scientificName'] . "</em>)</strong><br>\n";
  echo @$element['gathering']['displayDateTime'] . "<br>\n";
  echo @$element['gathering']['biogeographicalProvince'] . ", ";
  echo @$element['gathering']['municipality'] . ", ";
  echo @$element['gathering']['locality'] . "<br>\n";
 
  if (isset($element['gathering']['team'])) {
    foreach ($element['gathering']['team'] as $i => $name) {
      echo $name . ", ";
    }
  }
  echo "<br>\n";
  echo "Havaintoja: " . $element['rarityScore']['desc'] . "<br>\n";

  echo "<a href=\"" . $element['document']['documentId'] . "\">" . $element['document']['documentId'] . "</a>\n";

  echo "<p>\n\n";

  $observationCount++;
}

echo "<p>Yhteensä $observationCount havaintoa</p>";
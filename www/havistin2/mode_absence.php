<h2>Paikan puutelista</h2>

<p>Havainnot annetuista koordinaateista +- 1 aste leveyspiiriä, +- 0,5 astetta pituuspiiriä (Etelä-Suomessa n. 100x100 km2, Pohjois-Suomessa n. 80x100 km2), vuodesta 2000 alkaen. Ne lajit, jotka on havaittu *kokonaan* annettujen koordinaattien sisällä +- 0,25 astetta leveyspiiriä, +- 0,125 astetta pituuspiiriä (vastaavasti n. 30x30 km2 ja 20x30 km2) on yliviivattu. (Karkeistetut ja sensitiiviset havainnot eivät näy tilastossa oikein.)</p>

<?php

// Get data
$aggregateRank = getAggregateRank();
$taxonId = getTaxonId();

$set = $fin->allSpecies($aggregateRank, $taxonId, 0.5, 1, false);
$subset = $fin->allSpecies($aggregateRank, $taxonId, 0.125, 0.25, false);

// $fin->debug($subset); // debug

// Create array of my species
/*
Array format:
  Array
  (
      [http://tun.fi/MX.70582] => 1
  ...
*/
$subsetArr = Array();
foreach($subset['results'] as $nro => $taxonArr) {
  $key = key($taxonArr['aggregateBy']);
  $subsetArr[$taxonArr['aggregateBy'][$key]] = true;
}

// $fin->debug($subsetArr); // debug

// Print out a table of all observations
$i = 1;
echo "<table>";
foreach($set['results'] as $nro => $taxon) {
  $key = key($taxon['aggregateBy']);
  $taxonId = $taxon['aggregateBy'][$key];
  $taxonName = $fin->getTaxonName($taxonId);

  $class = "non-observed";
  if (isset($subsetArr[$taxonId])) {
    $class = "observed";
  }
//      print_r ($taxon);

  echo "<tr class=\"$class\">";
  echo "<td>";
  echo $i;
  echo "</td><td>";
  echo $taxonName;
  echo "</td><td>";
  echo $taxonId;
  echo "</td><td>";

  echo $taxon['count'];
  echo " havaintoa</td>";
  echo "</tr>\n";

  $i++;
}
echo "</table>";



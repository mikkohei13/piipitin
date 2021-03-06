<h2>Paikan puutelista</h2>

<p>Havaintojen määrä annetuista koordinaateista +- 1 aste leveyspiiriä, +- 0,5 astetta pituuspiiriä (Etelä-Suomessa n. 100x100 km2, Pohjois-Suomessa n. 80x100 km2), vuodesta 2000 alkaen. Ne lajit, jotka on havaittu *kokonaan* annettujen koordinaattien sisällä +- 0,25 astetta leveyspiiriä, +- 0,125 astetta pituuspiiriä (vastaavasti n. 30x30 km2 ja 20x30 km2) on yliviivattu.</p>
<p>Huom: Karkeistetut ja sensitiiviset havainnot eivät näy tilastossa oikein. Se ei myöskään toimi oikein, jos haettu taksoni sisältää haetulta alueelta yli 10.000 lajia - älä siis hae puutelistaa esim. kaikista eläimistä kerralla!</p>

<?php

// Get data
$aggregateRank = getAggregateRank();
$taxonId = getTaxonId();

$multiplier = getMultiplier();

$baseSetLatDelta = 0.5;
$baseSetLonDelta = 1;
$baseSubsetLatDelta = 0.125;
$baseSubsetLonDelta = 0.25;

$set = $fin->allSpecies($aggregateRank, $taxonId, $baseSetLatDelta * $multiplier, $baseSetLonDelta * $multiplier, false);
$subset = $fin->allSpecies($aggregateRank, $taxonId, $baseSubsetLatDelta * $multiplier, $baseSubsetLonDelta * $multiplier, false);

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
  echo "<td class=\"i\">";
  if ("non-observed" == $class) {
    echo "&diams;";
  }
  echo "</td><td>";
  echo $i;
  echo "</td><td>";
  echo "<a href=\"$taxonId\">$taxonName</a>";
  echo "</td><td>";

  echo $taxon['count'];
  echo "</td>";
  echo "</tr>\n";

  $i++;
}
echo "</table>";



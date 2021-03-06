<h2>Oma puutelista</h2>

<p>Havainnot annetuista koordinaateista +- 1 aste leveyspiiriä, +- 0,5 astetta pituuspiiriä, vuodenaikana tämä päivä +- 60 päivää, vuodesta 2000 alkaen. Lajit, jotka olet itse havainnut joskus Suomesta yliviivattuina.  
</p>

<?php

// TODO: filter out nonwilds

// Get data
$aggregateRank = getAggregateRank();
$taxonId = getTaxonId();

$set = $fin->allSpecies($aggregateRank, $taxonId);
$subset = $fin->mySpecies($aggregateRank, $taxonId);

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



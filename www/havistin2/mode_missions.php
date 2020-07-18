<h2>Oma puutelista</h2>
<?php

// TODO: filter out nonwilds

$set = $fin->allSpecies();
$subset = $fin->mySpecies();

//    $fin->debug($subset);

$subsetArr = Array();
foreach($subset['results'] as $nro => $taxonArr) {
  $key = key($taxonArr['aggregateBy']);
  $subsetArr[$taxonArr['aggregateBy'][$key]] = true;
}

//    $fin->debug($subsetArr);

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
  echo "</td>";
  echo "</tr>\n";

  $i++;
}
echo "</table>";



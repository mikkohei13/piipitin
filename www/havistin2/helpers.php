<?php

// todo: move to function, use also on mydocuments.php
if (isset($_GET['personToken'])) {
  if (ctype_alnum($_GET['personToken'])) {
    $personToken = $_GET['personToken'];
  }
  else {
    log2("ERROR", "Invalid personToken", LOG_DIR."/havistin.log");
  }
}
else {
  log2("ERROR", "No personToken given", LOG_DIR."/havistin.log");
}


function getAggregateRank() {

  $rankUnsafe = $_GET['rank'];
  if ("phylum" == $rankUnsafe) {
    $rank = "unit.linkings.taxon.phylymId";
  }
  elseif ("class" == $rankUnsafe) {
    $rank = "unit.linkings.taxon.classId";
  }
  elseif ("family" == $rankUnsafe) {
    $rank = "unit.linkings.taxon.familyId";
  }
  elseif ("genus" == $rankUnsafe) {
    $rank = "unit.linkings.taxon.genusId";
  }
  elseif ("species" == $rankUnsafe) {
    $rank = "unit.linkings.taxon.speciesId";
  }
  else {
    log2("ERROR", "Unknown rank", LOG_DIR."/havistin.log");
  }

  return $rank;
}

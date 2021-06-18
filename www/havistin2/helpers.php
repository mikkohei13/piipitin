<?php

// todo: move to function, use also on mydocuments.php
function checkPersonToken() {
  if (isset($_GET['personToken'])) {
    if (ctype_alnum($_GET['personToken'])) {
      return $_GET['personToken'];
    }
    else {
      log2("ERROR", "Invalid personToken", LOG_DIR."/havistin.log");
    }
  }
  else {
    log2("ERROR", "No personToken given", LOG_DIR."/havistin.log");
  }  
}


function getAggregateRank() {

  $rankUnsafe = $_GET['rank'];
  if ("order" == $rankUnsafe) {
    $rank = "unit.linkings.taxon.orderId";
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

function getLat() {
  $lat = floatval($_GET['lat']);
  if ($lat > 90 || $lat < -90) {
    log2("ERROR", "Invalid lat", LOG_DIR."/havistin.log");
  }
  return $lat;
}

function getLon() {
  $lon = floatval($_GET['lon']);
  if ($lon > 180 || $lon < -180) {
    log2("ERROR", "Invalid lon", LOG_DIR."/havistin.log");
  }
  return $lon;
}

function getTaxonId() {
  $taxonId = $_GET['taxonId'];
  $taxonId = str_replace("MX.", "", $taxonId);
  return "MX." . intval($taxonId);
}

function getMultiplier() {
  if (!isset($_GET['multiplier'])) {
    return 1;
  }
  $multiplier = floatval($_GET['multiplier']);
  return $multiplier;
}
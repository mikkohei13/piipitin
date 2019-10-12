<?php

require_once "log2_SLAVE.php";
require_once "finbif.php";
require_once "_secrets.php";

log2("START", "-------------------------------------------------------", "logs/havistin.log");

$personToken = $_GET['personToken'];

$fin = new finbif(API_TOKEN, $personToken);

//$fin->test();

$myDocuments = $fin->myDocuments("1980");

if ("json" == $_GET['format']) {
  echoAsJson($myDocuments);
}
elseif("tsv" == $_GET['format']) {
  documentsToTabular($myDocuments);
}
else {
  exit("Unknown format, chek your params");
}


log2("NOTICE", "mgs", "logs/havistin.log");

// todo: move to helpers

function echoAsJson($arr) {
  header('Content-Type: application/json');
  echo json_encode($arr);
}

function echoAsText($arr) {
  header('Content-Type: text/plain');
  echo "<pre>";
  print_r($arr);
  echo "</p>";
}

function documentsToTabular($documents) {

  global $fin; // todo: don't use globals

  error_reporting(E_ALL ^ E_NOTICE); // todo: scope of this?

  $s = "\t"; // separator

  echo "<pre>\n";

  foreach($documents as $docNro => $doc) {
    foreach ($doc['gatherings'] as $gatNro => $gat) {
      foreach ($gat['units'] as $uniNro => $uni) {

        // Document
        echo $doc['id'] . $s;
        echo $doc['formID'] . $s;
        echo $doc['sourceID'] . $s;
        echo $doc['collectionID'] . $s;
        echo $fin->personName($doc['creator']) . $s;
        echo $doc['dateCreated'] . $s;
        echo $fin->personName($doc['editor']) . $s;
        echo $doc['dateEdited'] . $s;
        echo $doc['publicityRestrictions'] . $s;
        echo $doc['secureLevel'] . $s;

        // Gathering event

        $legString = "";
        foreach ($doc['gatheringEvent']['leg'] as $legNro => $legID) {
          $legString .= $fin->personName($legID) . "; ";
        }
        echo trim($legString, "; ") . $s;

        echo $doc['gatheringEvent']['dateBegin'] . $s;
        echo $doc['gatheringEvent']['dateEnd'] . $s;

        // Gathering
        echo $gat['locality'] . $s;
        echo $gat['biologicalProvince'] . $s;
        echo $gat['country'] . $s;
        echo $gat['municipality'] . $s;

        // geometry

        // Unit
        echo $uni['notes'] . $s;
        echo $uni['recordBasis'] . $s;
        echo $uni['areaInSquareMeters'] . $s;
        echo $uni['plantLifeStage'] . $s;
        echo $uni['taxonConfidence'] . $s;
        
        // arrs: images

        echo $uni['identifications'][0]['taxon'] . $s;

        // Data for recognized taxa
        if (isset($uni['unitFact']['autocompleteSelectedTaxonID'])) {
          $taxonId = $uni['unitFact']['autocompleteSelectedTaxonID'];
          $taxonArr = $fin->taxon($taxonId);
  
          echo $taxonId . $s;
          echo $taxonArr['scientificName'] . $s;
          echo $taxonArr['scientificNameAuthorship'] . $s;
          echo $taxonArr['vernacularName'] . $s;
          echo $taxonArr['taxonomicOrder'] . $s;
          echo $taxonArr['taxonRank'] . $s;
          echo $taxonArr['redList2019'] . $s;
          echo $taxonArr['parent']['domain']['scientificName'] . $s;
          echo $taxonArr['parent']['phylum']['scientificName'] . $s;
          echo $taxonArr['parent']['class']['scientificName'] . $s;
          echo $taxonArr['parent']['order']['scientificName'] . $s;
          echo $taxonArr['parent']['family']['scientificName'] . $s;
          echo $taxonArr['parent']['genus']['scientificName'] . $s;
  
        }


        echo "\n";
      }
    }
  }
  echo "</pre>";
}

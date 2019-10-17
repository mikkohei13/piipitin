<?php

require_once "log2_SLAVE.php";
require_once "finbif.php";
require_once "_secrets.php";

log2("START", "Start mydocuments", "logs/havistin.log");

session_start();
$personToken = $_SESSION['personToken'];

$format = safeAlnum($_GET['format']);
$year = safeAlnum($_GET['year']);

$fin = new finbif(API_TOKEN, $personToken);

//$fin->test();

$myDocuments = $fin->myDocuments($year);

if ("json" == $format) {
  echoAsJson($myDocuments);
}
elseif("tsv" == $format) {
  documentsToTabular($myDocuments, $year);
}
else {
  log2("ERROR", "Unknown format, check the params", "logs/havistin.log");
}

// todo: move to helpers??

function echoAsJson($arr) {
  log2("NOTICE", "Generating json", "logs/havistin.log");

  header('Content-Type: application/json');
  echo json_encode($arr);
}

function echoAsText($arr) {
  log2("NOTICE", "Generating text (debug)", "logs/havistin.log");

  header('Content-Type: text/plain');
  echo "<pre>";
  print_r($arr);
  echo "</p>";
}

function documentsToTabular($documents, $key) {
  log2("NOTICE", "Generating tabular", "logs/havistin.log");

  $filename = "vihko-data-" . $key . "-" . date("Ymd-His") . ".csv";
  global $fin; // todo: don't use globals
  $unitCount = 0;

  error_reporting(E_ALL ^ E_NOTICE); // todo: scope of this?

  $s = "\t"; // column separator
  $arrSeparator = "; "; // cell-contents separator

  header("Content-type: text/csv");
  header("Content-Disposition: attachment; filename=" . $filename);
  header("Pragma: no-cache");
  header("Expires: 0");
  echoTabularHeader($s);

  foreach($documents as $docNro => $doc) {
    foreach ($doc['gatherings'] as $gatNro => $gat) {
      foreach ($gat['units'] as $uniNro => $uni) {

        // Id's
        echo $doc['id'] . $s;
        echo $gat['id'] . $s;
        echo $uni['id'] . $s;

        // Gathering event & document
        // Names cannot be handled with standard join, since need to convert id's to names
        $legString = "";
        foreach ($doc['gatheringEvent']['leg'] as $legNro => $legID) {
          $legString .= $fin->personName($legID) . "; ";
        }
        echo trim($legString, "; ") . $s;        

        echo $doc['publicityRestrictions'] . $s;
        echo $doc['secureLevel'] . $s;

        echo $doc['gatheringEvent']['dateBegin'] . $s;
        echo $doc['gatheringEvent']['dateEnd'] . $s;

        echo joinIfExists($arrSeparator, $doc['keywords']) . $s;

        // Gathering
        echo $gat['country'] . $s;
        echo $gat['biologicalProvince'] . $s;
        echo $gat['administrativeProvince'] . $s;
        echo $gat['locality'] . $s;
        echo $gat['municipality'] . $s;
        echo $gat['locality'] . $s;
        echo $gat['localityDescription'] . $s;
        echo joinIfExists($arrSeparator, $doc['habitat']) . $s;
        echo $gat['habitatDescription'] . $s;
        echo $gat['weather'] . $s;
        echo $gat['notes'] . $s;

        // Geometry
        
        $geoJson = json_encode($gat['geometry']);
        echo $geoJson . $s; // ABBA new

        echo $gat['geometry']['geometries'][0]['coordinateVerbatim'] . $s; // ABBA new
        echo $gat['coordinateRadius'] . $s;

        // Unit
        echo $uni['identifications'][0]['taxon'] . $s;

        echo $uni['count'] . $s;
        echo $uni['notes'] . $s;
        echo $uni['recordBasis'] . $s;
        echo $uni['taxonConfidence'] . $s;
        echo $uni['sex'] . $s;
        echo $uni['maleIndividualCount'] . $s;
        echo $uni['femaleIndividualCount'] . $s;
        echo $uni['lifeStage'] . $s;

        echo $uni['plantLifeStage'] . $s;
        echo $uni['plantStatusCode'] . $s;
        echo $uni['areaInSquareMeters'] . $s;
        echo $uni['hostInformalNameString'] . $s;

        echo $uni['atlasCode'] . $s;
        echo joinIfExists($arrSeparator, $uni['movingStatus']) . $s;

        echo joinIfExists($arrSeparator, $uni['additionalIDs']) . $s;
        echo joinIfExists($arrSeparator, $uni['keywords']) . $s;
        
        // todo: images

        // Document
        echo $fin->personName($doc['creator']) . $s;
        echo $doc['dateCreated'] . $s;
        echo $fin->personName($doc['editor']) . $s;
        echo $doc['dateEdited'] . $s;
        echo $doc['formID'] . $s;
        echo $doc['sourceID'] . $s;
        echo $doc['collectionID'] . $s;

        // Data for recognized taxa
        // This is printed last, because all observations don't have it
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
          echo $taxonArr['parent']['kingdom']['scientificName'] . $s;
          echo $taxonArr['parent']['phylum']['scientificName'] . $s;
          echo $taxonArr['parent']['class']['scientificName'] . $s;
          echo $taxonArr['parent']['order']['scientificName'] . $s;
          echo $taxonArr['parent']['family']['scientificName'] . $s;
          echo $taxonArr['parent']['genus']['scientificName'] . $s;
  
        }

        echo "\n";

        $unitCount++;
      }
    }
  }
  log2("NOTICE", "Handled " . $unitCount . " units", "logs/havistin.log");
}

// Helpers
// todo: move somewhere?

function echoTabularHeader($s) {

  $cols[] = "documentId"; // changed
  $cols[] = "gatheringId"; // changed
  $cols[] = "unitId"; // changed
  $cols[] = "leg";
  $cols[] = "publicityRestrictions";
  $cols[] = "secureLevel";
  $cols[] = "dateBegin";
  $cols[] = "dateEnd";
  $cols[] = "documentKeywords"; // changed
  $cols[] = "country";
  $cols[] = "biologicalProvince";
  $cols[] = "administrativeProvince";
  $cols[] = "locality";
  $cols[] = "municipality";
  $cols[] = "locality";
  $cols[] = "localityDescription";
  $cols[] = "habitat";
  $cols[] = "habitatDescription";
  $cols[] = "weather";
  $cols[] = "gatheringNotes"; // changed
  $cols[] = "geoJSON"; // changed 2
  $cols[] = "coordinateVerbatim"; // changed 2
  $cols[] = "coordinateRadius";
  $cols[] = "taxonVerbatim"; // changed
  $cols[] = "count";
  $cols[] = "unitNotes"; // changed
  $cols[] = "recordBasis";
  $cols[] = "taxonConfidence";
  $cols[] = "sex";
  $cols[] = "maleIndividualCount";
  $cols[] = "femaleIndividualCount";
  $cols[] = "lifeStage";
  $cols[] = "plantLifeStage";
  $cols[] = "plantStatusCode";
  $cols[] = "areaInSquareMeters";
  $cols[] = "hostInformalNameString";
  $cols[] = "atlasCode";
  $cols[] = "movingStatus";
  $cols[] = "additionalIDs";
  $cols[] = "unitKeywords"; // changed
  $cols[] = "creator";
  $cols[] = "dateCreated";
  $cols[] = "editor";
  $cols[] = "dateEdited";
  $cols[] = "formID";
  $cols[] = "sourceID";
  $cols[] = "collectionID";
  $cols[] = "taxonId";
  $cols[] = "scientificName";
  $cols[] = "scientificNameAuthorship";
  $cols[] = "finnishName"; // changed
  $cols[] = "taxonomicOrder";
  $cols[] = "taxonRank";
  $cols[] = "redList2019";
  $cols[] = "domain";
  $cols[] = "kingdom";
  $cols[] = "phylum";
  $cols[] = "class";
  $cols[] = "order";
  $cols[] = "family";
  $cols[] = "genus";

  echo join($s, $cols);
  echo "\n";
}

function joinIfExists($separator, $arr) {
    if (!empty($arr)) {
      return join($separator, $arr);
    }
    else {
      return "";
    }
}

function safeAlnum($unsafeString) {
  if (ctype_alnum($unsafeString)) {
    return $unsafeString;
  }
  else {
    log2("ERROR", "Invalid alphanumeric value", "logs/havistin.log");
  }
}

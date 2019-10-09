<?php
/*
<pre>
Terms:
- document: document-level data from api
- gathering: gathering-level data from api
- unit: unit-level data from api
- element: document, gathering and unit combined together for each unit
*/
//phpinfo();

require_once "config/env.php";
require_once "logger.php";
require_once "telegram.php";
require_once "lajifi.php";
require_once "nanodb.php";


if (isset($_GET['debug'])) {
  define("DEBUG", true);
  echo "<strong style='color: red;'>DEBUG MODE</strong>\n\n";
}
else{
  define("DEBUG", false);
  echo "<strong style='color: green;'>PRODUCTION MODE</strong>\n\n";
}

// ---------------------------------------------------------------
// Rarities
// Todo: restrict to finnish species
if ($_GET['mode'] == "rarities") {
  logger("lajifi.log", "info", "GET rarities");

  // Settings
  $debugThreshold = 0;
  $threshold = 10;
  $db = new nanoDb(("data/" . DATAFILE_RARITIES), 100);

  // Get and filter data
  $url = buildListQuery("ML.206"); // Only Finnish
  $elementsJSON = getDataFromLajifi($url);
  $elementsArrWithMetadata = json_decode($elementsJSON, TRUE);
  $elementsArr = $elementsArrWithMetadata['results'];

  // Add rarity scores to each unit
  $elementsArr = addRarityScore($elementsArr);
//  echo "\n\nHERE:\n"; print_r($elementsArr); // debug

  if (DEBUG) { print_r($elementsArr); } // debug

  // Handle each element (= unit)
  foreach ($elementsArr as $i => $element) {
    $scoreHelper = 0;
    if (isset($element['rarityScore']['total'])) {
      $scoreHelper = $element['rarityScore']['total'];
    }
    else {
      $scoreHelper = 0;
    }
    logger("lajifi.log", "info", "Handled observation " . $element['unit']['unitId'] . " with rarityScore of " . $scoreHelper);

    // DEBUG
    if (DEBUG) {
      if ($scoreHelper >= $debugThreshold) {
        echo formatRarityDataToPlaintext($element) . "\n\n";

        // Save to datafile
        // TODO: separate debug and prod datafiles
        $db->addRecord($element['unit']['unitId'], $element);
      }
    }
    // PROD
    else {
      if ($scoreHelper >= $threshold) {
        // Send to telegram
        sendToTelegram(formatRarityDataToPlaintext($element));

        // Save to datafile
        $db->addRecord($element['unit']['unitId'], $element);

        echo formatRarityDataToPlaintext($element) . "\n\n"; // debug
      }
    }
  }
}

// ---------------------------------------------------------------
// New documents
elseif ($_GET['mode'] == "documents") {
  logger("lajifi.log", "info", "GET documents");

  // Get and filter data
  $url = buildListQuery();
  $elementsJSON = getDataFromLajifi($url);
  $elementsArrWithMetadata = json_decode($elementsJSON, TRUE);
  $elementsArr = $elementsArrWithMetadata['results'];

  // Create a list of documents
  $documentsArr = buildDocumentList($elementsArr);

  // Handle each document
  foreach ($documentsArr as $documentId => $document) {
    // DEBUG
    if (DEBUG) {
      echo "<pre>" . formatMessageDataToPlaintext($documentId, $document) . "</pre>"; // debug to browser
      //  sendToTelegram(json_encode($document)); // debug to Telegram
    }
    // PROD
    else {
      sendToTelegram(formatMessageDataToPlaintext($documentId, $document));
    }
  }
}

// ---------------------------------------------------------------
// Other = warning
else {
  logger("lajifi.log", "warning", "Mode not set correctly");
  echo "Warning: Mode not set correctly";
}



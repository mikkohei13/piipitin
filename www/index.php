<pre>
<?php
//phpinfo();

require_once "config/env.php";
require_once "logger.php";
require_once "telegram.php";
require_once "lajifi.php";

if (isset($_GET['debug'])) {
  define("DEBUG", true);
  echo "<strong style='color: red;'>DEBUG MODE</strong>\n\n";
}
else{
  define("DEBUG", false);
  echo "<strong style='color: green;'>PRODUCTION MODE</strong>\n\n";
}

// Laji.fi

// Rarities
// Todo: restrict to finnish species
if ($_GET['mode'] == "rarities") {
  $debugThreshold = 1;
  $threshold = 1;

  $url = buildListQuery("ML.206"); // Only Finnish

  $dataJSON = getDataFromLajifi($url);
  $dataArrWithMetadata = json_decode($dataJSON, TRUE);
  $dataArr = $dataArrWithMetadata['results'];

  $dataArr = addRarityScore($dataArr);
//  echo "\n\nHERE:\n"; print_r($dataArr); // debug

  foreach ($dataArr as $i => $data) {
    if (DEBUG) {
//      print_r($dataArr);
      if ($data['rarityScore']['total'] >= $debugThreshold) { // TODO: makes notice
        echo formatRarityDataToPlaintext($data) . "\n\n";
      }
    }
    else {
      if ($data['rarityScore']['total'] >= $threshold) {
        echo formatRarityDataToPlaintext($data) . "\n\n";
      }
    }
  }
}

// New documents
elseif ($_GET['mode'] == "documents") {
  $url = buildListQuery();

  $dataJSON = getDataFromLajifi($url);
  $dataArrWithMetadata = json_decode($dataJSON, TRUE);
  $dataArr = $dataArrWithMetadata['results'];

  $documentList = buildDocumentList($dataArr);

  foreach ($documentList as $documentId => $data) {
    if (DEBUG) {
      echo "<pre>" . formatMessageDataToPlaintext($documentId, $data) . "</pre>"; // debug to browser
      //  sendToTelegram(json_encode($data)); // debug to Telegram
    }
    else {
      sendToTelegram(formatMessageDataToPlaintext($documentId, $data));
    }
  }
}

else {
  echo "Error: Mode not set correctly";
}



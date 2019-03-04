<pre>
<?php
//phpinfo();

require_once "config/env.php";
require_once "logger.php";
require_once "telegram.php";
require_once "lajifi.php";

if (isset($_GET['debug'])) {
  define("DEBUG", true);
}
else{
  define("DEBUG", false);
}
echo "CONTS: " . DEBUG;



// Laji.fi

// Rarities
// Todo: restrict to finnish species
if ($_GET['mode'] == "rarities") {
  $url = buildListQuery("ML.206"); // Only Finnish

  $dataJSON = getDataFromLajifi($url);
  $dataArrWithMetadata = json_decode($dataJSON, TRUE);
  $dataArr = $dataArrWithMetadata['results'];

  $dataArr = addRarityScore($dataArr);
  echo "\n\nHERE:\n"; print_r($dataArr); // debug
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



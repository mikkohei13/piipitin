<?php
//phpinfo();

require_once "config/env.php";
require_once "logger.php";
require_once "telegram.php";
require_once "lajifi.php";

// Laji.fi

$url = buildListQuery(LAJIFI_TOKEN);
$dataJSON = getDataFromLajifi($url);
$dataArrWithMetadata = json_decode($dataJSON, TRUE);
$dataArr = $dataArrWithMetadata['results'];

$documentListJSON = buildDocumentList($dataArr, "http://tun.fi/JX.987433");

foreach ($documentListJSON as $documentId => $data) {

//  sendToTelegram(formatMessageDataToPlaintext($documentId, $data)); // prod

  echo "<pre>" . formatMessageDataToPlaintext($documentId, $data) . "</pre>"; // debug to browser
//  sendToTelegram(json_encode($data)); // debug to Telegram
}

//header('Content-type: application/json'); echo $dataJSON;

// Telegram
/*
$message = "Test message ÅÄÖåäö";

$response = sendToTelegram($message);

if ($response['ok']) {
  echo "Success";
}
else {
  echo "Failure";
}
echo "<pre>";
print_r($response);
*/





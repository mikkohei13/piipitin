<?php
//phpinfo();

require_once "config/env.php";
require_once "logger.php";
require_once "telegram.php";
require_once "lajifi.php";

// Laji.fi

$url = buildListQuery(LAJIFI_TOKEN);
$dataJSON = getDataFromLajifi($url);
$documentListJSON = buildDocumentList($dataJSON, "http://tun.fi/JX.987433");

foreach ($documentListJSON as $documentId => $data) {
  sendToTelegram(json_encode($data));
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





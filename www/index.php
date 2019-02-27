<?php
//phpinfo();

include "config/env.php";

$message = "Test message ÅÄÖåäö";

$url = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage?chat_id=" . TELEGRAM_CHAT_ID . "&text=$message";

$response = json_decode(file_get_contents($url), TRUE);


if ($response['ok']) {
  echo "Success";
}
else {
  echo "Failure";
}
echo "<pre>";
print_r($response);
echo $url;




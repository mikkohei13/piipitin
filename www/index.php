<?php
//phpinfo();

require_once "config/env.php";
require_once "logger.php";
require_once "telegram.php";

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




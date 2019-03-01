<?php

function sendToTelegram($message) {
  $url = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage?chat_id=" . TELEGRAM_CHAT_ID . "&text=$message";

  $response = file_get_contents($url);

  // Note that on failure, file_get_contents will return FALSE, not the string from the api
  if ($response === FALSE) {
    logger("telegram.log", "error", ("sending message to Telegram failed\t" . $url . "\t" . json_encode($http_response_header)));
  }
  else {
    logger("telegram.log", "ok", "sent message to Telegram");
  }

  return json_decode($response, TRUE);
}
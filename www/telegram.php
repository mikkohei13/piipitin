<?php

function sendToTelegram($message) {
  $url = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage?chat_id=" . TELEGRAM_CHAT_ID . "&text=$message";

  $response = json_decode(file_get_contents($url), TRUE);

  if ($response['ok']) {
    logger("telegram.log", "ok", "sent message to Telegram");
  }
  else {
    logger("telegram.log", "error", "sending message to Telegram failed");
  }

  return $response;
}
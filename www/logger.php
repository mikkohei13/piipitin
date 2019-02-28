<?php


function logger($filename, $type, $message) {
  $filePath = "logs/$filename";

  $fullMessage = date("Y-m-d H:i:s") . "\t" . $type . "\t" . $message . "\n";

  return file_put_contents($filePath, $fullMessage, FILE_APPEND);
}

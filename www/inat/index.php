<?php
require_once "readINat.php";
require_once "postToAPI.php";
require_once "logger.php";

log2("ok", "Script started");

// Get data from iNat

// Test data
$data = Array();
$data['sourceId'] = "HR.TEST";
$data['documents'] = "Json goes here";


// POST to API
$result = postToAPI($data);

if (200 == $result['code']) {
  echo "Successfully posted to API, which responded 200 and: " . $result['response'];
  log2("ok", "Posted to API");
  // Continue processing as usual
}
else {
  echo "Posting to API failed, with responded " . $result['code'] . " and: " . $result['response'];
  log2("error", "Posting to API failed");
  // Stop processing
}

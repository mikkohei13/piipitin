<?php

$data = Array();
$data['sourceId'] = "HR.TEST";
$data['documents'] = "Json goes here";

// Inside Docker localhost is 127.0.0.1
$apiURL = "http://127.0.0.1/inat/test/index.php";

//$json = json_encode($arr);



// POST to API


$options = array(
  'http' => array(
      'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
      'method'  => 'POST',
      'content' => http_build_query($data),
  )
);
$context  = stream_context_create($options);
$result = file_get_contents($apiURL, false, $context);

echo "<pre>FINISHED\n";
var_dump($result);



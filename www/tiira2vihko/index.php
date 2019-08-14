<pre>
<?php
require_once "handle_row.php";

/*
Tee havainto kaikilla tiedoilla, erikoismerkeillä ($deg; etc) ja molemmilla lomakkeilla
*/
$file = "data/2019.txt";
//$file = "data/testihavainnot.txt";

$rowNumber = 0;

if (($handle = fopen($file, "r")) !== FALSE) {

  // Loops through rows
  while (($row = fgetcsv($handle, 0, "#")) !== FALSE) {

    // Convert ISO-8859-1 to UTF-8
    $row = array_map("utf8_encode", $row);

    // Header row
    if (0 == $rowNumber) {
      $colNames = setColNames($row);
    }
    // Data row
    else {
//      $fieldCount = count($row);
//      echo "$fieldCount fields\t"; // debug

      print_r (handleRow($row, $colNames)); // TODO: Check if === false

      // Loops through cells
      /*
      foreach ($row as $colNumber => $cell) {
        echo $colNames[$colNumber] . ":" . $cell . "\t";
      }
      */

      echo "\n"; // debug
    }

    $rowNumber++;
  }
  fclose($handle);
  echo "Finished.";
}
else {
  echo "Reading file failed.";
}

function setColNames($row) {
  $colNames = Array();

  foreach ($row as $i => $colName) {
    $colNames[$i] = $colName;
  }

  print_r ($colNames); // debug
  return $colNames;
}




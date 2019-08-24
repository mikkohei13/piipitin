<pre>
<?php
require_once "handle_row.php";
require_once "export_data.php";

$file = "data/2019.txt";
$file = "data/120.txt";
$file = "data/120eityhjia.txt";
$file = "data/havainnot.txt";
$file = "data/new.txt";

$vihkoRows = Array();
$rowNumber = 0;
$skippedRowCount = 0;
$exportedRowCount = 0;
$skippedRowMessages = "";

if (($handle = fopen($file, "r")) !== FALSE) {

  // Loops through rows
  while (($row = fgetcsv($handle, 0, "#")) !== FALSE) {
    $vihkoRow = NULL;

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

      $vihkoRow = handleRow($row, $colNames);
      if (isset($vihkoRow['skipped']) && TRUE === $vihkoRow['skipped']) {
        $skippedRowMessages .= $vihkoRow['row'] . " " . $vihkoRow['skippingReason'] . "\n";
        $skippedRowCount++;
      }
      else {
        $vihkoRows[] = $vihkoRow;
        $exportedRowCount++;
      }
    }

    $rowNumber++;
  }
  fclose($handle);

  // Do something with the data
  echo "\nFound " . ($rowNumber - 1) . " data rows."; // Deduct header row
  echo "\nExported $exportedRowCount rows to ";

  export_data($vihkoRows);

  echo "\nSkipped $skippedRowCount rows:\n";
  echo $skippedRowMessages;

}
else {
  echo "Reading file failed.";
}

function setColNames($row) {
  $colNames = Array();

  foreach ($row as $i => $colName) {
    $colNames[$i] = $colName;
  }

//  print_r ($colNames); // debug
  return $colNames;
}




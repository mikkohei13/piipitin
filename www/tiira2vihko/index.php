<pre>
<?php
require_once "handle_row.php";
require_once "export_data.php";

$file = "data/2019.txt";
$file = "data/havainnot.txt";
$file = "data/120.txt";

$vihkoRows = Array();
$rowNumber = 0;

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
        echo $vihkoRow['row'] . " " . $vihkoRow['skippingReason'] . "\n";
      }
      else {
        $vihkoRows[] = $vihkoRow;
      }
    }

    $rowNumber++;
  }
  fclose($handle);

  // Do something with the data
  export_data($vihkoRows);

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




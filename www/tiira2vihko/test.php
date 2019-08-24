<?php
/*
Array
(
    [file] => Array
        (
            [name] => new.txt
            [type] => text/plain
            [tmp_name] => /tmp/phphzDdA8
            [error] => 0
            [size] => 2201
        )

)
*/

//require_once "conversion.php";
require_once "handle_row.php";
require_once "export_data.php";


if (debug()) {
  echo "<pre>";
  print_r ($_FILES);
}

// TODO: handle empty file

$fileString = checkFileSecurity($_FILES);

if (FALSE === $fileString) {
  exit("<!--File did not pass validation-->");
}

if (FALSE === isTiiraFile(substr($fileString, 0, 50))) {
  exit("Tiedosto ei ole Tiira-muodossa.");
}

// Handle file
$vihkoRows = Array();
//$rowNumber = 0;
$skippedRowCount = 0;
$exportedRowCount = 0;
$skippedRowMessages = "";

$fileArr = explode("\n", $fileString); // Todo: check how line endings are handled


// Loops through rows
foreach ($fileArr as $rowNumber => $rowString) {
  $vihkoRow = NULL;
  $row = explode("#", $rowString); // Each cell/column in its own array item

  // Convert ISO-8859-1 to UTF-8
  $row = array_map("utf8_encode", $row);

  // Header row
  if (0 == $rowNumber) {
    $colNames = $row;
  }
  // Last empty row
  elseif (1 == count($row)) {
    // Do nothing
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

//  $rowNumber++;
}

// Option 1) Receive string
$vihkoString = export_data($vihkoRows);

/*
// Option 2) Receive filename
$vihkoFilename = export_data($vihkoRows); // Saves to a file
$vihkoString = file_get_contents($vihkoFilename); // Reads the file
*/

if (debug()) {
  echo "\n" . $vihkoString;
}
else {
  sendStringToBrowser($vihkoString, "vihko-import-(JX.519).csv"); // Sends to browser
}




//---------------------------------------------------------------------
// FUNCTIONS

/*
function setColNames($row) {
  $colNames = Array();

  foreach ($row as $i => $colName) {
    $colNames[$i] = $colName;
  }

//  print_r ($colNames); // debug
  return $colNames;
}
*/

function sendStringToBrowser($content, $filename) {
  // https://stackoverflow.com/questions/10835628/how-to-send-a-string-as-file-to-the-browser-using-php

  $length = strlen($content);

  header('Content-Description: File Transfer');
  header('Content-Type: text/plain; charset=utf-8');
  header('Content-Disposition: attachment; filename=' . $filename);
  header('Content-Transfer-Encoding: binary');
  header('Content-Length: ' . $length);
  header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
  header('Expires: 0');
  header('Pragma: public');

  echo $content;
  return TRUE;
}


function isTiiraFile($fileString) {
  if (substr($fileString, 0, 50) == "Havainto id#Laji#Pvm1#Pvm2#Kello_hav_1#Kello_hav_2") {
    return TRUE;
  }
  else {
    return FALSE;
  }
}

/*
Input: $_FILES
Returns:
- If problem with file, returns FALSE and echoes error message
- If file is ok, returns file as a string
*/
function checkFileSecurity($filesArray) {

  $fileSizeLimit = 10000000; // 10 MB

  try {
      
      // Undefined | Multiple Files | $_FILES Corruption Attack
      // If this request falls under any of them, treat it invalid.
      if (!isset($filesArray['file']['error']) || is_array($filesArray['file']['error'])) {
          throw new RuntimeException('Virheellinen tiedosto.');
      }

      // Check $_FILES['file']['error'] value.
      switch ($filesArray['file']['error']) {
          case UPLOAD_ERR_OK:
              break;
          case UPLOAD_ERR_NO_FILE:
              throw new RuntimeException('Et lähettänyt tiedostoa.');
          case UPLOAD_ERR_INI_SIZE:
          case UPLOAD_ERR_FORM_SIZE:
              throw new RuntimeException('Liian suuri tiedosto. Suurin sallittu koko on ' . $fileSizeLimit);
          default:
              throw new RuntimeException('Tuntematon virhe ' . $filesArray['file']['error']);
      }

      // You should also check filesize here. 
      if ($filesArray['file']['size'] > $fileSizeLimit) {
          throw new RuntimeException('Liian suuri tiedosto. Suurin sallittu koko on ' . $fileSizeLimit);
      }

      // DO NOT TRUST $_FILES['file']['mime'] VALUE !!
      // Check MIME Type by yourself.
      $finfo = new finfo(FILEINFO_MIME_TYPE);
      if (false === $ext = array_search(
          $finfo->file($filesArray['file']['tmp_name']),
          array(
              'txt' => 'text/plain',
          ),
          true
      )) {
          throw new RuntimeException('Virheellinen tiedostomuoto. Vain tekstitaulukko (.csv) on sallittu.');
      }

      // Now $ext contains file extension

      /*
      // You should name it uniquely.
      // DO NOT USE $_FILES['file']['name'] WITHOUT ANY VALIDATION !!
      // On this example, obtain safe unique name from its binary data.
      if (!move_uploaded_file(
          $_FILES['file']['tmp_name'],
          sprintf('./uploads/%s.%s',
              sha1_file($_FILES['file']['tmp_name']),
              $ext
          )
      )) {
          throw new RuntimeException('Failed to move uploaded file.');
      }
      */

      return file_get_contents($filesArray['file']['tmp_name']);
  }
  catch (RuntimeException $e) {
      echo $e->getMessage();
      return FALSE;
  }
}

function debug() {
  if (isset($_GET['DEBUG'])) {
    return TRUE;
  }
  else {
    return FALSE;
  }
}

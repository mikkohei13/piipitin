<?php
header('Content-Type: text/plain; charset=utf-8');

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

echo "<pre>";

print_r ($_FILES);

// TODO: Check that conforms to Tiira format. Separate function for this.
// TODO: handle empty file

$fileString = checkFileSecurity($_FILES);

if (FALSE !== $fileString) {

  // Handle file

  echo $fileString;
}


/*
Input: $_FILES
Returns:
- If problem with file, returns FALSE and echoes error message
- If file is ok, returns file as a string
*/
function checkFileSecurity($filesArray) {

  $fileSizeLimit = 10000000;

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

      print_r ($filesArray);
      return file_get_contents($filesArray['file']['tmp_name']); // ABBA miksi tämä ei toimi????? Palauttaa tyhjän tjsp.
  }
  catch (RuntimeException $e) {
      echo $e->getMessage();
      return FALSE;
  }
}


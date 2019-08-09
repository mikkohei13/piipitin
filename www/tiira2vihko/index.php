<pre>
<?php

/*
Tee havainto kaikilla tiedoilla, erikoismerkeillä ($deg; etc) ja molemmilla lomakkeilla
*/
$file = "data/2019.txt";
$file = "data/testihavainnot.txt";

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

function handleRow($row, $colNames) {
  $vihkoRow = Array();

  // Build indexed array from associative array
  $rowAssoc = Array();
  foreach ($row as $colNumber => $cell) {
    $rowAssoc[$colNames[$colNumber]] = trim($cell);
  }

  if ("SUMMA" == $rowAssoc['rivityyppi']) {
    return FALSE;
  }


  // Taxon
  $vihkoRow['Laji - Määritys'] = $rowAssoc['Laji'];

  // Id
  $vihkoRow['Muut tunnisteet - Havainto'] = "tiira.fi:" . $rowAssoc['Havainto id'];

  // Date begin and end
  $vihkoRow['Alku - Yleinen keruutapahtuma'] = $rowAssoc['Pvm1'];
  $vihkoRow['Loppu - Yleinen keruutapahtuma'] = $rowAssoc['Pvm2'];

  // Time begin
  if (!empty($rowAssoc['Kello_lintu_1'])) {
    $kello1 = $rowAssoc['Kello_lintu_1'];
  }
  elseif (!empty($rowAssoc['Kello_hav_1'])) {
    $kello1 = $rowAssoc['Kello_hav_1'];
  }
  if (isset($kello1)) {
    $vihkoRow['Alku - Yleinen keruutapahtuma'] = $vihkoRow['Alku - Yleinen keruutapahtuma'] . ", " . $kello1;
  }

  // Time end
  if (!empty($rowAssoc['Kello_lintu_2'])) {
    $kello2 = $rowAssoc['Kello_lintu_2'];
  }
  elseif (!empty($rowAssoc['Kello_hav_2'])) {
    $kello2 = $rowAssoc['Kello_hav_2'];
  }
  if (isset($kello2)) {
    $vihkoRow['Loppu - Yleinen keruutapahtuma'] = $vihkoRow['Loppu - Yleinen keruutapahtuma'] . ", " . $kello2;
  }

  // Locality
  $vihkoRow['Kunta - Keruutapahtuma'] = $rowAssoc['Kunta'];
  $vihkoRow['Paikannimet - Keruutapahtuma'] = $rowAssoc['Paikka'];

  // Coordinates
  // If there is one coordinate about the bird, expect that there are full coordinates
  if (!empty($rowAssoc['X-koord-linnun'])) {
    $vihkoRow['Koordinaatit@N'] = $rowAssoc['Y-koord'];
    $vihkoRow['Koordinaatit@E'] = $rowAssoc['X-koord'];
    $vihkoRow['Koordinaattien tarkkuus metreinä'] = coordinateAccuracyToInt($rowAssoc['Tarkkuus_linnun']);
    $vihkoRow['Lisätiedot - Keruutapahtuma'] = "Linnun koordinaatit, tarkkuus " . $rowAssoc['Tarkkuus_linnun'] . ". ";
  }
  // Else expect that there are full coordinates for observer
  else {
    $vihkoRow['Koordinaatit@N'] = $rowAssoc['Y-koord'];
    $vihkoRow['Koordinaatit@E'] = $rowAssoc['X-koord'];
    $vihkoRow['Koordinaattien tarkkuus metreinä'] = coordinateAccuracyToInt($rowAssoc['Tarkkuus']);
    $vihkoRow['Lisätiedot - Keruutapahtuma'] = "Havainnoijan koordinaatit, tarkkuus " . $rowAssoc['Tarkkuus'] . ".";
  }
  $vihkoRow['Koordinaatit@sys - Keruutapahtuma'] = "wgs84";

  // Notes. (Lisätietoja_2 first, because it's first on the tiira.fi form)
  if (!empty($rowAssoc['Lisätietoja']) && !empty($rowAssoc['Lisätietoja_2'])) {
    $vihkoRow['Lisätiedot - Havainto'] = $rowAssoc['Lisätietoja_2'] . " / " . $rowAssoc['Lisätietoja'];
  }
  else {
    $vihkoRow['Lisätiedot - Havainto'] = $rowAssoc['Lisätietoja_2'] . " " . $rowAssoc['Lisätietoja'];
    $vihkoRow['Lisätiedot - Havainto'] = trim($vihkoRow['Lisätiedot - Havainto']);
  }

  // Atlas
  $vihkoRow['Pesimisvarmuusindeksi - Havainto'] = $rowAssoc['Atlaskoodi'];

  // Metadata
  $vihkoRow['Lisätiedot - Keruutapahtuma'] .= "Tallentaja: " . $rowAssoc['Tallentaja'] . ". ";
  $vihkoRow['Lisätiedot - Keruutapahtuma'] .= "Tallennusaika: " . $rowAssoc['Tallennusaika'] . ". ";

  // Observers
//  $vihkoRow['Havainnoijat - Yleinen keruutapahtuma'] = $rowAssoc['Havainnoijat'];
  $vihkoRow['Havainnoijat - Yleinen keruutapahtuma'] = ""; // Hidden due to privary requirements TODO: Add importer name
  $vihkoRow['Havainnoijien nimet ovat julkisia - Yleinen keruutapahtuma'] = "Kyllä";

  // Coarsening
  // If contains anything, will be coarsened.
  // Expect that if user wants to totally hide the observation, they will not import it.
  if (empty($rowAssoc['Salattu'])) {
    $vihkoRow['Havainnon tarkat paikkatiedot ovat julkisia - Havaintoerä'] = "Ei karkeistettu";    
  }
  else {
    $vihkoRow['Havainnon tarkat paikkatiedot ovat julkisia - Havaintoerä'] = "10 km"; 
  }

  // Unit info
  $vihkoRow['Määrä - Havainto'] = $rowAssoc['Määrä'];
  if ("pariutuneet" == $rowAssoc['Sukupuoli']) {
    $vihkoRow['Määrä - Havainto'] .= " pariutuneet";
    $vihkoRow['Sukupuoli - Havainto'] = "eri sukupuolia";
  }
  elseif ("k" == $rowAssoc['Sukupuoli']) {
    $vihkoRow['Sukupuoli - Havainto'] = "koiras";
  }
  elseif ("n" == $rowAssoc['Sukupuoli']) {
    $vihkoRow['Sukupuoli - Havainto'] = "naaras";
  }

  /*
  $vihkoRow[' - Havainto'] = $rowAssoc[''];
  $vihkoRow[' - Havainto'] = $rowAssoc[''];
  $vihkoRow[' - Havainto'] = $rowAssoc[''];
  
*/

  



/*
Not handled:
Paikannettu
*/

  return $vihkoRow;
}

function coordinateAccuracyToInt($str) {

  switch ($str) {
    case "<10 m":
        $int = 10;
        break;
    case "<50 m":
        $int = 50;
        break;
    case "<200 m":
        $int = 200;
        break;
    case "<250 m":
        $int = 250;
        break;
    case "<500 m":
        $int = 500;
        break;
    case "<1 km":
        $int = 1000;
        break;
    case "<5 km":
        $int = 5000;
        break;
    case ">500 m":
        $int = 2000;
        break;
    case ">5 km":
        $int = 10000;
        break;
  }

  return $int;
}

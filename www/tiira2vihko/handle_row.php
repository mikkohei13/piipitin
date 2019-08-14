<?php



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
  
    $notesGathering = Array();
    $keywordsDocument = Array();
    $keywordsUnit = Array();
  
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
      array_push($notesGathering, "linnun koordinaatit");
      if (empty($rowAssoc['Tarkkuus_linnun'])) {
        array_push($notesGathering, "koordinaattien tarkkuus tuntematon");
      }
      else {
        array_push($notesGathering, "koordinaattien tarkkuus " . $rowAssoc['Tarkkuus_linnun']);
      }
    }
    // Else expect that there are full coordinates for observer
    else {
      $vihkoRow['Koordinaatit@N'] = $rowAssoc['Y-koord'];
      $vihkoRow['Koordinaatit@E'] = $rowAssoc['X-koord'];
      $vihkoRow['Koordinaattien tarkkuus metreinä'] = coordinateAccuracyToInt($rowAssoc['Tarkkuus']);
      array_push($notesGathering, "havainnoijan koordinaatit");
      if (empty($rowAssoc['Tarkkuus'])) {
        array_push($notesGathering, "koordinaattien tarkkuus tuntematon");
      }
      else {
        array_push($notesGathering, "koordinaattien tarkkuus " . $rowAssoc['Tarkkuus']);
      }
    }
    // ABBA
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
    array_push($notesGathering, "tallentaja: " . $rowAssoc['Tallentaja']);
    array_push($notesGathering, "tallentaja: " . $rowAssoc['Tallennusaika']);
  
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

    // Keywords
    array_push($keywordsDocument, "tiira.fi");
    array_push($keywordsDocument, "tiira2vihko");
    array_push($keywordsDocument, "import");
  

    $vihkoRow['Lisätiedot - Keruutapahtuma'] = implode(" / ", $notesGathering);
    $vihkoRow['Avainsanat - Havaintoerä'] = implode(",", $keywordsDocument);

    if (!empty($keywordsUnit)) {
        $vihkoRow['Kokoelma/Avainsanat - Havainto'] = implode(",", $keywordsUnit);
    }

  
    /*
    $vihkoRow[' - Havainto'] = $rowAssoc[''];
    $vihkoRow[' - Havainto'] = $rowAssoc[''];
    $vihkoRow[' - Havainto'] = $rowAssoc[''];
    
  */
  
    
  
  
  
  /*
  TODO:
  Notes when cood accuracy not known
  tags: noCoordinateAccuracy
  
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
      case "": // Unknown accuracy
          $int = 2000;
          break;
    }
  
    return $int;
  }
  
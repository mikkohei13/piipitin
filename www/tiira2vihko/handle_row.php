<?php

/* TODO:
  - observer's MA-code (or on laji.fi import?)
  - remove empty items from arrays
*/

function handleRow($row, $colNames) {
    $vihkoRow = Array();
  
    $notesGathering = Array();
    $notesUnit = Array();
    $keywordsDocument = Array();
    $keywordsUnit = Array();
  
    // Build indexed array from associative array
    $rowAssoc = Array();
    foreach ($row as $colNumber => $cell) {
      $rowAssoc[$colNames[$colNumber]] = trim($cell);
    }
  
    // Skip SUMMA rows, since they duplicate the "real" observations
    if ("SUMMA" == $rowAssoc['rivityyppi']) {
      return NULL;
    }
  
    // Taxon
    $vihkoRow['Laji - Määritys'] = $rowAssoc['Laji'];
  
    // Id
    $vihkoRow['Muut tunnisteet - Havainto'] = "tiira.fi:" . $rowAssoc['Havainto id'];
    array_push($notesUnit, "https://www.tiira.fi/selain/naytahavis.php?id=" . $rowAssoc['Havainto id']);
  
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
      array_push($keywordsUnit, "koordinaatit-linnun");
      if (empty($rowAssoc['Tarkkuus_linnun'])) {
        array_push($keywordsUnit, "koordinaatit-tarkkuus-tuntematon");
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
      array_push($keywordsUnit, "koordinaatit-havainnoijan");
      if (empty($rowAssoc['Tarkkuus'])) {
        array_push($keywordsUnit, "koordinaatit-tarkkuus-tuntematon");
        array_push($notesGathering, "koordinaattien tarkkuus tuntematon");
      }
      else {
        array_push($notesGathering, "koordinaattien tarkkuus " . $rowAssoc['Tarkkuus']);
      }
    }
    $vihkoRow['Koordinaatit@sys - Keruutapahtuma'] = "wgs84";
  
    // Notes. (Lisätietoja_2 first, because it's first on the tiira.fi form)
    array_push($notesUnit, $rowAssoc['Lisätietoja_2']);
    array_push($notesUnit, $rowAssoc['Lisätietoja']);
  
    // Atlas
    $vihkoRow['Pesimisvarmuusindeksi - Havainto'] = $rowAssoc['Atlaskoodi'];
  
    // Metadata
    array_push($notesGathering, "tallentanut Tiiraan: " . $rowAssoc['Tallentaja']);
    array_push($notesGathering, "tallennettu Tiiraan: " . $rowAssoc['Tallennusaika']);
  
    // Observers
    $vihkoRow['Havainnoijat - Yleinen keruutapahtuma'] = str_replace(",", ";", $rowAssoc['Havainnoijat']);
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

    /*
    if (!empty($rowAssoc['Tallenteita'])) {
        array_push($notesUnit, $rowAssoc['Tallenteita']);
    }
    */
  
    // Abundance & sex
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

    // Plumage
    $mapPlumage["ad"] = "ad (aikuinen)";
    $mapPlumage["eijp"] = "eijp (muu kuin juhlapuku)";
    $mapPlumage["imm"] = "imm (ei-sukukypsä)";
    $mapPlumage["jp"] = "jp (juhlapuku)";
    $mapPlumage["juv"] = "juv (1. täydellinen puku)";
    $mapPlumage["n-puk"] = "n-puk (naaraspukuinen)";
    $mapPlumage["pull"] = "pull (untuvapoikanen)";
    $mapPlumage["subad"] = "subad (juv ja ad välinen puku)";
    $mapPlumage["tp"] = "tp (talvipukuinen)";
    $mapPlumage["vp"] = "vp (vaihtopukuinen)";
    $mapPlumage["pep"] = "pep (peruspuku)"; // todo: add
    $mapPlumage["ss"] = "ss (sulkasatoinen)"; // todo: add

    if (!empty($rowAssoc['Puku'])) {
        $vihkoRow['Linnun puku - Havainto'] = $mapPlumage[$rowAssoc['Puku']];
    }

    // Age
    $mapAge["+1kv"] = "+1kv (vanhempi kuin 1. kalenterivuosi)";
    $mapAge["1kv"] = "1kv (1. kalenterivuosi)";
    $mapAge["+2kv"] = "+2kv (vanhempi kuin 2. kalenterivuosi)";
    $mapAge["2kv"] = "2kv (edellisenä vuonna syntynyt)";
    $mapAge["+3kv"] = "+3kv";
    $mapAge["3kv"] = "3kv";
    $mapAge["+4kv"] = "+4kv";
    $mapAge["4kv"] = "4kv";
    $mapAge["+5kv"] = "+5kv";
    $mapAge["5kv"] = "5kv";
    $mapAge["+6kv"] = "+6kv";
    $mapAge["6kv"] = "6kv";
    $mapAge["+7kv"] = "+7kv";
    $mapAge["7kv"] = "7kv";
    $mapAge["+8kv"] = "+8kv";
    $mapAge["8kv"] = "8kv";
    $mapAge["fl"] = "fl (täysikasvuinen)";
    $mapAge["pm"] = "pm (maastopoikanen)";
    $mapAge["pp"] = "pp (pesäpoikanen";

    if (!empty($rowAssoc['Ikä'])) {
        $vihkoRow['Linnun ikä - Havainto'] = $mapAge[$rowAssoc['Ikä']];
    }

    // Moving (status)
    // This handles status in different way than Vihko so far by adding direction to moving field
    $vihkoRow['Linnun tila - Havainto'] = $rowAssoc['Tila'];
    
    // Flock
    if (!empty($rowAssoc['Parvi'])) {
      array_push($notesUnit, "parvi " . $rowAssoc['Parvi']);
    }

    // Twitched
    if ("X" == $rowAssoc['Bongattu']) {
      $vihkoRow['Bongattu - Havainto'] = "Kyllä"; 
    }

    // Breeding
    if ("X" == $rowAssoc['Pesintä']) {
      $vihkoRow['Pesintä - Havainto'] = "Kyllä"; 
    }

    // Indirect
    /*
//    No field on Tiira UI, not currently used?
    if ("X" == $rowAssoc['Epäsuora havainto']) {
//      array_push($notesUnit, "epäsuora havainto");
      $vihkoRow['Havainnointitapa - Havainto'] = "Epäsuora havainto (jäljet, ulosteet, yms)"; 
    }
    */


    // Keywords
    array_push($keywordsDocument, "tiira.fi");
    array_push($keywordsDocument, "import");
    array_push($keywordsDocument, "tiira2vihko");
  
// TODO: remove empty items from arrays

    $vihkoRow['Avainsanat - Havaintoerä'] = implode(",", $keywordsDocument);

    if (!empty($notesGathering)) {
        $vihkoRow['Lisätiedot - Keruutapahtuma'] = implode(" / ", $notesGathering);
    }
    if (!empty($keywordsUnit)) {
        $vihkoRow['Kokoelma/Avainsanat - Havainto'] = implode(",", $keywordsUnit);
    }
    if (!empty($notesUnit)) {
        $vihkoRow['Lisätiedot - Havainto'] = implode(" / ", $notesUnit);
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
  
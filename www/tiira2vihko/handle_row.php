<?php

function handleRow($row, $colNames) {
//    print_r ($colNames); print_r ($row); return NULL; // debug: prints our raw rows as arrays
    $vihkoRow = Array();
  
    $notesGathering = Array();
    $notesUnit = Array();
    $keywordsDocument = Array();
    $keywordsUnit = Array();
    $identifiersUnit = Array();
  
    // Build indexed array from associative array
    $rowAssoc = Array();
    foreach ($row as $colNumber => $cell) {
      $rowAssoc[$colNames[$colNumber]] = trim($cell);
    }
  
    // Skip SUMMA rows, since they duplicate the "real" observations
    if ("SUMMA" == $rowAssoc['rivityyppi']) {
      return Array('skipped' => TRUE, 'skippingReason' => "sum row", 'row' => $rowAssoc['Havainto id']);
    }
    // Skip if both Y-coords are missing, otherwise expect that full coordinates are set 
    elseif (empty($rowAssoc['Y-koord']) && empty ($rowAssoc['X-koord-linnun'])) {
      return Array('skipped' => TRUE, 'skippingReason' => "coordinates missing", 'row' => $rowAssoc['Havainto id']);
    }

    // Taxon
    $vihkoRow['Laji - Määritys'] = $rowAssoc['Laji'];
  
    // Id
    array_push($identifiersUnit, ("tiira.fi:" . $rowAssoc['Havainto id']));
//    array_push($notesUnit, "https://www.tiira.fi/selain/naytahavis.php?id=" . $rowAssoc['Havainto id']); // This link may change
  
    // Date begin and end
    $vihkoRow['Alku - Yleinen keruutapahtuma'] = formatDate($rowAssoc['Pvm1']);
    $vihkoRow['Loppu - Yleinen keruutapahtuma'] = formatDate($rowAssoc['Pvm2']);
  

    /*
    Problem with dates:
    Tiira allows entering conflicting time information, where end time if before start time, or where start time is missing.

    Here we expect that start time is during start date, and end time is during end date.

    What we want to avoid is 
    A) Missing end date, even though there is end time
    B) End date+time combination that is before start date+time combination. 

    */

    // Time

    // If both times are set
    if (!empty($rowAssoc['Kello_hav_1']) && !empty($rowAssoc['Kello_hav_2'])) {

      // Test for case A 
      if (empty($vihkoRow['Loppu - Yleinen keruutapahtuma'])) {
        // Handle case A by filling in end date
        $vihkoRow['Loppu - Yleinen keruutapahtuma'] = $vihkoRow['Alku - Yleinen keruutapahtuma'];
      }

      // Test for case B)
      $tentativeStartDatetime = $vihkoRow['Alku - Yleinen keruutapahtuma'] . formatTime($rowAssoc['Kello_hav_1']);
      $tentativeEndDatetime = $vihkoRow['Loppu - Yleinen keruutapahtuma'] . formatTime($rowAssoc['Kello_hav_2']);

      // Compare that start datetime is before or equal to end datetime
      if ($tentativeStartDatetime <= $tentativeEndDatetime) {
        $vihkoRow['Alku - Yleinen keruutapahtuma'] = $tentativeStartDatetime;
        $vihkoRow['Loppu - Yleinen keruutapahtuma'] = $tentativeEndDatetime;
      }
      else {
        // Handle case B by not including times, since one of them is incorrect
        array_push($keywordsDocument, "havainnon-aika-epäselvä");
        array_push($notesGathering, ("havainnon alkuaika " . $rowAssoc['Kello_hav_1'] . " myöhemmin kuin loppuaika " . $rowAssoc['Kello_hav_2']));
      }
    }
    // If only start time is set
    elseif (!empty($rowAssoc['Kello_hav_1'])) {
      $vihkoRow['Alku - Yleinen keruutapahtuma'] = $vihkoRow['Alku - Yleinen keruutapahtuma'] . formatTime($rowAssoc['Kello_hav_1']);
    }
    // If only end time is set
    elseif (!empty($rowAssoc['Kello_hav_2'])) {
      if (empty($vihkoRow['Loppu - Yleinen keruutapahtuma'])) {
        $vihkoRow['Loppu - Yleinen keruutapahtuma'] = $vihkoRow['Alku - Yleinen keruutapahtuma'];
      }
      $vihkoRow['Loppu - Yleinen keruutapahtuma'] . formatTime($rowAssoc['Kello_hav_2']);
    }
    // else no dates to handle
  
    /*
    // Time end
    if (!empty($rowAssoc['Kello_hav_2'])) {
        // If begin end date is missing, add begin date, because there needs to be an end date if there is an end time. 
        if (empty($vihkoRow['Loppu - Yleinen keruutapahtuma'])) {
            $vihkoRow['Loppu - Yleinen keruutapahtuma'] = formatDate($rowAssoc['Pvm1']);
        }
        $vihkoRow['Loppu - Yleinen keruutapahtuma'] .= formatTime($rowAssoc['Kello_hav_2']);
    }
    */

    // Bird time, in notes field
    $timeBird = "";
    if (!empty($rowAssoc['Kello_lintu_1'])) {
        $timeBird = $rowAssoc['Kello_lintu_1'];
    }
    if (!empty($rowAssoc['Kello_lintu_2'])) {
        $timeBird .= " - " . $rowAssoc['Kello_lintu_2'];
    }
    if (!empty($timeBird)) {
        $timeBird = "linnun havaintoaika: " . $timeBird;
        array_push($notesUnit, $timeBird);
        array_push($keywordsUnit, "linnulla-aika");
    }
  
    // Locality
    $vihkoRow['Kunta - Keruutapahtuma'] = $rowAssoc['Kunta'];
    $vihkoRow['Paikannimet - Keruutapahtuma'] = $rowAssoc['Paikka'];
  
    // Coordinates
    // If there is one coordinate about the bird, expect that there are full coordinates
    if (!empty($rowAssoc['X-koord-linnun'])) {
      $vihkoRow['Koordinaatit@N'] = $rowAssoc['Y-koord-linnun'];
      $vihkoRow['Koordinaatit@E'] = $rowAssoc['X-koord-linnun'];
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
    if (!empty($rowAssoc['Lisätietoja_2'])) {
      array_push($notesUnit, "alihavainnon lisätiedot: " . $rowAssoc['Lisätietoja_2']);
    }
    if (!empty($rowAssoc['Lisätietoja'])) {
      array_push($notesUnit, "havainnon lisätiedot: " . $rowAssoc['Lisätietoja']);
    }
  
    // Atlas
    $vihkoRow['Pesimisvarmuusindeksi - Havainto'] = mapAtlasCode($rowAssoc['Atlaskoodi']);
  
    // Metadata
//    array_push($notesUnit, "tallentanut Tiiraan: " . $rowAssoc['Tallentaja']); // Remove to protect personal data, while allowing to import own observations
    array_push($notesUnit, "tallennettu Tiiraan: " . $rowAssoc['Tallennusaika']);
  
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
    if (!empty($rowAssoc['Määrä'])) {
      $vihkoRow['Määrä - Havainto'] = $rowAssoc['Määrä'];
    }
    else {
      $vihkoRow['Määrä - Havainto'] = 0;
    }

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
    $vihkoRow['Linnun puku - Havainto'] = mapPlumage($rowAssoc['Puku']);

    // Age
    $vihkoRow['Linnun ikä - Havainto'] = mapAge($rowAssoc['Ikä']);

    // Moving (status)
    // This handles status in different way than Vihko so far, by adding direction to moving field
    $vihkoRow['Linnun tila - Havainto'] = str_replace(",", ";", $rowAssoc['Tila']);
    
    // Flock id (This seems to be unique ID in Tiira, so put it into id field.)
    if (!empty($rowAssoc['Parvi'])) {
      array_push($identifiersUnit, ("parvi:" . $rowAssoc['Parvi']));
//      array_push($notesUnit, "parvi " . $rowAssoc['Parvi']);
    }

    // Twitched
    if ("X" == $rowAssoc['Bongattu']) {
      $vihkoRow['Bongattu - Havainto'] = "Kyllä"; 
    }

    // Breeding
//    echo "pesinta: " . $rowAssoc['Pesintä']; // debug
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


    // Keywords for all documents
    array_push($keywordsDocument, "tiira.fi"); // Source
    array_push($keywordsDocument, "import"); // Action
    array_push($keywordsDocument, "tiira2vihko"); // Tool

    $vihkoRow['Avainsanat - Havaintoerä'] = implode(";", $keywordsDocument);
    $vihkoRow['Muut tunnisteet - Havainto'] = implode(";", $identifiersUnit);

    if (!empty($notesGathering)) {
//      $notesGathering = array_filter($notesGathering, !empty($value)); // This SHOULD (not tested) remove empty itemsvalues from array, but it's not needed here, because values are not pushed into the array anymore if they do not exists.
      $vihkoRow['Lisätiedot - Keruutapahtuma'] = implode(" / ", $notesGathering);
    }
    if (!empty($keywordsUnit)) {
      $vihkoRow['Kokoelma/Avainsanat - Havainto'] = implode(";", $keywordsUnit);
    }
    if (!empty($notesUnit)) {
      $vihkoRow['Lisätiedot - Havainto'] = implode(" / ", $notesUnit);
    }
    
    return $vihkoRow;
  }

/*
  function isValue($value) {
    if (empty($value)) { return TRUE; } return FALSE;
//    { return !is_null($value) && $value !== ''; }
  }
*/
  function mapAge($tiiraAge) {
    if (!empty($tiiraAge)) {
      $mapAge = Array();
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
      $mapAge["pp"] = "pp (pesäpoikanen)";  
  
      if (isset($mapAge[$tiiraAge])) {
        return "$mapAge[$tiiraAge]";
      }
      else {
        return $tiiraAge;
      }
    }
    else {
      return "";
    }
  }


  function mapPlumage($tiiraPlumage) {
    if (!empty($tiiraPlumage)) {

      $mapPlumage["ad"] = "MY.birdPlumageAd"; 
      $mapPlumage["eijp"] = "MY.birdPlumageEijp"; 
      $mapPlumage["imm"] = "MY.birdPlumageImm"; 
      $mapPlumage["jp"] = "MY.birdPlumageJp"; 
      $mapPlumage["juv"] = "MY.birdPlumageJuv"; 
      $mapPlumage["n-puk"] = "MY.birdPlumageNpuk"; 
      $mapPlumage["pull"] = "MY.birdPlumagePull"; 
      $mapPlumage["subad"] = "MY.birdPlumageSubad"; 
      $mapPlumage["tp"] = "MY.birdPlumageTp"; 
      $mapPlumage["vp"] = "MY.birdPlumageVp"; 
      $mapPlumage["pep"] = "MY.birdPlumagePep"; 
      $mapPlumage["ss"] = "MY.birdPlumageSs"; 
      
      if (isset($mapPlumage[$tiiraPlumage])) {
        return "$mapPlumage[$tiiraPlumage]";
      }
      else {
        return $tiiraPlumage;
      }
    }
    else {
      return "";
    }
  }

  function mapAtlasCode($tiiraCode) {
    if (!empty($tiiraCode)) {
      return ("MY.atlasCodeEnum" . $tiiraCode);
    }
    else {
      return "";
    }
  }

  function formatTime($time) {
    $pieces = explode(":", $time);
    $timeFormatted = ("T" . $pieces[0] . ":" . $pieces[1]);
//    echo "F: " . $timeFormatted . "\n"; // debug
    return $timeFormatted;
  }

  function formatDate($date) {
    if (empty($date)) {
      return "";
    }
    $pieces = explode(".", $date);
    $dateFormatted = $pieces[2] . "-" . $pieces[1] . "-" . $pieces[0];
//    echo "D: " . $dateFormatted . "\n"; // debug
    return $dateFormatted;
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
  
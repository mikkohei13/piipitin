<?php

/*
Todo:
pagination (how would I like to use it?)
curl with error code detection & handling
*/

class finbif
{
  private $apiToken = NULL;
  private $personToken = NULL;

  public function __construct($apiToken, $personToken) {
    $this->apiToken = $apiToken;
    $this->personToken = $personToken;
  }

  public function mySpecies() {
    $url = "https://api.laji.fi/v0/warehouse/query/unit/aggregate?aggregateBy=unit.linkings.originalTaxon.id&geoJSON=false&onlyCount=true&pairCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=1000&page=1&cache=false&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&time=2020&individualCountMin=1&qualityIssues=NO_ISSUES&observerPersonToken=" . $this->personToken . "&access_token=" . $this->apiToken;
    
    return $this->getFromApi($url);
  }

  public function allSpecies() {

    $lat = 60.18;
    $lon = 24.70;
    $latMin = $lat - 0.5;
    $latMax = $lat + 0.5;
    $lonMin = $lon - 1;
    $lonMax = $lon + 1;
    $coordinatesParam = "&coordinates=" . $latMin . "%3A" . $latMax . "%3A" . $lonMin . "%3A" . $lonMax . "%3AWGS84%3A1";

    $url = "https://api.laji.fi/v0/warehouse/query/unit/aggregate?aggregateBy=unit.linkings.originalTaxon.id&geoJSON=false&onlyCount=true&pairCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=2000&page=1&cache=false&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&taxonRankId=MX.species&countryId=ML.206&time=-60%2F0&individualCountMin=1" . $coordinatesParam . "&qualityIssues=NO_ISSUES&access_token=" . $this->apiToken;
    
    return $this->getFromApi($url);
  }

  public function debug($arr) {
    echo "<pre>";
    print_r ($arr);
    echo "</pre>";
  }

  public function emptyWhenMissing($string = "") {
    return $string;
  }

  public function getTaxonName($taxonId) {
    $taxonId = str_replace("http://tun.fi/", "", $taxonId);
    $url = "https://api.laji.fi/v0/taxa/" . $taxonId . "?lang=fi&langFallback=true&maxLevel=0&includeHidden=false&includeMedia=false&includeDescriptions=false&includeRedListEvaluations=false&sortOrder=taxonomic&access_token=" . $this->apiToken;
    $data = $this->getFromApi($url, "taxon-" . $taxonId);

    $taxonName = $this->emptyWhenMissing(@$data['vernacularName']) . " (<em>" . $data['scientificName'] . "</em>)";
    return $taxonName;
  }

  public function compareTaxa() {
    $set = $this->allSpecies();
    $subset = $this->mySpecies();

//    $this->debug($subset);

    $subsetArr = Array();
    foreach($subset['results'] as $nro => $taxonArr) {
      $subsetArr[$taxonArr['aggregateBy']['unit.linkings.originalTaxon.id']] = true;
    }

//    $this->debug($subsetArr);

    $i = 1;
    echo "<table>";
    foreach($set['results'] as $nro => $taxon) {
      $taxonId = $taxon['aggregateBy']['unit.linkings.originalTaxon.id'];
      $taxonName = $this->getTaxonName($taxonId);

      $class = "non-observed";
      if (isset($subsetArr[$taxonId])) {
        $class = "observed";
      }
//      print_r ($taxon);

      echo "<tr class=\"$class\">";
      echo "<td>";
      echo $i;
      echo "</td><td>";
      echo $taxonName;
      echo "</td><td>";
      echo $taxonId;
      echo "</td><td>";

      echo $taxon['count'];
      echo "</td>";
      echo "</tr>\n";

      $i++;
    }
    echo "</table>";

  }

  // Returns array
  public function myDocumentsByYear() {
    $url = "https://api.laji.fi/v0/documents/count/byYear?personToken=" . $this->personToken . "&access_token=" . $this->apiToken;

    return $this->getFromApi($url);
  }
    
  public function myDocuments($year) {
    // todo: year validation 1000-current

    $url = "https://api.laji.fi/v0/documents?observationYear=" . $year . "&templates=false&personToken=" . $this->personToken . "&access_token=" . $this->apiToken;

    $dataArr = $this->getPages($url, 1, 10);
    if (FALSE !== $dataArr) {
      return $dataArr;
    }
    else {
      echo "Error getting data form API, please check your request.";
    }
    
  }

  // Returns array
  public function personByToken($personToken) {
    $url = "https://api.laji.fi/v0/person/" . $personToken . "?&access_token=" . $this->apiToken;
    return $this->getFromApi($url);
  }

  // Returns string
  public function personName($personId) {

    // If not person id
    // Move to somewhere?
    if (0 !== strpos($personId, "MA.")) {
      return $personId;
    }

    $url = "https://api.laji.fi/v0/person/by-id/" . $personId . "?&access_token=" . $this->apiToken;
    $responseArr = $this->getFromApi($url, "person-" . $personId);
    $name = $responseArr['fullName'];
    return $name;
  }

  // Returns array
  public function taxon($taxonId) {

    $url = "https://api.laji.fi/v0/taxa/" . $taxonId . "?lang=fi&langFallback=true&maxLevel=0&selectedFields=scientificName%2CscientificNameAuthorship%2CtaxonRank%2CvernacularName%2CtaxonomicOrder%2CredListStatusesInFinland%2Cparent&includeHidden=false&includeMedia=false&includeDescriptions=false&includeRedListEvaluations=false&sortOrder=taxonomic&access_token=" . $this->apiToken;

    $arr = $this->getFromApi($url, "taxon-" . $taxonId);

    // 2019 red list status
    if ("MX.species" == $arr['taxonRank'] && isset($arr['redListStatusesInFinland'])) {
      foreach ($arr['redListStatusesInFinland'] as $nro => $list) {
        if (2019 === $list['year']) {
          $arr['redList2019'] = str_replace("MX.iucn", "", $list['status']);
          break;
        }
        else {
          $arr['redList2019'] = "Not included in 2019";
        }
      }
    }
    else {
      $arr['redList2019'] = "";
    }

    return $arr;
  }

  // Generic functions

  private function getPages($baseUrl, $firstPage, $pageSize) {
    $page = $firstPage;
    $dataArr = Array();

    $pagesLimit = 100; // debug
    $sleepSecondsBetweenPages = 3;

    while ($page <= $pagesLimit) {
      log2("NOTICE", "Handling page $page", LOG_DIR."/havistin.log");

      $pagedUrl = $baseUrl . "&pageSize=$pageSize&page=$page";
      $responseArr = $this->getFromApi($pagedUrl);
      $dataArr = array_merge($dataArr, $responseArr['results']);

      $page++;

      // Last page
      if (0 == $responseArr['total'] || $responseArr['currentPage'] >= $responseArr['lastPage']) {
        break;
      }

      sleep($sleepSecondsBetweenPages);
    }

    log2("NOTICE", "Last page was " . ($page-1), LOG_DIR."/havistin.log");

    return $dataArr;
  }

  private function getFromApi($url, $cache = FALSE) {
    // No-cache
    if (FALSE == $cache) {
//      log2("D", "getFromApi no-cache: $url", LOG_DIR."/havistin.log");

      $response = $this->getByCurl($url);
    }
    // Cache
    // Use $cache as a base for hashed filename
    else {
      $cacheFilename = LOG_DIR."/" . sha1($cache) . ".json"; // todo: cache folder

      if (file_exists($cacheFilename)) { // && file age not above limit
//        log2("D", "getFromApi read from cache $cache, url: $url", LOG_DIR."/havistin.log");
  
        $response = file_get_contents($cacheFilename);
      }
      else {
//        log2("D", "getFromApi write to cache $cache, url: $url", LOG_DIR."/havistin.log");
  
        $response = $this->getByCurl($url);
        file_put_contents($cacheFilename, $response);
      }
    }

    if (FALSE == $response) {
      log2("ERROR", "Error getting data from API or cache", LOG_DIR."/havistin.log");
      return FALSE;
    }

    $arr = json_decode($response, TRUE);
    return $arr;
  }

  private function getByCurl($url) {
    log2("NOTICE", "GET " . $url, LOG_DIR."/havistin.log");

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $curlInfo = curl_getinfo($ch);
    curl_close($ch);
  
    //  print_r ($curlInfo); // debug

    // todo: move error handling away from this class
    if (200 == $curlInfo['http_code']) {
      log2("NOTICE", "API responded " . $curlInfo['http_code'], LOG_DIR."/havistin.log");
    }
    elseif (400 == $curlInfo['http_code']) {
      echo "Kirjautuminen vanhentunut - Ole hyvä ja <a href='login/'>kirjaudu uudelleen</a><br><br>"; // Or some other error, don't show to user
      log2("ERROR", "API responded " . $curlInfo['http_code'] . " / " . $response, LOG_DIR."/havistin.log");
    }
    else {
      echo "Virhe - Kokeile <a href='login/'>kirjautua uudelleen</a><br><br>";
      log2("ERROR", "API responded " . $curlInfo['http_code'] . " / " . $response, LOG_DIR."/havistin.log");
    }

    return $response;
    
  }

  public function test() {
    $url = "https://api.laji.fi/v0/person/" . $this->personToken . "/profile?access_token=" . $this->apiToken;
    $personMe = $this->getFromApi($url);
    if ($personMe) {
      print_r($personMe);
    }
    else {
      echo "Error getting data form API, please check your request.";
    }
  }


}
?>
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

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $curlInfo = curl_getinfo($ch);
    curl_close($ch);
  
    //  print_r ($curlInfo); // debug

    // todo: move error handling away from this class
    if (200 != $curlInfo['http_code']) {
      echo "Kirjautuminen vanhentunut tai muu virhe - kokeile kirjautua uudelleen <br><br>"; // Or some other error, don't show to user
      log2("ERROR", "API responded " . $curlInfo['http_code'] . " / " . $response, LOG_DIR."/havistin.log");
    }
    else {
      log2("NOTICE", "API responded " . $curlInfo['http_code'], LOG_DIR."/havistin.log");
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
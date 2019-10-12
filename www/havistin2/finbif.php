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
    foreach ($arr['redListStatusesInFinland'] as $nro => $list) {
      if (2019 === $list['year']) {
        $arr['redList2019'] = str_replace("MX.iucn", "", $list['status']);
        break;
      }
    }

    return $arr;
  }

  // Generic functions

  private function getPages($baseUrl, $firstPage, $pageSize) {
    $page = $firstPage;
    $dataArr = Array();

    $pagesLimit = 1; // debug

    while ($page <= $pagesLimit) {
      log2("NOTICE", "Handling page $page", "logs/havistin.log");

      $pagedUrl = $baseUrl . "&pageSize=$pageSize&page=$page";
      $responseArr = $this->getFromApi($pagedUrl);
      $dataArr = array_merge($dataArr, $responseArr['results']);

      // Last page
      if (0 == $responseArr['total'] || $responseArr['currentPage'] >= $responseArr['lastPage']) {
        break;
      }

      $page++;
    }
    log2("NOTICE", "Last page was $page", "logs/havistin.log");

    return $dataArr;
  }

  private function getFromApi($url, $cache = FALSE) {
    // No-cache
    if (FALSE == $cache) {
//      log2("D", "No-cache: $url", "logs/havistin.log");

      $response = file_get_contents($url);
    }
    // Cache
    // Use $cache as a base for hashed filename
    else {
      $cacheFilename = "logs/" . sha1($cache) . ".json"; // todo: cache folder

      if (file_exists($cacheFilename)) { // && file age not above limit
//        log2("D", "Read from cache $cache, url: $url", "logs/havistin.log");
  
        $response = file_get_contents($cacheFilename);
      }
      else {
//        log2("D", "Write to cache $cache, url: $url", "logs/havistin.log");
  
        $response = file_get_contents($url);
        file_put_contents($cacheFilename, $response);
      }
    }

    if (FALSE == $response) {
      log2("ERROR", "Error getting data from API or cache", "logs/havistin.log");
      return FALSE;
    }

    $arr = json_decode($response, TRUE);
    return $arr;
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
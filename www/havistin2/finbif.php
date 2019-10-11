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
      print_r($dataArr);
    }
    else {
      echo "Error getting data form API, please check your request.";
    }
    
  }

  private function getPages($baseUrl, $firstPage, $pageSize) {
    $page = $firstPage;
    $dataArr = Array();

    while ($page < 1000) {
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

  private function getFromApi($url) {
    log2("NOTICE", "GET $url", "logs/havistin.log");

    $response = file_get_contents($url);
      if (FALSE == $response) {
      return FALSE;
    }
    else {
      $arr = json_decode($response, TRUE);
      return $arr;
    }

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
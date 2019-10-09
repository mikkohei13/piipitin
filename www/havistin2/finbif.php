<?php

class finbif
{
    private $apiToken = NULL;
    private $personToken = NULL;

    public function __construct($apiToken, $personToken) {
        $this->apiToken = $apiToken;
        $this->personToken = $personToken;
    }

    public function test() {
        $url = "https://api.laji.fi/v0/person/" . $this->personToken . "/profile?access_token=" . $this->apiToken;
        $json = file_get_contents($url);
        $arr = json_decode($json, TRUE);
        print_r($arr);
    }

}
?>
<?php
/*
Notes:
- default page size seems to be 100, even though docs say its 20. If no page size is set, pagination metadata is incorrect.

What are names that contain parenthesis? Subgenera? e.g. 
http://tun.fi/MX.228146
http://tun.fi/MX.228153

Does this have empty finnish name?
http://tun.fi/MX.53714

Does this have english name as finnish:
http://tun.fi/MX.5012927
*/

require_once "../config/env.php";

define("DEBUG", false);

$params = Array();

$params['pageSize'] = 100;
$params['page'] = 500; // start page

$pageLimit = 10; // DEBUG
$pagesHandled = 0;

$morePages = TRUE;

while($morePages) {
    $url = buildTaxonQuery($params);
    $dataJSON = getDataFromLajifi($url);
    $dataArr = json_decode($dataJSON, TRUE);

    // Do something with $dataArr
    printNames($dataArr['results']);

    if ($dataArr['currentPage'] >= $dataArr['lastPage']) {
        // Last page, stop processing
        $morePages = FALSE;
    }
    elseif ($pagesHandled >= $pageLimit) {
        // Reached limit, stop processing
        $morePages = FALSE;
    }
    else {
        // Get next page
        $params['page']++;
        $pagesHandled++;
    }
}

function printNames($results) {
    foreach ($results as $nameKey => $nameArr) {
        debugData($nameArr, __LINE__);

        // If no vernacular name, skip
        if (!isset($nameArr['vernacularName']) || empty($nameArr['vernacularName'])) {
            continue;
        }

        // Synonyms
        $synonyms = "";
        if (isset($nameArr['synonyms'])) {
            foreach ($nameArr['synonyms'] as $synonymKey => $synonymArr) {
                $synonyms = $synonyms . "\t" . $synonymArr['scientificName'];
            }
            $synonyms = trim($synonyms);
        }

        echo "http://tun.fi/" . $nameArr['id'] . "\t" . $nameArr['scientificName'] . "\t" . $nameArr['vernacularName'] . "\t" . $synonyms . "\n";
    }
}

// ------------------------------------------------

// Get data from an api and return as JSON
function getDataFromLajifi($url) {
    $responseJSON = file_get_contents($url);
    if ($responseJSON === FALSE) {
        $errorMessage = "Error fetching data from api.laji.fi";
//        logger("lajifi.log", "error", $errorMessage);
        exit($errorMessage);
    }
    return $responseJSON;
}


/*
Build and return query URL for a list of taxa

MH:
https://api.laji.fi/v0/taxa?lang=multi&langFallback=false&includeHidden=false&includeMedia=false&includeDescriptions=false&includeRedListEvaluations=false&sortOrder=taxonomic&page=1&pageSize=100&access_token=

VR:
https://laji.fi/api/taxa?selectedFields=vernacularName,id,scientificName,synonyms.scientificName&lang=fi
*/
function buildTaxonQuery($params) {
    $url = "https://laji.fi/api/taxa?selectedFields=vernacularName,id,scientificName,synonyms.scientificName&lang=fi&pageSize=" . $params['pageSize'] . "&page=" . $params['page'];
    debugData($url, __LINE__);
    return $url;
}

function debugData($data, $line = "?") {
    if (DEBUG) {
        if (is_array($data)) {
            echo "<pre>LINE $line: \n";
            print_r ($data);
            echo "\n\n";
        }
        else {
            echo "<pre>LINE $line: $data \n\n";
        }
    }
}



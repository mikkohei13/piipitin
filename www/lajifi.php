<?php

// Get data from an api and return as JSON
function getDataFromLajifi($url) {
    $responseJSON = file_get_contents($url);
    return $responseJSON;
}

/*
Build and return query URL for a list of units.

https://api.laji.fi/v0/warehouse/query/list?selected=document.collectionId%2Cdocument.createdDate%2Cdocument.documentId%2Cdocument.editorUserIds%2Cdocument.firstLoadDate%2Cdocument.formId%2Cdocument.loadDate%2Cdocument.modifiedDate%2Cdocument.sourceId%2Cgathering.biogeographicalProvince%2Cgathering.conversions.wgs84CenterPoint.lat%2Cgathering.conversions.wgs84CenterPoint.lon%2Cgathering.country%2Cgathering.displayDateTime%2Cgathering.eventDate.begin%2Cgathering.gatheringId%2Cgathering.interpretations.coordinateAccuracy%2Cgathering.linkings.observers.fullName%2Cgathering.locality%2Cgathering.municipality%2Cgathering.team%2Cunit.linkings.taxon.finnish%2Cunit.linkings.taxon.scientificName%2Cunit.reportedTaxonId%2Cunit.taxonVerbatim%2Cunit.unitId&orderBy=document.firstLoadDate&pageSize=20&page=1&cache=false&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&countryId=ML.206&collectionId=HR.1747&individualCountMin=1&firstLoadedSameOrAfter=2019-02-28%20DESC&qualityIssues=NO_ISSUES&access_token=
*/
function buildListQuery($token) {

    // Selected fields
    $selectedArr = Array(
        "document.collectionId",
        "document.createdDate",
        "document.documentId",
        "document.editorUserIds",
        "document.firstLoadDate",
        "document.formId",
        "document.loadDate",
        "document.modifiedDate",
        "document.sourceId",
        "gathering.biogeographicalProvince",
        "gathering.conversions.wgs84CenterPoint.lat",
        "gathering.conversions.wgs84CenterPoint.lon",
        "gathering.country",
        "gathering.displayDateTime",
        "gathering.eventDate.begin",
        "gathering.gatheringId",
        "gathering.interpretations.coordinateAccuracy",
        "gathering.linkings.observers.fullName",
        "gathering.locality",
        "gathering.municipality",
        "gathering.team",
        "unit.linkings.taxon.finnish",
        "unit.linkings.taxon.scientificName",
        "unit.reportedTaxonId",
        "unit.taxonVerbatim",
        "unit.unitId"
    );
    $selected = join("%2C", $selectedArr);

    // Settings
    $orderBy = "document.firstLoadDate";
    $orderDirection = "DESC"; // DESC / ASC
    $limit = 100;

    // Filters
    // To show only fresh observations, add time filter. Now returns also old observations entred today.

    $collectionIdQname = "HR.1747";
    $countryIdQname = ""; // ML.206 = Suomi
    $date = date("Y-m-d");
//        $date = date("2019-02-28"); // debug

    $url = "https://api.laji.fi/v0/warehouse/query/list?selected=" . $selected . "&orderBy=" . $orderBy . "%20" . $orderDirection . "&pageSize=" . $limit . "&page=1&cache=false&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&collectionId=" . $collectionIdQname . "&countryId=" . $countryIdQname . "&firstLoadedSameOrAfter=" . $date . "&qualityIssues=NO_ISSUES&access_token=" . $token;

//    echo "URL: " . $url; // debug

    return $url;
}

// Build and return query URL for an aggregate.
/*
function buildAggregateQuery($token, $date) {

    // Date has to be in format 2018-12-24
    $url = "https://api.laji.fi/v0/warehouse/query/aggregate?aggregateBy=document.collectionId&geoJSON=false&onlyCount=true&pairCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=100&page=1&cache=false&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&individualCountMin=1&firstLoadedSameOrAfter=" . $date . "&qualityIssues=NO_ISSUES&access_token=" . $token;

    return $url;
}
*/

// Removes handled elements based on documentId
function filterHandledUnits($dataArr) {

    $latestId = getLatestId(LATESTID_FILENAME);
    $newestId = FALSE;
    $dataArrFiltered = Array();

    // todo: move removin metadata elsewhere
    foreach($dataArr as $i => $element) {

        $documentId = $element['document']['documentId'];

        // Pick only the first id
        if ($newestId === FALSE) {
            $newestId = $documentId;
        }

        if ($documentId == $latestId) {
            logger("lajifi.log", "info", ("Break after finding handled document id " . $element['document']['documentId']));
            break;
        }

        $dataArrFiltered[$i] = $element;

    }

    // Set latest id only if you really have one
    if ($newestId !== FALSE) {
        setLatestId(LATESTID_FILENAME, $newestId);
    }
    
    return $dataArrFiltered;
}

function getLatestId($filename) {

    // Allow overriding
    if (isset($_GET["debugLatestId"])) {
        return $_GET["debugLatestId"];
    }

    $fileContents = file_get_contents("data/" . $filename);
    return trim($fileContents);
}

function setLatestId($filename, $id) {
    return file_put_contents("data/" . $filename, $id);
}



// Build and return an array of documents with their basic data
// TODO: move latestid handling away from here (one task per function)
function buildDocumentList($dataArr) {

//    echo "<pre>"; print_r ($dataArr); echo "</pre>"; // debug
    $dataArr = filterHandledUnits($dataArr);

    $data = Array();
    foreach($dataArr as $i => $element) {

        // Shorthands
        $doc = $element['document'];
        $gat = $element['gathering'];
        $uni = $element['unit'];

        // Basic data can be overwritten per gathering, since it cannot vary
        $locality = $gat['country'] . ", " . $gat['biogeographicalProvince'] . ", " . $gat['municipality'] . ", " . $gat['locality'];
        $date = $gat['eventDate']['begin'];

        $team = "";
        foreach ($gat['team'] as $n => $name) {
            $team = $team . ", " . $name;
        }
        $team = trim($team, ", ");

        // Counting units
        @$data[$doc['documentId']][$gat['gatheringId']]['unitCount'] += 1; // Supress "Undefined index" errors 

        $data[$doc['documentId']][$gat['gatheringId']]['locality'] = $locality;
        $data[$doc['documentId']][$gat['gatheringId']]['date'] = $date;
        $data[$doc['documentId']][$gat['gatheringId']]['team'] = $team;
    }

    echo "<pre style='color: green;'>"; print_r ($data); echo "\n\n"; echo "</pre>"; // debug

    logger("lajifi.log", "info", ("Handled " . count($data) . " documents"));

    return $data;
}

function formatMessageDataToPlaintext($docId, $data) {

    $txt = "";

    foreach ($data as $gatId => $gat) {
        $txt .= trim($gat['locality'], ", ") . "\n";
        $txt .= "- " . $gat['unitCount'] . " havainto(a)\n";
        $txt .= "- " . $gat['date'] . "\n";
        $txt .= "- " . $gat['team'] . "\n";
    }

    $txt .= $docId;

//    $txt = "<pre>\n" . $txt . "\n</pre>"; // debug, for displaying in browser

    return substr($txt, 0, 1024); // Limit character count, just in case
}

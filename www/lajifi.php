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
//    $date = date("2019-02-20"); // debug

    $url = "https://api.laji.fi/v0/warehouse/query/list?selected=" . $selected . "&orderBy=" . $orderBy . "%20" . $orderDirection . "&pageSize=" . $limit . "&page=1&cache=false&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&collectionId=" . $collectionIdQname . "&countryId=" . $countryIdQname . "&firstLoadedSameOrAfter=" . $date . "&qualityIssues=NO_ISSUES&access_token=" . $token;

//    echo "URL: " . $url; // debug

    return $url;
}

// Build and return query URL for an aggregate.
function buildAggregateQuery($token, $date) {

    // Date has to be in format 2018-12-24
    $url = "https://api.laji.fi/v0/warehouse/query/aggregate?aggregateBy=document.collectionId&geoJSON=false&onlyCount=true&pairCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=100&page=1&cache=false&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&individualCountMin=1&firstLoadedSameOrAfter=" . $date . "&qualityIssues=NO_ISSUES&access_token=" . $token;

    return $url;
}

// Build and return an array of documents with their basic data
// TODO: move latestid handling away from here (one task per function)
function buildDocumentList($json) {

    $arr = json_decode($json, TRUE);
//    echo "<pre>"; print_r ($arr); echo "</pre>"; // debug

    $latestId = getLatestId(LATESTID_FILENAME);
    $newestId = FALSE;

    $data = Array();
    foreach($arr['results'] as $i => $element) {

        if ($element['document']['documentId'] == $latestId) {
            logger("lajifi.log", "info", ("Break after finding handled document id " . $element['document']['documentId']));
            break;
        }

        // Shorthands
        $doc = $element['document'];
        $gat = $element['gathering'];
        $uni = $element['unit'];

        // Pick only the first id
        if ($newestId === FALSE) {
            $newestId = $doc['documentId'];
        }

        // Basic data can be overwritten per gathering, since it cannot vary
        $locality = $gat['country'] . ", " . $gat['biogeographicalProvince'] . ", " . $gat['municipality'] . ", " . $gat['locality'];
        $date = $gat['eventDate']['begin'];

        $team = "";
        foreach ($gat['team'] as $n => $name) {
            $team = $team . ", " . $name;
        }
        $team = trim($team, ", ");

        // Counting units
        @$data[$doc['documentId']][$gat['gatheringId']]['unitCOunt'] += 1; // Supress "Undefined index" errors 

        $data[$doc['documentId']][$gat['gatheringId']]['locality'] = $locality;
        $data[$doc['documentId']][$gat['gatheringId']]['date'] = $date;
        $data[$doc['documentId']][$gat['gatheringId']]['team'] = $team;
    }

    // Set latest id only if you really have one
    if ($newestId !== FALSE) {
        setLatestId(LATESTID_FILENAME, $newestId);
    }

    echo "<pre style='color: green;'>"; print_r ($data); echo "\n\n"; echo "</pre>"; // debug

    logger("lajifi.log", "info", ("Handled " . count($data) . " documents"));

    return $data;
}

function getLatestId($filename) {
    $fileContents = file_get_contents("data/" . $filename);
    return trim($fileContents);
}

function setLatestId($filename, $id) {
    return file_put_contents("data/" . $filename, $id);
}

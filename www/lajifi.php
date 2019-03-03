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
        "gathering.conversions.year",
        "gathering.conversions.dayOfYearBegin",
        "gathering.conversions.dayOfYearEnd",
        "gathering.country",
        "gathering.displayDateTime",
        "gathering.eventDate.begin",
        "gathering.gatheringId",
        "gathering.interpretations.coordinateAccuracy",
        "gathering.linkings.observers.fullName",
        "gathering.locality",
        "gathering.municipality",
        "gathering.team",
        "unit.linkings.taxon.id",
        "unit.linkings.taxon.qname",
        "unit.linkings.taxon.finnish",
        "unit.linkings.taxon.scientificName",
        "unit.linkings.taxon.taxonRank",
        "unit.reportedTaxonId",
        "unit.taxonVerbatim",
        "unit.unitId"
    );
    $selected = join("%2C", $selectedArr);

    // Settings
    $orderBy = "document.firstLoadDate";
    $orderDirection = "DESC"; // DESC / ASC
    $limit = 100;
    $limit = 10; // debug

    // Filters
    // To show only fresh observations, add time filter. Now returns also old observations entred today.

    $collectionIdQname = "HR.1747";
    $countryIdQname = ""; // ML.206 = Suomi
    $date = date("Y-m-d");
//        $date = date("2019-02-28"); // debug

    $url = "https://api.laji.fi/v0/warehouse/query/list?selected=" . $selected . "&orderBy=" . $orderBy . "%20" . $orderDirection . "&pageSize=" . $limit . "&page=1&cache=false&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&collectionId=" . $collectionIdQname . "&countryId=" . $countryIdQname . "&firstLoadedSameOrAfter=" . $date . "&qualityIssues=NO_ISSUES&access_token=" . $token;

    echo "URL built: " . $url . "\n"; // debug

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



function addRarityScore($dataArr) {

    //    echo "<pre>"; print_r ($dataArr); echo "</pre>"; // debug
    $dataArr = filterHandledUnits($dataArr);

    $data = Array();
    foreach($dataArr as $i => $element) {

        // Remove and skipt non-species
        if ($element['unit']['linkings']['taxon']['taxonRank'] != "http://tun.fi/MX.species") {
            unset($dataArr[$i]);
            continue;
        }

        $rarityScore = 0;

        // --------------------------------------
        // Observations from Finland
        $speciesObservationCount = 0;
        $rawDataArr = json_decode(getDataFromLajifi(buildSpeciesAggregateQuery_Finland($element['unit']['linkings']['taxon']['id'])), TRUE);
//        echo "\n\nAggregate query data:\n"; print_r ($rawDataArr); //continue; // debug

        $speciesObservationCount = $rawDataArr['results'][0]['count'];
        if (!isset($speciesObservationCount)) {
            $speciesObservationCount = 0; // Is this needed?
        }

        $limit = 100; // prod
        $limit = 1000; // debug
        if ($speciesObservationCount < $limit)
        {
            $rarityScore += ($limit - $speciesObservationCount);
            $dataArr[$i]['rarityScore']['finland'] = ($limit - $speciesObservationCount);
        }

        // --------------------------------------
        // Observations from biogeo province
        $speciesObservationCount = 0;
        $rawDataArr = json_decode(getDataFromLajifi(buildSpeciesAggregateQuery_Area($element['unit']['linkings']['taxon']['id'], $element['gathering']['biogeographicalProvince'])), TRUE);
//        echo "\n\nAggregate query data:\n"; print_r ($rawDataArr); //continue; // debug

        $speciesObservationCount = $rawDataArr['results'][0]['count'];
        if (!isset($speciesObservationCount)) {
            $speciesObservationCount = 0; // Is this needed?
        }

        $limit = 10; // prod
        $limit = 100; // debug
        if ($speciesObservationCount < $limit)
        {
            $rarityScore += ($limit - $speciesObservationCount);
            $dataArr[$i]['rarityScore']['biogeo'] = ($limit - $speciesObservationCount);
        }

        // --------------------------------------
        // Observations from this year, only if obs is from this year also
        if ($element['gathering']['conversions']['year'] == date("Y")) {
            $speciesObservationCount = 0;
            $rawDataArr = json_decode(getDataFromLajifi(buildSpeciesAggregateQuery_Year($element['unit']['linkings']['taxon']['id'], date("Y"))), TRUE);
//            echo "\n\nAggregate query data ".__LINE__.":\n"; print_r ($rawDataArr); //continue; // debug
    
            $speciesObservationCount = $rawDataArr['results'][0]['count'];
            if (!isset($speciesObservationCount)) {
                $speciesObservationCount = 0; // Is this needed?
            }
    
            $limit = 5; // prod
            $limit = 10; // debug
            if ($speciesObservationCount < $limit)
            {
                $rarityScore += ($limit - $speciesObservationCount);
                $dataArr[$i]['rarityScore']['year'] = ($limit - $speciesObservationCount);
            }    
        }

        // --------------------------------------
        // Observations around day of year, only if date is exact
        // todo: skip this is dead, indirect or not growing
        if ($element['gathering']['conversions']['dayOfYearBegin'] == $element['gathering']['conversions']['dayOfYearEnd']) {

            $speciesObservationCount = 0;
            $rawDataArr = json_decode(getDataFromLajifi(buildSpeciesAggregateQuery_Phenology($element['unit']['linkings']['taxon']['id'], $element['gathering']['conversions']['dayOfYearBegin'], 20)), TRUE);
    //        echo "\n\nAggregate query data:\n"; print_r ($rawDataArr); //continue; // debug

            $speciesObservationCount = $rawDataArr['results'][0]['count'];
            if (!isset($speciesObservationCount)) {
                $speciesObservationCount = 0; // Is this needed?
            }

            $limit = 10; // prod
            $limit = 100; // debug
            if ($speciesObservationCount < $limit)
            {
                $rarityScore += ($limit - $speciesObservationCount);
                $dataArr[$i]['rarityScore']['phenology'] = ($limit - $speciesObservationCount);
            }

        }

        $dataArr[$i]['rarityScore']['total'] = $rarityScore;
    }

    return $dataArr;
}

function buildSpeciesAggregateQuery_Finland($taxonId) {
    return "https://api.laji.fi/v0/warehouse/query/aggregate?aggregateBy=unit.linkings.taxon.id&geoJSON=false&onlyCount=true&pairCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=2&page=1&cache=true&taxonId=" . $taxonId . "&useIdentificationAnnotations=true&includeSubTaxa=false&includeNonValidTaxa=true&countryId=ML.206&individualCountMin=1&qualityIssues=NO_ISSUES&access_token=" . LAJIFI_TOKEN;
}

function buildSpeciesAggregateQuery_Area($taxonId, $areaName) {
    return "https://api.laji.fi/v0/warehouse/query/aggregate?aggregateBy=unit.linkings.taxon.id&geoJSON=false&onlyCount=true&pairCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=2&page=1&cache=true&taxonId=" . $taxonId . "&useIdentificationAnnotations=true&includeSubTaxa=false&includeNonValidTaxa=true&area=" . urlencode($areaName) . "&individualCountMin=1&qualityIssues=NO_ISSUES&access_token=" . LAJIFI_TOKEN;
}

function buildSpeciesAggregateQuery_Year($taxonId, $year) {
    return "https://api.laji.fi/v0/warehouse/query/aggregate?aggregateBy=unit.linkings.taxon.id&geoJSON=false&onlyCount=true&pairCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=2&page=1&cache=true&taxonId=" . $taxonId . "&useIdentificationAnnotations=true&includeSubTaxa=false&includeNonValidTaxa=true&time=" . $year . "&individualCountMin=1&qualityIssues=NO_ISSUES&access_token=" . LAJIFI_TOKEN;
}

function buildSpeciesAggregateQuery_Phenology($taxonId, $dayOfYear, $radius) {
    $dayBegin = $dayOfYear - $radius;
    if ($dayBegin < 0) {
        $dayBegin = 365 + $dayBegin;
    }
    $dayEnd = $dayOfYear + $radius;
    if ($dayEnd > 365) {
        $dayEnd = $dayEnd - 365;
    }
    $dayParam = $dayBegin . "%2F" . $dayEnd;

    return "https://api.laji.fi/v0/warehouse/query/aggregate?aggregateBy=unit.linkings.taxon.id&geoJSON=false&onlyCount=true&pairCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=2&page=1&cache=true&taxonId=" . $taxonId . "&useIdentificationAnnotations=true&includeSubTaxa=false&includeNonValidTaxa=true&dayOfYear=" . $dayParam . "&individualCountMin=1&qualityIssues=NO_ISSUES&access_token=" . LAJIFI_TOKEN;
}

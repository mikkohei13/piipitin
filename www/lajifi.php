<?php

// Get data from an api and return as JSON
function getDataFromLajifi($url) {
    $responseJSON = file_get_contents($url);
    if ($responseJSON === FALSE) {
        $errorMessage = "Error fetching data from api.laji.fi";
        logger("lajifi.log", "error", $errorMessage);
        exit($errorMessage);
    }
    return $responseJSON;
}

/*
Build and return query URL for a list of units.

https://api.laji.fi/v0/warehouse/query/list?selected=document.collectionId%2Cdocument.createdDate%2Cdocument.documentId%2Cdocument.editorUserIds%2Cdocument.firstLoadDate%2Cdocument.formId%2Cdocument.loadDate%2Cdocument.modifiedDate%2Cdocument.sourceId%2Cgathering.biogeographicalProvince%2Cgathering.conversions.wgs84CenterPoint.lat%2Cgathering.conversions.wgs84CenterPoint.lon%2Cgathering.country%2Cgathering.displayDateTime%2Cgathering.eventDate.begin%2Cgathering.gatheringId%2Cgathering.interpretations.coordinateAccuracy%2Cgathering.linkings.observers.fullName%2Cgathering.locality%2Cgathering.municipality%2Cgathering.team%2Cunit.linkings.taxon.finnish%2Cunit.linkings.taxon.scientificName%2Cunit.reportedTaxonId%2Cunit.taxonVerbatim%2Cunit.unitId&orderBy=document.firstLoadDate&pageSize=20&page=1&cache=false&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&countryId=ML.206&collectionId=HR.1747&individualCountMin=1&firstLoadedSameOrAfter=2019-02-28%20DESC&qualityIssues=NO_ISSUES&access_token=
*/
function buildListQuery($countryIdQname = "") {

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
        "unit.linkings.taxon.vernacularName",
        "unit.linkings.taxon.taxonRank",
        "unit.reportedTaxonId",
        "unit.taxonVerbatim",
        "unit.unitId"
    );
    $selected = join("%2C", $selectedArr);

    // Settings
    $orderBy = "document.firstLoadDate";
    $orderDirection = "DESC"; // DESC / ASC

    if (DEBUG) {
        $limit = 50;
    }
    else {
        $limit = 50;
    }

    // Filters
    // To show only fresh observations, add time filter. Now returns also old observations entred today.

    $collectionIdQname = "HR.1747";
    $date = date("Y-m-d");

    $url = "https://api.laji.fi/v0/warehouse/query/list?selected=" . $selected . "&orderBy=" . $orderBy . "%20" . $orderDirection . "&pageSize=" . $limit . "&page=1&cache=false&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&collectionId=" . $collectionIdQname . "&countryId=" . $countryIdQname . "&firstLoadedSameOrAfter=" . $date . "&qualityIssues=NO_ISSUES&access_token=" . LAJIFI_TOKEN;

    echo "URL built:\n</pre>" . $url . "<pre>\n"; // debug

    return $url;
}

// Build and return query URL for an aggregate.
/*
function buildAggregateQuery($date) {

    // Date has to be in format 2018-12-24
    $url = "https://api.laji.fi/v0/warehouse/query/aggregate?aggregateBy=document.collectionId&geoJSON=false&onlyCount=true&pairCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=100&page=1&cache=false&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&individualCountMin=1&firstLoadedSameOrAfter=" . $date . "&qualityIssues=NO_ISSUES&access_token=" . LAJIFI_TOKEN;

    return $url;
}
*/

// Removes handled elements based on documentId
function filterHandledUnits($dataArr, $latestIdFilename) {

    $latestId = getLatestId($latestIdFilename);
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
        setLatestId($latestIdFilename, $newestId);
    }
    
    return $dataArrFiltered;
}

function getLatestId($filename) {

    // Allow overriding
    if (!empty($_GET["debug"])) {
        return $_GET["debug"];
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
    $dataArr = filterHandledUnits($dataArr, LATESTID_FILENAME_DOCUMENTS);

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


function addRarityScorePart(&$element, $url, $limit, $slug, $topLabel) { // Passing by reference!
    $rawDataArr = json_decode(getDataFromLajifi($url), TRUE);

    $speciesObservationCount = $rawDataArr['results'][0]['count'];
    if (!isset($speciesObservationCount)) {
        $speciesObservationCount = 0; // Is this needed?
    }

    if ($speciesObservationCount < $limit)
    {
        @$element['rarityScore']['total'] += ($limit - $speciesObservationCount); // @
        @$element['rarityScore'][$slug] = ($limit - $speciesObservationCount); // @
    }
    
    if ($speciesObservationCount <= 1) {
        @$element['rarityScore']['top'] .= $topLabel . ", "; // @
    }

    @$element['rarityScore']['desc'] .= $slug . ": " . $speciesObservationCount . ", "; // @

    //    else { $debugSlug = "debug" . $slug; $element['rarityScore'][$debugSlug] = "debug";} // debug

    // No need to return the result, since $element was passed by reference
    return TRUE;
}


function addRarityScore($dataArr) {

    //    echo "<pre>"; print_r ($dataArr); echo "</pre>"; // debug
    $dataArr = filterHandledUnits($dataArr, LATESTID_FILENAME_RARITIES);

    $data = Array();
    foreach($dataArr as $i => $element) {

        // Remove and skipt non-species
        if ($element['unit']['linkings']['taxon']['taxonRank'] != "http://tun.fi/MX.species" || !isset($element['unit']['linkings']['taxon']['taxonRank'])) {
            unset($dataArr[$i]);
            continue;
        }

        // Passing dataArr by reference!
        // Observations from Finland
        $url = buildSpeciesAggregateQuery_Finland($element['unit']['linkings']['taxon']['id']);
        addRarityScorePart($dataArr[$i], $url, 41, "Suomesta", "Suomen ensimmäinen");

        // Observations from biogeographical province
        $url = buildSpeciesAggregateQuery_Area($element['unit']['linkings']['taxon']['id'], $element['gathering']['biogeographicalProvince']);
        addRarityScorePart($dataArr[$i], $url, 21, "eliömaakunnasta", "eliömaakunnan ensimmäinen");

        // Observations from this year, only if obs is from this year also
        if ($element['gathering']['conversions']['year'] == date("Y")) {
            $url = buildSpeciesAggregateQuery_Year($element['unit']['linkings']['taxon']['id'], date("Y"));
            addRarityScorePart($dataArr[$i], $url, 11, "tältä vuodelta", "vuoden ensimmäinen");
        }

        // Observations around day of year, only if date is exact
        // TODO: skip this is dead, indirect or not growing
        // TODO: allow 5 day periods
        if ($element['gathering']['conversions']['dayOfYearBegin'] == $element['gathering']['conversions']['dayOfYearEnd']) {
            $url = buildSpeciesAggregateQuery_Phenology($element['unit']['linkings']['taxon']['id'], $element['gathering']['conversions']['dayOfYearBegin'], 30);
            addRarityScorePart($dataArr[$i], $url, 21, "kaudelta", "kauden ensimmäinen");
        }

        // Observations from the last decade, only if obs is from last decade
        if ($element['gathering']['conversions']['year'] >= (date("Y") - 10)) {
            $url = buildSpeciesAggregateQuery_Decade($element['unit']['linkings']['taxon']['id']);
            addRarityScorePart($dataArr[$i], $url, 21, "vuosikymmeneltä", "vuosikymmenen ensimmäinen");
        }

        // Trim
        @trim($dataArr[$i]['rarityScore']['top'], ", "); // @
        trim($dataArr[$i]['rarityScore']['desc'], ", ");

    }

    return $dataArr;
}

function buildSpeciesAggregateQuery_Finland($taxonId) {
    return "https://api.laji.fi/v0/warehouse/query/aggregate?aggregateBy=unit.linkings.taxon.id&geoJSON=false&onlyCount=true&pairCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=2&page=1&cache=true&taxonId=" . $taxonId . "&useIdentificationAnnotations=true&includeSubTaxa=false&includeNonValidTaxa=true&countryId=ML.206&individualCountMin=1&qualityIssues=NO_ISSUES&access_token=" . LAJIFI_TOKEN;
}

function buildSpeciesAggregateQuery_Area($taxonId, $areaName) {
    return "https://api.laji.fi/v0/warehouse/query/aggregate?aggregateBy=unit.linkings.taxon.id&geoJSON=false&onlyCount=true&pairCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=2&page=1&cache=true&taxonId=" . $taxonId . "&useIdentificationAnnotations=true&includeSubTaxa=false&includeNonValidTaxa=true&countryId=ML.206&area=" . urlencode($areaName) . "&individualCountMin=1&qualityIssues=NO_ISSUES&access_token=" . LAJIFI_TOKEN;
}

function buildSpeciesAggregateQuery_Year($taxonId, $year) {
    return "https://api.laji.fi/v0/warehouse/query/aggregate?aggregateBy=unit.linkings.taxon.id&geoJSON=false&onlyCount=true&pairCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=2&page=1&cache=true&taxonId=" . $taxonId . "&useIdentificationAnnotations=true&includeSubTaxa=false&includeNonValidTaxa=true&countryId=ML.206&time=" . $year . "&individualCountMin=1&qualityIssues=NO_ISSUES&access_token=" . LAJIFI_TOKEN;
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

    return "https://api.laji.fi/v0/warehouse/query/aggregate?aggregateBy=unit.linkings.taxon.id&geoJSON=false&onlyCount=true&pairCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=2&page=1&cache=true&taxonId=" . $taxonId . "&useIdentificationAnnotations=true&includeSubTaxa=false&includeNonValidTaxa=true&countryId=ML.206&dayOfYear=" . $dayParam . "&individualCountMin=1&qualityIssues=NO_ISSUES&access_token=" . LAJIFI_TOKEN;
}

function buildSpeciesAggregateQuery_Decade($taxonId) {
    $yearParam = (date("Y") - 10) . "%2F" . date("Y");

    return "https://api.laji.fi/v0/warehouse/query/aggregate?aggregateBy=unit.linkings.taxon.id&geoJSON=false&onlyCount=true&pairCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=2&page=1&cache=true&taxonId=" . $taxonId . "&useIdentificationAnnotations=true&includeSubTaxa=false&includeNonValidTaxa=true&countryId=ML.206&time=" . $yearParam . "&individualCountMin=1&qualityIssues=NO_ISSUES&access_token=" . LAJIFI_TOKEN;
}

/*
            [rarityScore] => Array
                (
                    [finland] => 50/51
                    [biogeo] => 10/11
                    [year] => 5/6
                    [phenology] => 10/11
                    [decade] => 10/11
                    [total] => 85
                    [top] => first from Finland, first from province, first this year, first during this season, first during the last decade
                )
*/

function formatRarityDataToPlaintext($element) {

    $txt = "MIELENKIINTOINEN, ";

    $txt .= $element['rarityScore']['total'] . " pistettä\n";
    
    $txt .= $element['unit']['linkings']['taxon']['vernacularName']['fi'] . " (" . $element['unit']['linkings']['taxon']['scientificName'] . ")\n";
    $txt .= $element['gathering']['displayDateTime'] . "\n";

    $locality = $element['gathering']['biogeographicalProvince'] . ", " . $element['gathering']['municipality'] . ", " . $element['gathering']['locality'];
    $txt .= trim($locality . "\n") . "\n";

    if (isset($element['gathering']['team'])) {
        $team = "";
        foreach ($element['gathering']['team'] as $i => $name) {
          $team .= $name . ", ";
        }
        $txt .= trim($team, ", ") . "\n";

    }

    $txt .= $element['document']['documentId'] . "\n";
    $txt .= "Havaintoja: " . $element['rarityScore']['desc'];

    return $txt;
}
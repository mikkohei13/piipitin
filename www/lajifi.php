<?php


function getDataFromLajifi($url) {
    $responseJSON = file_get_contents($url);
    return $responseJSON;
}


/*
https://api.laji.fi/v0/warehouse/query/list?pageSize=100&page=1&cache=false&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&countryId=ML.206&individualCountMin=1&qualityIssues=NO_ISSUES
*/
function buildListQueryOLD($token) {

    // Selected fields
    $selectedArr = Array(
        "document.createdDate",
        "document.documentId",
        "document.loadDate",
        "gathering.biogeographicalProvince",
        "gathering.conversions.wgs84CenterPoint.lat",
        "gathering.conversions.wgs84CenterPoint.lon",
        "gathering.country",
        "gathering.eventDate.begin",
        "gathering.eventDate.end",
        "gathering.gatheringId",
        "gathering.interpretations.biogeographicalProvince",
        "gathering.interpretations.country",
        "gathering.interpretations.finnishMunicipality",
        "gathering.locality",
        "gathering.municipality",
        "gathering.notes",
        "gathering.province",
        "gathering.team",
        "unit.linkings.taxon.qname",
        "unit.linkings.taxon.scientificName",
        "unit.linkings.taxon.vernacularName",
        "unit.unitId"
    );
    $selected = join("%2C", $selectedArr);

    // Settings
    $orderBy = "document.createdDate";
    $orderDirection = "DESC"; // DESC / ASC
    $limit = 10;

    // Filters
    $collectionIdQname = "HR.1747";
    $countryIdQname = "ML.206";

    $url = "https://api.laji.fi/v0/warehouse/query/list?selected=" . $selected . "&orderBy=" . $orderBy . "%20" . $orderDirection . "&pageSize=" . $limit . "&page=1&cache=false&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&individualCountMin=1&qualityIssues=NO_ISSUES&collectionId=" . $collectionIdQname . "&countryId=" . $countryIdQname . "&access_token=" . $token;

 //   $url = "https://api.laji.fi/v0/warehouse/query/list?selected=" . $selected . "&orderBy=" . $orderBy . "%20" . $orderDirection . "&pageSize=" . $limit . "&page=1&collectionId=" . $collectionIdQname . "&access_token=" . $token; // OLD from Telelaitos

    return $url;
}

/*
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
    $countryIdQname = "ML.206";
    $date = date("Y-m-d");
    $date = date("2019-02-20"); // debug

    $url = "https://api.laji.fi/v0/warehouse/query/list?selected=" . $selected . "&orderBy=" . $orderBy . "%20" . $orderDirection . "&pageSize=" . $limit . "&page=1&cache=false&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&countryId=ML.206&collectionId=" . $collectionIdQname . "&countryId=" . $countryIdQname . "&firstLoadedSameOrAfter=" . $date . "&qualityIssues=NO_ISSUES&access_token=" . $token;

    return $url;
}

function buildAggregateQuery($token, $date) {

    // Date has to be in format 2018-12-24

    $url = "https://api.laji.fi/v0/warehouse/query/aggregate?aggregateBy=document.collectionId&geoJSON=false&onlyCount=true&pairCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=100&page=1&cache=false&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&individualCountMin=1&firstLoadedSameOrAfter=" . $date . "&qualityIssues=NO_ISSUES&access_token=" . $token;

    return $url;
}


function buildDocumentList($json, $latestDocumentId) {

    /*
    Target:

    {
        documentId: string
        gatherings {
            gatheringId
            team: string
            date: string
            locality: string
            unitCount: int
        }
    }

    */

    $arr = json_decode($json, TRUE);
//    echo "<pre>"; print_r ($arr); echo "</pre>"; // debug

    $data = Array();

        // TODO: change document -> element
        foreach($arr['results'] as $i => $document) {


        if ($document['document']['documentId'] == $latestDocumentId) {
            break;
        }

        // Shorthands
        $doc = $document['document'];
        $gat = $document['gathering'];
        $uni = $document['unit'];

        // Basic data can be overwritten per gathering, since it cannot vary
        $locality = $gat['country'] . ", " . $gat['biogeographicalProvince'] . ", " . $gat['municipality'] . ", " . $gat['locality'];
        $date = $gat['eventDate']['begin'];

        $team = "";
        foreach ($gat['team'] as $n => $name) {
            $team = $team . ", " . $name;
        }
        $team = trim($team, ", ");

        // Counting units
        $data[$doc['documentId']][$gat['gatheringId']]['unitCOunt'] += 1;

        $data[$doc['documentId']][$gat['gatheringId']]['locality'] = $locality;
        $data[$doc['documentId']][$gat['gatheringId']]['date'] = $date;
        $data[$doc['documentId']][$gat['gatheringId']]['team'] = $team;
    }

    echo "<pre style='color: green;'>"; print_r ($data); echo "\n\n"; echo "</pre>"; // debug



}

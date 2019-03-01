<?php


function getDataFromLajifi($url) {
    $responseJSON = file_get_contents($url);
    return $responseJSON;
}

function buildListQuery($token) {

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
    $orderBy = "document.createdDate";
    $orderDirection = "DESC"; // DESC / ASC
    $limit = 10;
    $collectionIdQname = "HR.1747";

    $url = "https://api.laji.fi/v0/warehouse/query/list?selected=" . $selected . "&orderBy=" . $orderBy . "%20" . $orderDirection . "&pageSize=" . $limit . "&page=1&collectionId=" . $collectionIdQname . "&access_token=" . $token;

    return $url;
}

function buildAggregateQuery($token) {

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
    $orderBy = "document.createdDate";
    $orderDirection = "DESC"; // DESC / ASC
    $limit = 10;
    $collectionIdQname = "HR.1747";

    $url = "https://api.laji.fi/v0/warehouse/query/list?selected=" . $selected . "&orderBy=" . $orderBy . "%20" . $orderDirection . "&pageSize=" . $limit . "&page=1&collectionId=" . $collectionIdQname . "&access_token=" . $token;

    return $url;
}


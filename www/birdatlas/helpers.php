<?php


function getDataFromLajifi($url) {
    $responseJSON = file_get_contents($url . LAJIFI_TOKEN);
    if ($responseJSON === FALSE) {
        $errorMessage = "Error fetching data from api.laji.fi";
        exit($errorMessage);
    }
    return $responseJSON;
}

function getApiLink($url) {
    $html ="<a href=\"" . $url . LAJIFI_TOKEN . "\" class=\"apilink\">API</a>";
    return $html;
}

function getCollectionName($id) {
    if ("http://tun.fi/HR.4412" == $id) {
        return "Tiira";
    }
    if ("http://tun.fi/HR.1747" == $id) {
        return "Vihko, retkilomake";
    }
    if ("http://tun.fi/HR.4471" == $id) {
        return "Vihko, atlaslomake";
    }
    if ("http://tun.fi/HR.3211" == $id) {
        return "iNaturalist Suomi";
    }

    return $id;

}

function getAtlasGridSquare($arr) {
    return (int) $arr['gathering.conversions.ykj10km.lat'] . ":" . (int) $arr['gathering.conversions.ykj10km.lon'];
}


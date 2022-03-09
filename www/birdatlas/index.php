<?php

require_once "../config/env.php";
require_once "helpers.php";
require_once "html_header.php";


// Collections

$url = "https://api.laji.fi/v0/warehouse/query/unit/aggregate?aggregateBy=document.collectionId&onlyCount=true&taxonCounts=false&pairCounts=false&atlasCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=100&page=1&cache=true&taxonId=MX.37580&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&countryId=ML.206&yearMonth=2022%2F2025&individualCountMin=1&qualityIssues=NO_ISSUES&atlasClass=MY.atlasClassEnumB%2CMY.atlasClassEnumC%2CMY.atlasClassEnumD&access_token=";

$json = getDataFromLajifi($url);
$dataArr = json_decode($json, TRUE);

echo "<h2>Aineistot</h2>";
echo getApiLink($url);
echo "<table>";
echo "<tr><th>Aineisto</th><th>Havaintoa</th></tr>";

foreach($dataArr['results'] as $number => $arr) {
    echo "<tr><td>";
    echo getCollectionName($arr['aggregateBy']['document.collectionId']);
    echo "</td><td>";
    echo $arr['count'];
    echo "</td></tr>";
}

echo "</table>";


// Confirmed breeding, last 14 days

$url = "https://api.laji.fi/v0/warehouse/query/unit/aggregate?aggregateBy=unit.linkings.originalTaxon.speciesNameFinnish&onlyCount=true&taxonCounts=false&pairCounts=false&atlasCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=25&page=1&cache=true&taxonId=MX.37580&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&countryId=ML.206&yearMonth=2022%2F2025&individualCountMin=1&qualityIssues=NO_ISSUES&time=-14%2F0&atlasClass=MY.atlasClassEnumD&access_token=";

$json = getDataFromLajifi($url);
$dataArr = json_decode($json, TRUE);

echo "<h2>Varmat pesinnät viimeisen 2 viikon ajalta (top 25)</h2>";
echo getApiLink($url);

echo "<p>Näillä lajeilla on pesintä käynnissä nyt, eli niitä kannattaa erityisesti tarkkailla. Arkaluontoiset havainnot eivät ole tässä taulukossa mukana.</p>";
echo "<table>";
echo "<tr><th>Laji</th><th>Havaintoa</th></tr>";

foreach($dataArr['results'] as $number => $arr) {
    echo "<tr><td>";
    echo getCollectionName($arr['aggregateBy']['unit.linkings.originalTaxon.speciesNameFinnish']);
    echo "</td><td>";
    echo $arr['count'];
    echo "</td></tr>";
}

echo "</table>";


// Probable breeding, last 14 days

$url = "https://api.laji.fi/v0/warehouse/query/unit/aggregate?aggregateBy=unit.linkings.originalTaxon.speciesNameFinnish&onlyCount=true&taxonCounts=false&pairCounts=false&atlasCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=25&page=1&cache=true&taxonId=MX.37580&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&countryId=ML.206&yearMonth=2022%2F2025&individualCountMin=1&qualityIssues=NO_ISSUES&time=-14%2F0&atlasClass=MY.atlasClassEnumC&access_token=";

$json = getDataFromLajifi($url);
$dataArr = json_decode($json, TRUE);

echo "<h2>Todennäköiset pesinnät viimeisen 2 viikon ajalta (top 25)</h2>";
echo getApiLink($url);

echo "<p>Näillä lajeilla on pesintä valmisteilla tai käynnistymässä nyt, eli niitä kannattaa erityisesti tarkkailla. Arkaluontoiset havainnot eivät ole tässä taulukossa mukana.</p>";
echo "<table>";
echo "<tr><th>Laji</th><th>Havaintoa</th></tr>";

foreach($dataArr['results'] as $number => $arr) {
    echo "<tr><td>";
    echo getCollectionName($arr['aggregateBy']['unit.linkings.originalTaxon.speciesNameFinnish']);
    echo "</td><td>";
    echo $arr['count'];
    echo "</td></tr>";
}

echo "</table>";


// Species

$url = "https://api.laji.fi/v0/warehouse/query/unit/aggregate?aggregateBy=unit.linkings.originalTaxon.speciesNameFinnish&onlyCount=true&taxonCounts=false&pairCounts=false&atlasCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=50&page=1&cache=true&taxonId=MX.37580&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&countryId=ML.206&yearMonth=2022%2F2025&individualCountMin=1&qualityIssues=NO_ISSUES&atlasClass=MY.atlasClassEnumB%2CMY.atlasClassEnumC%2CMY.atlasClassEnumD&access_token=";

$json = getDataFromLajifi($url);
$dataArr = json_decode($json, TRUE);

echo "<h2>Eniten havaitut lajit (top 50)</h2>";
echo getApiLink($url);

echo "<table>";
echo "<tr><th>Laji</th><th>Havaintoa</th></tr>";

foreach($dataArr['results'] as $number => $arr) {
    echo "<tr><td>";
    echo getCollectionName($arr['aggregateBy']['unit.linkings.originalTaxon.speciesNameFinnish']);
    echo "</td><td>";
    echo $arr['count'];
    echo "</td></tr>";
}

echo "</table>";



// Observers

// ID's
$url = "https://api.laji.fi/v0/warehouse/query/unit/aggregate?aggregateBy=gathering.observerUserIds&onlyCount=true&taxonCounts=false&pairCounts=false&atlasCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=50&cache=true&taxonId=MX.37580&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&countryId=ML.206&yearMonth=2022%2F2025&individualCountMin=1&qualityIssues=NO_ISSUES&atlasClass=MY.atlasClassEnumB%2CMY.atlasClassEnumC%2CMY.atlasClassEnumD&access_token=";

// NAMES
$url = "https://api.laji.fi/v0/warehouse/query/unit/aggregate?aggregateBy=gathering.team.memberName&onlyCount=true&taxonCounts=false&pairCounts=false&atlasCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=50&cache=true&taxonId=MX.37580&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&countryId=ML.206&yearMonth=2022%2F2025&individualCountMin=1&qualityIssues=NO_ISSUES&atlasClass=MY.atlasClassEnumB%2CMY.atlasClassEnumC%2CMY.atlasClassEnumD&access_token=";


$json = getDataFromLajifi($url);
$dataArr = json_decode($json, TRUE);

echo "<h2>Aktiivisimmat havainnoijat (Lajitietokeskus, top 50)</h2>";
echo getApiLink($url);
echo "<p>Tässä ovat mukana havainnoijat niistä havainnoista, joissa on julkinen nimitieto Lajitietokeskuksen tietovarastossa. Laskennassa ei ole mukana arkaluontoisia havaintoja, ja käyttäjä on voinut myös itse salata nimensä Vihkossa havaintoeräkohtaisesti.</p>";

echo "<table>";
echo "<tr><th>Henkilö</th><th>Havaintoa</th></tr>";

foreach($dataArr['results'] as $number => $arr) {

    /*
    // ID's
    echo "<tr><td>";
    echo $arr['aggregateBy']['gathering.observerUserIds'];
    echo "</td><td>";
    echo $arr['count'];
    echo "</td></tr>";
    */

    // NAMES
    echo "<tr><td>";

    /*
    // Hashing
    if ("Mikko Heikkinen" == $arr['aggregateBy']['gathering.team.memberName']) {
        echo $arr['aggregateBy']['gathering.team.memberName'];
    }
    else {
        echo sha1($arr['aggregateBy']['gathering.team.memberName']);
    }
    */

    // No hashing
    echo $arr['aggregateBy']['gathering.team.memberName'];
    
    echo "</td><td>";
    echo $arr['count'];
    echo "</td></tr>";

}

echo "</table>";




// Grid squares

$url = "https://api.laji.fi/v0/warehouse/query/unit/aggregate?aggregateBy=gathering.conversions.ykj10km.lat%2Cgathering.conversions.ykj10km.lon&onlyCount=true&taxonCounts=false&pairCounts=false&atlasCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=25&page=1&cache=false&taxonId=MX.37580&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&countryId=ML.206&yearMonth=2022%2F2025&individualCountMin=1&qualityIssues=NO_ISSUES&atlasClass=MY.atlasClassEnumB%2CMY.atlasClassEnumC%2CMY.atlasClassEnumD&access_token=";

$json = getDataFromLajifi($url);
$dataArr = json_decode($json, TRUE);

echo "<h2>Atlasruudut, joista eniten havaintoja (top 25)</h2>";
echo getApiLink($url);
echo "<table>";
echo "<tr><th>Ruutu</th><th>Havaintoa</th></tr>";

foreach($dataArr['results'] as $number => $arr) {
    $grid = getAtlasGridSquare($arr['aggregateBy']);

    echo "<tr><td>";
    echo "<a href=\"https://laji.fi/map?gridsquare=" . $grid . "&layers=maastokartta,atlasGrid\">" . $grid . "</a>";
    echo "</td><td>";
    echo $arr['count'];
    echo "</td></tr>";
}

echo "</table>";




// Municipalities

$url = "https://api.laji.fi/v0/warehouse/query/unit/aggregate?aggregateBy=gathering.interpretations.municipalityDisplayname&onlyCount=true&taxonCounts=false&pairCounts=false&atlasCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=50&page=1&cache=true&taxonId=MX.37580&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&countryId=ML.206&yearMonth=2022%2F2025&individualCountMin=1&qualityIssues=NO_ISSUES&atlasClass=MY.atlasClassEnumB%2CMY.atlasClassEnumC%2CMY.atlasClassEnumD&access_token=";

$json = getDataFromLajifi($url);
$dataArr = json_decode($json, TRUE);

echo "<h2>Kunnat tai kuntayhdistelmät, joista eniten havaintoja (top 50)</h2>";
echo getApiLink($url);
echo "<p>Huom: havainnossa voi olla useita kuntanimiä, koska monissa tapauksissa atlashavainnossa voi olla monta kuntanimeä, tai kuntanimet tulkitaan atlasruudun perusteella.</p>";

echo "<table>";
echo "<tr><th>Kunnat</th><th>Havaintoa</th></tr>";

foreach($dataArr['results'] as $number => $arr) {
    echo "<tr><td>";
    echo $arr['aggregateBy']['gathering.interpretations.municipalityDisplayname'];
    echo "</td><td>";
    echo $arr['count'];
    echo "</td></tr>";
}

echo "</table>";

require_once "html_footer.php";

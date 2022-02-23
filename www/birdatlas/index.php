<?php

require_once "../config/env.php";
require_once "helpers.php";

echo "<h1>Lintuatlaksen tilastoja</h1>";
echo "<p>Nämä tilastot tulevat Lajitietokeskuksen julkisesta rajapinnasta (api.laji.fi) pienellä viiveellä.</p>";

// Collections

$url = "https://api.laji.fi/v0/warehouse/query/unit/aggregate?aggregateBy=document.collectionId&onlyCount=true&taxonCounts=false&pairCounts=false&atlasCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=100&page=1&cache=true&taxonId=MX.37580&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&countryId=ML.206&yearMonth=2022%2F2025&individualCountMin=1&qualityIssues=NO_ISSUES&atlasClass=MY.atlasClassEnumB%2CMY.atlasClassEnumC%2CMY.atlasClassEnumD&access_token=";

$json = getDataFromLajifi($url);
$dataArr = json_decode($json, TRUE);

echo "<h2>Aineistot</h2>";

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


// Species

$url = "https://api.laji.fi/v0/warehouse/query/unit/aggregate?aggregateBy=unit.linkings.originalTaxon.nameFinnish&onlyCount=true&taxonCounts=false&pairCounts=false&atlasCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=50&page=1&cache=true&taxonId=MX.37580&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&countryId=ML.206&yearMonth=2022%2F2025&individualCountMin=1&qualityIssues=NO_ISSUES&atlasClass=MY.atlasClassEnumB%2CMY.atlasClassEnumC%2CMY.atlasClassEnumD&access_token=";

$json = getDataFromLajifi($url);
$dataArr = json_decode($json, TRUE);

echo "<h2>50 eniten havaittua lajia</h2>";

echo "<table>";
echo "<tr><th>Laji</th><th>Havaintoa</th></tr>";

foreach($dataArr['results'] as $number => $arr) {
    echo "<tr><td>";
    echo getCollectionName($arr['aggregateBy']['unit.linkings.originalTaxon.nameFinnish']);
    echo "</td><td>";
    echo $arr['count'];
    echo "</td></tr>";
}

echo "</table>";



// Observers

$url = "https://api.laji.fi/v0/warehouse/query/unit/aggregate?aggregateBy=gathering.team.memberName&onlyCount=true&taxonCounts=false&pairCounts=false&atlasCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&cache=true&taxonId=MX.37580&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&countryId=ML.206&yearMonth=2022%2F2025&individualCountMin=1&qualityIssues=NO_ISSUES&atlasClass=MY.atlasClassEnumB%2CMY.atlasClassEnumC%2CMY.atlasClassEnumD&access_token=";

$json = getDataFromLajifi($url);
$dataArr = json_decode($json, TRUE);

echo "<h2>100 Aktiivisinta havainnoijaa</h2>";
echo "<p>Huom: tässä ei ole mukana Tiiran kautta kirjattuja havaintoja, koska havainnoijien nimet eivät välity sieltä Lajitietokeskukseen.</p>";

echo "<table>";
echo "<tr><th>Henkilö</th><th>Havaintoa</th></tr>";

foreach($dataArr['results'] as $number => $arr) {
    echo "<tr><td>";
    echo $arr['aggregateBy']['gathering.team.memberName'];
    echo "</td><td>";
    echo $arr['count'];
    echo "</td></tr>";
}

echo "</table>";




// Municipalities

$url = "https://api.laji.fi/v0/warehouse/query/unit/aggregate?aggregateBy=gathering.interpretations.municipalityDisplayname&onlyCount=true&taxonCounts=false&pairCounts=false&atlasCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=50&page=1&cache=true&taxonId=MX.37580&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&countryId=ML.206&yearMonth=2022%2F2025&individualCountMin=1&qualityIssues=NO_ISSUES&atlasClass=MY.atlasClassEnumB%2CMY.atlasClassEnumC%2CMY.atlasClassEnumD&access_token=";

$json = getDataFromLajifi($url);
$dataArr = json_decode($json, TRUE);

echo "<h2>50 kuntaa tai kuntayhdistelmää, joista eniten havaintoja</h2>";
echo "<p>Huom: havainnossa voi olla useita kuntanimiä, koska A) Tiiran havaintojen tarkat paikat ja kuntanimet eivät välity Lajitietokeskukseen, jolloin kunta tulkitaan atlasruudun perusteella, ja B) Vihkossa havaintoerälle voi kirjata useita kuntia.</p>";

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




// Grid squares

$url = "https://api.laji.fi/v0/warehouse/query/unit/aggregate?aggregateBy=gathering.conversions.ykj10km.lat%2Cgathering.conversions.ykj10km.lon&onlyCount=true&taxonCounts=false&pairCounts=false&atlasCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=10&page=1&cache=false&taxonId=MX.37580&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&countryId=ML.206&yearMonth=2022%2F2025&individualCountMin=1&qualityIssues=NO_ISSUES&atlasClass=MY.atlasClassEnumB%2CMY.atlasClassEnumC%2CMY.atlasClassEnumD&access_token=";

$json = getDataFromLajifi($url);
$dataArr = json_decode($json, TRUE);

echo "<h2>10 atlasruutua, joista eniten havaintoja</h2>";

echo "<table>";
echo "<tr><th>Ruutu</th><th>Havaintoa</th></tr>";

foreach($dataArr['results'] as $number => $arr) {
    echo "<tr><td>";
    echo getAtlasGridSquare($arr['aggregateBy']);
    echo "</td><td>";
    echo $arr['count'];
    echo "</td></tr>";
}

echo "</table>";

<?php

require_once "../config/env.php";
require_once "helpers.php";


//echo LAJIFI_TOKEN;


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

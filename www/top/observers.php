<h1>Lajeja Suomesta 2019</h1>
<p>Yksitysyyssyistä tämä luettelo näyttää vain ne henkilöt, jotka ovat pyytäneet olla mukana luettelossa. Jos haluat mukaan, viestitä osoitteeseen hiha(ät)biomi.org.</p>
<p>Nollahavaintoja (yksilömäärä = 0) ja lajia korkeammalle tasolle määritettyjä havaintoja ei huomioida.</p>
<hr>
<?php

$namesObservations = Array();

foreach ($names as $i => $name) {
  $nameUrlencoded = urlencode($name);
  $url = "https://api.laji.fi/v0/warehouse/query/aggregate?aggregateBy=unit.linkings.taxon.scientificName&geoJSON=false&onlyCount=true&pairCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=100&page=1&cache=true&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&taxonRankId=MX.species&countryId=ML.206&time=2019&individualCountMin=1&teamMember=" . $nameUrlencoded . "&qualityIssues=NO_ISSUES&access_token=" . LAJIFI_TOKEN;

  $json = getDataFromLajifi($url);
  $arr = json_decode($json, TRUE);

  $namesObservations[$name] = $arr['total'];
}

arsort($namesObservations);

echoTable($namesObservations);

?>

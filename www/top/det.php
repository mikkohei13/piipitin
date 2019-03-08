<h1>Määrityksiä Suomesta 2019</h1>
<p>Tämä luettelo näyttää det-kentän sisällön Lajitietokeskukseen tallennetuista suomalaisista näytteistä/havainnoista vuodelta 2019. Jokainen kirjoitusasu näytetään erikseen, paitsi että "Sukunimi, Etunimi" yhdistetään muotoon "Etunimi Sukunimi". Luettelo ei ota huomioon onko kyseessä oman vai toisen henkilön näytteen/havainnon määritys (koska tämän tiedon tilastointi olisi rajapinnan kautta huomattavan vaikeaa).
<p>Yhdenmukaisuuden vuoksi <em>määrittäjiä Vihkoon kirjatessa</em> kannattaa käyttää muotoa "Etunimi Sukunimi", erotella useat määrittäjät toisistaan puolipisteellä (;), ja kirjata muut määritykseen liittyvät tiedot "määrityksen peruste"- tai "lisätiedot"-kenttään. Kaikkea epäyhtenäisyyttä tämä ei kuitenkaan poista, sillä Lajitietokeskukseen kerätään havaintotietoa kymmenistä eri järjestelmistä, joissa kussakin saattaa olla omia käytäntöjään.
<hr>
<?php

$namesObservations = Array();

$url = "https://api.laji.fi/v0/warehouse/query/aggregate?aggregateBy=unit.det&geoJSON=false&onlyCount=true&pairCounts=false&excludeNulls=true&pessimisticDateRangeHandling=false&pageSize=100&page=1&cache=true&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&taxonRankId=MX.species&countryId=ML.206&time=2019&individualCountMin=1&qualityIssues=NO_ISSUES&access_token=" . LAJIFI_TOKEN;

$json = getDataFromLajifi($url);
$arr = json_decode($json, TRUE);

$namesCounts = Array();

foreach ($arr['results'] as $i => $data) {
  $nameNormalized = normalizeName($data['aggregateBy']['unit.det']);
  if (isset($namesCounts[$nameNormalized])) {
    $namesCounts[$nameNormalized] += $data['count'];
  }
  else {
    $namesCounts[$nameNormalized] = $data['count'];
  }
}

// Generic names
arsort($namesCounts);

echoTable($namesCounts);


// Functions

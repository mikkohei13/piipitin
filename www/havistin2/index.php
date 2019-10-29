<?php

/*
Todo:
- Clarify & test col names
- todo's in files 
- producton logging

Todo later:
- Organize classess, functions, content, templates
- Logout with DELETE person token
- Getting taxon information about Hatikka observations
- Log2: make a class, set debug vs. production mode
- Images
*/

require_once "log2_SLAVE.php";
require_once "finbif.php";
require_once "_secrets.php";
require_once "html_include/header.php";

// todo: move to function, use also on mydocuments.php
if (isset($_GET['personToken'])) {
  if (ctype_alnum($_GET['personToken'])) {
    $personToken = $_GET['personToken'];
  }
  else {
    log2("ERROR", "Invalid personToken", LOG_DIR."/havistin.log");
  }
}
else {
  log2("ERROR", "No personToken given", LOG_DIR."/havistin.log");
}

$fin = new finbif(API_TOKEN, $personToken);

$me = $fin->personByToken($personToken);

log2("START", "Load index by user " . $me['id'], LOG_DIR."/havistin.log");

// -----------------------------------------------------------------

?>

<h1>Havistin v0.1</h1>

<p>
Havistimen avulla voit tallentaa tiedostoksi (eli exportata) omat havaintosi (ne, joissa olet havainnoijana) Lajitietokeskuksen Vihko-havaintopalvelusta.
Tallennusformaatteja on kaksi:
</p>
<ul>
  <li>TSV (tab separated values) -taulukkotiedosto, jossa on mukana "tärkeimmät" sarakkeet. Tämän voi avata esim. Excelillä tai Open/Libre Officella.</li>
  <li>JSON-tiedosto, jossa on mukana kaikki Vihkoon tallennettu tieto alkuperäisessä muodossaan. Suuri osa tiedoista esitetään tunnisteina (esim. taksonin tunniste, henkilön tunniste). JSON on yleinen tiedostoformaatti rakenteiselle datalle, ja sitä voi käsitellä yleisillä ohjelmontityökaluilla.</li>
</ul>


<?php

// -----------------------------------------------------------------

$myDocumentsByYear = $fin->myDocumentsByYear();

$htmlTableRows = "";
foreach ($myDocumentsByYear as $nro => $arr) {
  $htmlTableRows .= "<tr>
    <td>" . $arr['year'] . "</td>
    <td>" . $arr['count'] . "</td>
    <td><a href='mydocuments.php?year=" . $arr['year'] . "&format=tsv&personToken=$personToken'>TSV</td>
    <td><a href='mydocuments.php?year=" . $arr['year'] . "&format=json&personToken=$personToken'>JSON</td>
  </tr>";
}

// -----------------------------------------------------------------

?>

<table>
<tr><td>Vuosi</td><td>Havaintoeriä</td></tr>

<?php 
echo $htmlTableRows;
?>

</table>

<?php
// -----------------------------------------------------------------

require_once "html_include/footer.php";

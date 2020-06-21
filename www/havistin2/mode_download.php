

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
echo "</table>";


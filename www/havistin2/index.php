<?php

/*
Todo:
- Login system
- todo's in files 
- producton logging
- Getting taxon information about Hatikka observations
*/

require_once "log2_SLAVE.php";
require_once "finbif.php";
require_once "_secrets.php";
require_once "html_include/header.php";

session_start();

if (isset($_GET['personToken'])) {
  if (ctype_alnum($_GET['personToken'])) {
    $_SESSION['personToken'] = $personToken = $_GET['personToken'];
  }
  else {
    log2("ERROR", "Invalid personToken", "logs/havistin.log");
  }
}
elseif (isset($_SESSION['personToken'])) {
  $personToken = $_SESSION['personToken'];
}
else {
  log2("ERROR", "No personToken given", "logs/havistin.log");
}

$fin = new finbif(API_TOKEN, $personToken);

$me = $fin->personByToken($personToken);

log2("START", "Load index by user " . $me['id'], "logs/havistin.log");


// -----------------------------------------------------------------

$myDocumentsByYear = $fin->myDocumentsByYear();

$htmlTableRows = "";
foreach ($myDocumentsByYear as $nro => $arr) {
  $htmlTableRows .= "<tr>
    <td>" . $arr['year'] . "</td>
    <td>" . $arr['count'] . "</td>
    <td><a href='mydocuments.php?year=" . $arr['year'] . "&format=tsv'>TSV</td>
    <td><a href='mydocuments.php?year=" . $arr['year'] . "&format=json'>JSON</td>
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

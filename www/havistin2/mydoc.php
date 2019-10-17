<?php



require_once "log2_SLAVE.php";
require_once "finbif.php";
require_once "_secrets.php";

log2("START", "-------------------------------------------------------", "logs/havistin.log");

$personToken = $_GET['personToken']; // todo: security

$fin = new finbif(API_TOKEN, $personToken);

//$fin->test();

$myDocumentsByYear = $fin->myDocumentsByYear();

$htmlTableRows = "";
foreach ($myDocumentsByYear as $nro => $arr) {
  $htmlTableRows .= "<tr><td>" . $arr['year'] . "</td><td>" . $arr['count'] . "</td></tr>";
}

// --------------------

?>

<table>
<tr><td>Vuosi</td><td>Havaintoeriä</td></tr>

<?php 
echo $htmlTableRows;
?>

</table>


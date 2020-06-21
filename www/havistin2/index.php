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

echo "<h1>Havistin v0.1</h1>";

// -----------------------------------------------------------------

if (@$_GET['mode'] == "missions") {
  require_once "mode_missions.php";
}
elseif (@$_GET['mode'] == "download") {
  require_once "mode_download.php";
}
else {
  echo "
    <p><a href=\"./?mode=download&personToken=$personToken\">Lataa omat havainnot</a></p>
    <p><a href=\"./?mode=missions&personToken=$personToken\">Oma puutelista</a></p>
  ";
}

?>


<?php

// -----------------------------------------------------------------

require_once "html_include/footer.php";

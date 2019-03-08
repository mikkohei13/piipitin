<?php


// Converts "Lastname, Firstname" into "Firstname Lastname"
function normalizeName($name) {
  if (1 == substr_count($name, ",") && 1 == substr_count($name, " ")) {
    $nameParts = explode(",", $name);
    $name = trim($nameParts[1]) . " " . trim($nameParts[0]);
  }
  return $name;
}

function echoTable($namesCounts) {
  $i = 0;
  $prevCount = NULL;
  echo "<table>\n";
  foreach ($namesCounts as $name => $count) {
    if ($count === $prevCount) {
      $iDisplayed = "";
    }
    else {
      $i++;
      $prevCount = $count;
      $iDisplayed = $i . ".";
    }
    echo "  <tr>\n";
    echo "    <td>$iDisplayed</td>\n";
    echo "    <td>$name</td>\n";
    echo "    <td>$count</td>\n";
    echo "  </tr>\n";
  }  
  echo "</table>\n";
}


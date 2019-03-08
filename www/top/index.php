<?php
require_once "../config/env.php";
require_once "../logger.php";
require_once "../lajifi.php";
require_once "helpers.php";

if (isset($_GET['observers'])) {
  require_once "observers.php";
//  logger("lajifi.log", "info", "GET top names");
}
elseif (isset($_GET['det'])) {
  require_once "det.php";
}
else {
  echo "Please give top list type.";
//  logger("lajifi.log", "warning", "Top list type not given");
}

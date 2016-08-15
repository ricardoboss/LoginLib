<?php
require("load.php");

session_start(0);
$_SESSION['test'] = "test";
echo "Set $_SESSION['test] to \"test\"";
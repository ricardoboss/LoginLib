<?php
require("load.php");

session_start(0);

echo "$_SESSION:\n";
var_dump($_SESSION);

echo "\n";

if (!isset($_SESSION['test'])) {
	return 1;
} else {
	return 0;
}
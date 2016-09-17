<?php
require("load.php");

session_start(0);

echo "\$_SESSION:\n";
var_dump($_SESSION);

if (isset($_SESSION['test'])) {
	return 0;
} else {
	return 1;
}
<?php
require("load.php");

session_start(0);

logEntry("\$_SESSION:");
logEntry($_SESSION);

if (isset($_SESSION['test'])) {
	return 0;
} else {
	return 1;
}
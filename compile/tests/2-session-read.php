<?php
require("load.php");

session_start(0);
echo "$_SESSION:\n";
var_dump($_SESSION);
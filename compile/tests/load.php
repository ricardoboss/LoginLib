<?php
$root = dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR;

require($root."dist".DIRECTORY_SEPARATOR."LoginLib.php");

require($root."test".DIRECTORY_SEPARATOR."MysqliDb.php");
require($root."test".DIRECTORY_SEPARATOR."DatabaseAdapter.php");
require($root."test".DIRECTORY_SEPARATOR."config.php");

$db = new DatabaseAdapter($databaseConfig);
$loginlib = new LoginLib\LoginLib($config, $db);
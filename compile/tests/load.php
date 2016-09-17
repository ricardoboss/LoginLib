<?php
$root = dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR;

require($root."dist".DIRECTORY_SEPARATOR."LoginLib.php");

require($root."test".DIRECTORY_SEPARATOR."MysqliDb.php");
require($root."test".DIRECTORY_SEPARATOR."DatabaseAdapter.php");
require($root."test".DIRECTORY_SEPARATOR."config.php");

try {
	$db = new DatabaseAdapter($databaseConfig);
	$db->connect();
} catch (Exception $e) {
	die(trigger_error("Could not connect to database!", E_USER_ERROR));
}

try {
	$loginlib = new LoginLib\LoginLib($config, $db);
} catch(LoginLib\ConfigurationException $e) {
	die(trigger_error("Caught ConfigurationException: ".$e->getMessage()));
}
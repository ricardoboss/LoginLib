<?php
require('load.php');

try {
	$db->connect();
	logEntry("Ping: " . print_r($db->ping()));
	
	logEntry("Running test queries:");
	
	$queriesraw = file_get_contents(dirname(__DIR__).DIRECTORY_SEPARATOR."queries".DIRECTORY_SEPARATOR."test.sql");
	$queries = explode(";", $queriesraw);
	
	foreach ($queries as $id => $query) {
		$queries[$id] = trim(str_replace(array("\r\n  ", "\r\n"), array(" ", ""), $query));
	}
	
	foreach ($queries as $id => $query) {
		if (strlen($query) != 0) {
			logEntry("Running query " . $id . ": \"" . substr($query, 0, 40) . "...\"");
			$response = $db->rawQuery($query);
			logEntry("Response: " . print_r($response));
		}
	}
	
	return 0;
} catch (Exception $e) {
	trigger_error("Database threw an exception: " . $e->getMessage() . "\nLast error: " . $db->getLastError());
	return 1;
}
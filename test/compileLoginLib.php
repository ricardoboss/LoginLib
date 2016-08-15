<?php
$filestarttime = microtime(true);
class Compiler {
	public $root;
	public $outputfile;
	private $sourcefiles;
	
	public function __construct($root) {
		$this->root = $root;
		$this->outputfile = $this->root.'/dist/LoginLib.php';
		$this->sourcefiles = array(
			'LoginLib.php',
			'Config.php',
			'Results.php',
			'IDatabase.php',
			'User.php',
			'Exceptions.php'
		);
	}

	public function compile() {
		$starttime = microtime(true);

		file_put_contents($this->outputfile, "<?php\r\n");

		// add file comment
		$this->append("/**\r\n * This file contains all classes for LoginLib.\r\n * \r\n * Dont't forget the dependency on MysqliDb!\r\n */\r\n");
		
		// add namespace
		$this->append("namespace LoginLib;");

		// loop through the source files and add them to the output file
		foreach ($this->sourcefiles as $file) {
			if (substr($file, 0, 1) !== ".")
				$this->append(file_get_contents($this->root.'/src/'.$file));
		}

		echo "Compiling finished! (Took: ".(microtime(true) - $starttime)."ms)\n\n";
	}

	private function append($file) {
		// filter the file description and the php tag + namespaces away...
		$file = preg_replace("/\\<\\?php.*(use [a-z\\\]*;|namespace [a-z\\\]*;)/is", "", $file);

		// just append the file to the final one
		file_put_contents($this->outputfile, $file, FILE_APPEND | LOCK_EX);
	}
}

/*****************************************************************************/

echo "Compiling...\n";

$c = new Compiler(dirname(__DIR__));
$c->compile();

// Test if no php errors are thrown
require($c->outputfile);

// echo current LoginLib version, if build succeed
if (!class_exists("LoginLib\LoginLib"))
	die(trigger_error("Class LoginLib\LoginLib not found!", E_USER_ERROR));

echo "Running LoginLib v".LoginLib\LoginLib::version()."\n\n";

/*****************************************************************************/

echo "Loading testing environment...\n";

// create test users
$user1 = array(
	'username' => "MCMainiac1",
	'email' => "mcmainiac1@example.com",
	'password' => "password1"
);

$user2 = array(
	'username' => "MCMainiac2",
	'email' => "mcmainiac2@example.com",
	'password' => "password2"
);

// load classes
require("MysqliDb.php");
require("DatabaseAdapter.php");

// load config
require("config.php");

// create database adapter
$db = new DatabaseAdapter($databaseConfig);

// read queries
$queriesraw = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."queries".DIRECTORY_SEPARATOR."create.sql");
$queries = split(";", $queriesraw);

foreach ($queries as $id => $query)
	$queries[$id] = trim(str_replace(array("\r\n  ", "\r\n"), array(" ", ""), $query));

// create tables
foreach ($queries as $query)
	if (strlen($query) != 0)
		$db->rawQuery($query);

// create LoginLib instance
$loginlib = new LoginLib\LoginLib($config, $db);

/*****************************************************************************/

echo "Starting tests...\n\n";
$i = 0;

//====================

$i++;
echo "[TEST ".$i."]: Register with wrong password\n";
$loginlib->register($user1['username'], $user1['email'], $user1['password'], "not my password...", function($result) {
	echo "Result: ".$result."\n";
});

echo "\n";

//====================

$i++;
echo "[TEST ".$i."]: Register\n";
$loginlib->register($user1['username'], $user1['email'], $user1['password'], $user1['password'], function($result) {
	global $loginlib;
	echo "Result: ".$result."\n";
	echo "Logged in: ".($loginlib->isLoggedIn() ? "Yes":"No")."\n";
});

echo "\n";

//====================

$i++;
echo "[TEST ".$i."]: Register with existing username\n";
$loginlib->register($user1['username'], $user1['email'], $user1['password'], $user1['password'], function($result) {
	global $loginlib;
	echo "Result: ".$result."\n";
});

echo "\n";

//====================

$i++;
echo "[TEST ".$i."]: Register with existing email\n";
$loginlib->register($user2['username'], $user1['email'], $user1['password'], $user1['password'], function($result) {
	global $loginlib;
	echo "Result: ".$result."\n";
});

echo "\n";

//====================

$i++;
echo "[TEST ".$i."]: Login with wrong credentials\n";
$loginlib->login($user1['username'], $user2['password'], function($result) {
	global $loginlib;
	echo "Result: ".$result."\n";
	echo "Am I logged in?: ".($loginlib->isLoggedIn() ? "Yes!":"No!")."\n";
});

echo "\n";

//====================

$i++;
echo "[TEST ".$i."]: Login\n";
$loginlib->login($user1['username'], $user1['password'], function($result) {
	global $loginlib;
	echo "Result: ".$result."\n";
	echo "Am I logged in?: ".($loginlib->isLoggedIn() ? "Yes!":"No!")."\n";
});

if (!$loginlib->isLoggedIn())
	die(trigger_error("Login did not succeed! Fatal error!", E_USER_ERROR));

echo "\n";

//====================

$i++;
echo "[TEST ".$i."]: Logout\n";
$loginlib->logout();
echo "Am I logged in?: ".($loginlib->isLoggedIn() ? "Yes!":"No!")."\n";

echo "\nTests finished!\n\n";

/*****************************************************************************/

$queriesraw = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."queries".DIRECTORY_SEPARATOR."delete.sql");
$queries = split(";", $queriesraw);

foreach ($queries as $id => $query)
	$queries[$id] = trim(str_replace(array("\r\n  ", "\r\n"), array(" ", ""), $query));

// create tables
foreach ($queries as $query)
	if (strlen($query) != 0)
		$db->rawQuery($query);

/*****************************************************************************/

echo "Complete build with tests took: ".(microtime(true) - $filestarttime)."ms\n";

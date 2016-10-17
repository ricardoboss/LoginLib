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
			'IDatabase.php',
            'Exceptions'.DIRECTORY_SEPARATOR.'ConfigurationException.php',
            'Exceptions'.DIRECTORY_SEPARATOR.'DatabaseException.php',
			'Results'.DIRECTORY_SEPARATOR.'MethodResult.php',
			'Results'.DIRECTORY_SEPARATOR.'LoginResult.php',
			'Results'.DIRECTORY_SEPARATOR.'RegisterResult.php',
			'User.php'
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
        foreach ($this->sourcefiles as $file)
            try {
                $this->appendFile($this->root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $file);
            } catch (Exception $e) {
                die(trigger_error("Cannot append file: " . $this->root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $file . "\r\n".$e->getMessage()));
            }

		echo "Compiling finished! (Took: ".(microtime(true) - $starttime)."ms)\n\n";
	}

    private function append($file) {
		// filter the file description and the php tag + namespaces away...
		$file = preg_replace("/\\<\\?php.*(use [a-z\\\]*;|namespace [a-z\\\]*;)/is", "", $file);

		// just append the file to the final one
		file_put_contents($this->outputfile, $file, FILE_APPEND | LOCK_EX);
	}

	private function appendFile($file) {
	    if (!file_exists($file))
	        throw new Exception("File not found: " . $file);
        else
            $this->append(file_get_contents($file));
    }
}

/*****************************************************************************/

echo "Compiling...\n";

$c = new Compiler(dirname(__DIR__));
$c->compile();

// Test if no php errors are thrown
try {
    require($c->outputfile);
} catch (Exception $e) {
    die(trigger_error("Complied file is not php valid: " . $e->getMessage(), E_USER_ERROR));
}

if (!class_exists("LoginLib\LoginLib")) {
	die(trigger_error("Class LoginLib\\LoginLib not found!", E_USER_ERROR));
}

// echo current LoginLib version, if build succeed
echo "Running LoginLib v".LoginLib\LoginLib::version()."\n\n";

/*****************************************************************************/

// create tables

echo "Creating tables in database...\n";

require("tests".DIRECTORY_SEPARATOR."MysqliDb.php");
require("tests".DIRECTORY_SEPARATOR."DatabaseAdapter.php");
require("tests".DIRECTORY_SEPARATOR."config.php");

$queriesraw = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."queries".DIRECTORY_SEPARATOR."create.sql");
$queries = explode(";", $queriesraw);

foreach ($queries as $id => $query) {
	$queries[$id] = trim(str_replace(array("\r\n  ", "\r\n"), array(" ", ""), $query));
}

if (isset($databaseConfig)) {
    $db = new DatabaseAdapter($databaseConfig);
} else {
    trigger_error("Database config not found!", E_USER_ERROR);
}

try {
	$db->connect();
} catch (Exception $e) {
	trigger_error("Failed to connect to database!", E_USER_ERROR);
	return 1;
}

try {
	foreach ($queries as $id => $query) {
		if (strlen($query) != 0) {
			//echo "Running query " . $id . ": \"" . substr($query, 0, 25) . "...\"\n";
			$response = $db->rawQuery($query);
			
		}
	}
} catch (Exception $e) {
	trigger_error("Failed to run sql queries: " . $db->getLastError(), E_USER_ERROR);
	return 1;
}

echo "\n";

/*****************************************************************************/

// collect tests

if (!($h = opendir(__DIR__.DIRECTORY_SEPARATOR."tests"))) {
	trigger_error("Could not open directory handle for tests!", E_USER_ERROR);
	return 1;
}

$tests = array();

while (false !== ($entry = readdir($h))) {
	if (substr($entry, 0, 1) != "." && is_numeric(substr($entry, 0, 1))) {
		$tests[] = __DIR__.DIRECTORY_SEPARATOR."tests".DIRECTORY_SEPARATOR.$entry; 
	}
}

// running tests

$teststarttime = microtime(true);

echo "Running tests:\n";

$ok = true;

for ($i = 0; $i < count($tests); $i++) {
	exec("php \"".$tests[$i]."\"", $output, $return);
	
	$path = explode(DIRECTORY_SEPARATOR, $tests[$i]);
	$file = substr(end($path), 2);
	
	echo "==== Test [".($i + 1)."] (\"".$file."\") ====\n";
	foreach ($output as $line)
		echo $line."\n";
	echo "\n";
	echo "Returned: ".$return."\n";
	echo "\n";
	
	if ($return != 0) {
		$ok = false;
		break;
	}
}

if ($ok)
	echo "Tests completed successfully! (took: ".(microtime(true) - $teststarttime)."ms)\n";
else {
	trigger_error("Tests failed! (took: ".(microtime(true) - $teststarttime)."ms)\n", E_USER_ERROR);
	return 1;
}

/*****************************************************************************/

// delete tables

$queriesraw = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."queries".DIRECTORY_SEPARATOR."delete.sql");
$queries = explode(";", $queriesraw);

foreach ($queries as $id => $query) {
	$queries[$id] = trim(str_replace(array("\r\n  ", "\r\n"), array(" ", ""), $query));
}

try {
	$db->connect();
} catch (Exception $e) {
	trigger_error("Failed to connect to database!", E_USER_ERROR);
	return 1;
}

try {
	foreach ($queries as $id => $query) {
		if (strlen($query) != 0) {
			//echo "Running query " . $id . ": \"" . substr($query, 0, 25) . "...\"\n";
			$response = $db->rawQuery($query);
				
		}
	}
} catch (Exception $e) {
	trigger_error("Failed to run sql queries: " . $db->getLastError(), E_USER_ERROR);
	return 1;
}

/*****************************************************************************/

echo "\n";
echo "Complete build with tests took: ".(microtime(true) - $filestarttime)."ms\n";

return 0;


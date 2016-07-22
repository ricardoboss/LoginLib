<?php
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

		echo "Finished! (Took: ".(microtime(true) - $starttime)."ms)";
	}

	private function append($file) {
		// filter the file description and the php tag + namespaces away...
		$file = preg_replace("/\\<\\?php.*(use [a-z\\\]*;|namespace [a-z\\\]*;)/is", "", $file);

		// just append the file to the final one
		file_put_contents($this->outputfile, $file, FILE_APPEND | LOCK_EX);
	}
}

$c = new Compiler(dirname(__DIR__));
$c->compile();

// Test if no php errors are thrown
require($c->outputfile);

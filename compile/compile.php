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
		$this->append("/**\r\n * This file contains all classes for LoginLib.\r\n */\r\n");

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

return 0;
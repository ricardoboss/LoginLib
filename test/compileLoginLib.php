<?php
class Compiler {
	const root = dirname(__DIR__);
	const outputfile = Compiler::root.'dist/LoginLib.php';
	const sourcefiles = array(
		'LoginLib.php',
		'Config.php',
		'Results.php',
		'IDatabase.php',
		'User.php',
		'Exceptions.php'
	);

	public static function start() {
		$starttime = microtime(true);

		file_put_contents(Compiler::outputfile, "<?php\r\n");

		Compiler::append("/**\r\n * This file contains all classes for LoginLib.\r\n * \r\n * Dont't forget the dependency on MysqliDb!\r\n */\r\n");
		Compiler::append("namespace LoginLib;");

		foreach (Compiler::sourcefiles as $file) {
			if (substr($file, 0, 1) !== ".")
				Compiler::append(file_get_contents(Compiler::root.'src'.DIRECTORY_SEPARATOR.$file));
		}

		echo "Finished! (Took: ".(microtime(true) - $starttime)."ms)";
	}

	private static function append($file) {
		// filter the file description and the php tag away...
		$file = preg_replace("/\\<\\?php.*(use [a-z\\\]*;|namespace [a-z\\\]*;)/is", "", $file);

		// just append the file to the final one
		file_put_contents(Compiler::outputfile, $file, FILE_APPEND | LOCK_EX);
	}
}

Compiler::start();

// Test if no php errors are thrown
require(Compiler::outputfile);
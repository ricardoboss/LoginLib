<?php
$root = dirname ( __DIR__ ) . DIRECTORY_SEPARATOR . 'src';

if ($h = opendir ( $root )) {
	while ( false !== ($file = readdir ( $h )) ) {
		if (substr ( $file, 0, 1 ) != ".")
			include ($root . DIRECTORY_SEPARATOR . $file);
	}
	
	closedir ( $h );
} else {
	echo "An error occured: unable to open resource handle in: '" . $path . "' on line 4 in load.php!";
}

unset ( $root, $h, $file );

require('config.php');

require('MysqliDb.php');
require('DatabaseAdapter.php');
$db = new DatabaseAdapter($databaseConfig);

// create a login lib instance with the config (defined in config.php)
$loginlib = new LoginLib\LoginLib($config, $db);
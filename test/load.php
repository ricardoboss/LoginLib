<?php
$root = dirname(__DIR__).DIRECTORY_SEPARATOR;

for ($i = 0; $i < 1; $i++) {
	if ($i == 0)
		$path = $root.'deps';
	else
		$path = $root.'src';

	if ($h = opendir($path)) {
		while (false !== ($file = readdir($h))) {
			if (substr($file, 0, 1) != ".")
				include($path.DIRECTORY_SEPARATOR.$file);
		}

		closedir($h);
	} else {
		echo "An error occured: unable to open resource handle in: '".$path."' on line 10 in load.php!";
	}
}

$db = new MysqliDb('localhost', 'loginlib', 'CmH93W4k', 'LoginLib');
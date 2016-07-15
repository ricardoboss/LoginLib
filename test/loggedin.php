<?php
require('../dist/config.php');
require('load.php');

$loginlib = new LoginLib\LoginLib($config);

var_dump($loginlib->isLoggedIn());
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>LoginLib Test Page</title>
	<link rel="stylesheet" href="./style.css">
</head>
<body>
	<div>
		<pre><?php var_dump($GLOBALS); ?></pre>
	</div>
</body>
</html>
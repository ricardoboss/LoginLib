<?php
require('../dist/config.php');
require('load.php');

$loginlib = new LoginLib\LoginLib($config);

if (!$loginlib->isLoggedIn())
	header("Location: ./?ref=login");
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
<?php
require('../dist/config.php');
require('load.php');

$loginlib = new LoginLib\LoginLib($config);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>LoginLib Test Page</title>
	<link rel="stylesheet" href="./style.css">
</head>
<body>
	<div>
		<p>
			<form action="index.php" method="post">
				You are <span class="<?php echo $loginlib->isLoggedIn() ? 'text-success">':'text-danger">not '; ?>logged in</span>!<br>
				<button type="submit">Log<?php echo $loginlib->isLoggedIn() ? 'out':'in'; ?></button>
				<input type="hidden" name="method" value="<?php echo $loginlib->isLoggedIn() ? 'logout':'redir'; ?>">
			</form>
		</p>
		<br>
		<pre><?php var_dump($GLOBALS); ?></pre>
	</div>
</body>
</html>
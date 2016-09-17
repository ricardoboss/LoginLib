<?php require('load.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>LoginLib Test Page</title>
	<link rel="stylesheet" href="./style.css">
</head>
<body>
	<h1>LoginLib Test Page</h1>
	<div>
		<form action="index.php" method="post">
			You are 
			<?php if ($loginlib->isLoggedIn()) { ?>
			<span class="text-success">logged in</span>
			<?php } else { ?>
			<span class="text-danger">not logged in</span>
			<?php } ?>
			!<br>
			<button type="submit">Log<?php echo $loginlib->isLoggedIn() ? 'out':'in'; ?></button>
			<input type="hidden" name="method" value="<?php echo $loginlib->isLoggedIn() ? 'logout':'redir'; ?>">
		</form>
		<br>
		<pre><?php unset($_SERVER); var_dump($GLOBALS); ?></pre>
	</div>
</body>
</html>
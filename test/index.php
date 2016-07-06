<?php
if (isset($_POST['method'])) {
	require('load.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>LoginLib Tests</title>
</head>
<body>
	<form method="post" action="">
		<h1>Login</h1>
		<div>
			<label for="lg-username">Username:</label><br>
			<input type="text" name="lg-username" id="lg-username" placeholder="Username" required>
		</div>
		<div>
			<label for="lg-password">Password:</label><br>
			<input type="password" name="lg-password" id="lg-password" placeholder="Password" required>
		</div>
		<button type="submit">Login</button>
		<input type="hidden" name="method" value="login">
	</form>
	<br>
	<form method="post" action="">
		<h1>Register</h1>
		<div>
			<label for="rg-username">Username</label><br>
			<input type="text" name="rg-username" id="rg-username" placeholder="Username" required>
		</div>
		<div>
			<label for="rg-email">Email-Address</label><br>
			<input type="email" name="rg-email" id="rg-email" placeholder="Email-Address" required>
		</div>
		<div>
			<label for="rg-password">Password</label><br>
			<input type="password" name="rg-password" id="rg-password" placeholder="Password" required>
		</div>
		<div>
			<label for="rg-confirm">Confirm</label><br>
			<input type="password" name="rg-confirm" id="rg-confirm" placeholder="Confirm" required>
		</div>
		<button type="submit">Register</button>
		<input type="hidden" name="method" value="register">
	</form>
	<br>
	<p>
		Current $_POST:
		<pre><?php var_dump($_POST); ?></pre>
	</p>
	<?php if (isset($db)) { ?>
	<p>
		Last DB query:
		<pre><?php $db->getLastQuery(); ?></pre>
	</p>
	<?php } ?>
</body>
</html>
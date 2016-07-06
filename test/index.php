<?php
if (isset($_POST['method'])) {
	/* for production
	// load deps
	require('../dist/MysqliDb.php');
	
	// load the release version
	require('../dist/LoginLib.php');
	*/
	
	// get the default config (obviously not included in the LoginLib.php)
	require('../dist/config.php');
	require('load.php');
	
	// create a login lib instance with the config (defined in config.php)
	$loginlib = new LoginLib\LoginLib($config);
	
	$message = "";
	
	// determine the used method
	switch ($_POST['method']) {
		// in case we got a login submitted, let LoginLib process it
		case 'login' :
			// call the login method
			$loginlib->login ( 
					// provide username and password from the user
					$_POST['lg-username'], $_POST['lg-password'], 
					
					// define a callback function with the result (type: MethodResult) as a parameter
					function ($result) {
						global $message;
						
						// get the result from the MethodResult object (in this case it is a LoginResult)
						switch ($result->getResult()) {
							case LoginLib\LoginResult::SUCCESS :
								$message = "Login successfull!";
								break;
							case LoginLib\LoginResult::PASSWORD_WRONG :
								$message = "The given password is wrong!";
								break;
							case LoginLib\LoginResult::USERNAME_NOT_FOUND :
								$message = "The username or email you provided is not registered!";
								break;
							
							default :
								$message = "Oops! This shouldn't have happened! Unknown or unregistered LoginResult: " . $result;
								break;
						}
					});
			break;
		case 'register' :
			// call the register method
			$loginlib->register(
				// provide the fields from the formular
				$_POST['rg-username'], $_POST['rg-email'], $_POST['rg-password'], $_POST['rg-confirm'],
				
				// define a callback function to parse the RegisterResult
				function($result) {
					global $message;
					
					switch ($result->getResult()) {
						case LoginLib\RegisterResult::SUCCESS:
							$message = "Registration successfull! You can now log in!";
							break;
						case LoginLib\RegisterResult::USERNAME_GIVEN:
							$message = "That username is already in use! Dang it!";
						case LoginLib\RegisterResult::EMAIL_GIVEN:
							$message = "That email address is already in use! Sorry!";
							break;
						case LoginLib\RegisterResult::PASSWORD_MISMATCH:
							$message = "The passwords you entered didn't match! Please try again!";
							break;
							
						default:
							$message = "What's this? Unknown or unregistered RegisterResult: " . $result;
					}
				}
			);
			break;
		
		default :
			$message = "Oh no! Wrong or unregistered method was submitted: " . $_POST ['method'];
			break;
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>LoginLib Tests</title>
<!-- apply a bit of style; we don't want to let it look too shabby, do we? -->
<style type="text/css">
* {
	box-sizing: border-box;
}

body {
	max-width: 700px;
	margin: 0 auto 25px;
	padding: 20px 25px;
	box-shadow: 0 5px 25px rgba(0, 0, 0, 0.45);
	font-family: sans-serif;
}

p, form {
	margin-top: 20px;
	margin-bottom: 0;
}

h1, h3 {
	padding-bottom: 15px;
	margin: 0;
}

form>div, button {
	margin-top: 15px;
}

label {
	padding: 7.5px 5px;
}

input, label {
	display: block;
	width: 100%;
}

input, button {
	padding: 7.5px 10px;
	border: solid 1px #888;
}

pre, .pre-header {
	background: #666;
	color: #fefefe;
	padding: 20px;
	margin: 0;
	border-radius: 10px;
	display: block;
	width: 100%;
	box-shadow: 0 0 20px rgba(0,0,0,0.45) inset;
	overflow-x: auto;
}

.pre-header {
	border-bottom-left-radius: 0;
	border-bottom-right-radius: 0;
	border-bottom: solid 1px #fefefe;
	margin-top: 20px;
}

.pre-header ~ pre {
	border-top-left-radius: 0;
	border-top-right-radius: 0;
	margin-bottom: 20px;
}
</style>
</head>
<body>
	<h1>LoginLib Test Page</h1>
	<?php if (isset($message)) { echo '<p>'.$message.'</p>'; } ?>
	<form method="post" action="">
		<h2>Login</h2>
		<div>
			<label for="lg-username">Username:</label> <input type="text"
				name="lg-username" id="lg-username" placeholder="Username" required>
		</div>
		<div>
			<label for="lg-password">Password:</label> <input type="password"
				name="lg-password" id="lg-password" placeholder="Password" required>
		</div>
		<button type="submit">Login</button>
		<input type="hidden" name="method" value="login">
	</form>
	<br>
	<form method="post" action="">
		<h2>Register</h2>
		<div>
			<label for="rg-username">Username</label> <input type="text"
				name="rg-username" id="rg-username" placeholder="Username" required>
		</div>
		<div>
			<label for="rg-email">Email-Address</label> <input type="email"
				name="rg-email" id="rg-email" placeholder="Email-Address" required>
		</div>
		<div>
			<label for="rg-password">Password</label> <input type="password"
				name="rg-password" id="rg-password" placeholder="Password" required>
		</div>
		<div>
			<label for="rg-confirm">Confirm</label> <input type="password"
				name="rg-confirm" id="rg-confirm" placeholder="Confirm" required>
		</div>
		<button type="submit">Register</button>
		<input type="hidden" name="method" value="register">
	</form>
	<br>
	<?php if (isset($db)) { ?>
	<div>
		<span class="pre-header">Last DB query:</span>
		<pre><?php echo $db->getLastQuery(); ?></pre>
	</div>
	<div>
		<span class="pre-header">Last DB error:</span>
		<pre><?php echo $db->getLastError(); ?></pre>
	</div>
	<?php } ?>
	<div>
		<span class="pre-header">GLOBALS:</span>
		<pre><?php unset($GLOBALS['_SERVER']); unset($GLOBALS['db']); var_dump($GLOBALS); ?></pre>
	</div>
</body>
</html>
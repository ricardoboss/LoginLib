<?php
/**
 * This file contains the default configuration of LoginLib
 */
namespace LoginLib;

/**
 * @var The configuration of LoginLib
 */
$config = array(
	'database' => array(
		'host' => "localhost",
		'username' => "loginlib",
		'password' => "CmH93W4k",
		'db' => "LoginLib",
		'tables' => array(
			'accounts' => "accounts",
			'login-tokens' => "login_tokens"
		)
	)
);
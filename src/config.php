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
		'user' => "loginlib",
		'pass' => "CmH93W4k",
		'name' => "LoginLib",
		'tables' => array(
			'accounts' => "accounts",
			'login-tokens' => "login_tokens"
		)
	)
);
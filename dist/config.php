<?php
/**
 * This file contains the default configuration of LoginLib
 */
namespace LoginLib;

/**
 *
 * @var The configuration of LoginLib
 */
$config = array (
	'database' => array (
		'host' => "localhost",
		'username' => "loginlib",
		'password' => "CmH93W4k",
		'db' => "LoginLib"
	),
	'tables' => array (
		'accounts' => array (
			'name' => "accounts",
			'col_id' => "id",
			'col_username' => "username",
			'col_email' => "email",
			'col_password_hash' => "password_hash",
			'col_updated_at' => "updated_at",
			'col_registered_at' => "registered_at" 
		),
		'login-tokens' => "login_tokens"
	)
);
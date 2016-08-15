<?php
$config = array(
	'authentication' => array(
		'username' => "both", // 'username', 'email' or 'both'
		'storing' => "cookie", // 'cookie' or 'session'
		'login_after_registration' => true // true or false
	),
	'table' => array(
		'accounts' => array(
			'name' => "accounts",
			'col_id' => "id",
			'col_username' => "username",
			'col_email' => "email",
			'col_password_hash' => "password_hash",
			'col_updated_at' => "updated_at",
			'col_registered_at' => "registered_at"
		),
		'login_tokens' => array(
			'name' => "login_tokens",
			'col_id' => "id",
			'col_token' => "token",
			'col_account_id' => "account_id",
			'col_created_at' => "created_at",
			'col_logged_out' => "logged_out"
		)
	),
	'cookie' => array(
		'path' => "*",
		'domain' => "",
		'login_token' => array(
			'name' => "ll_lt",
			'expire' => (60 * 60 * 24 * 7 * 4) // 1 month
		),
		'token_id' => array(
			'name' => "ll_ti",
			'expire' => (60 * 60 * 24 * 7 * 4) // 1 month
		)
	)
);

$databaseConfig = array(
	'host' => "mcmainiac.ddns.net",
	'username' => "travis",
	'password' => "travis",
	'db' => "travis"
);
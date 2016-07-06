<?php
/**
 * This file contains the default configuration of LoginLib
 */
namespace LoginLib;

/**
 *
 * @var The configuration of LoginLib
 */
$config = array(
	'database' => array(
		'host' => "localhost",
		'username' => "loginlib",
		'password' => "CmH93W4k",
		'db' => "LoginLib"
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
			'col_account_id' => "account_id",
			'col_created_at' => "created_at",
			'col_logged_out' => "logged_out"
		)
	),
	'cookie' => array(
		'path' => "*",
		'domain' => $_SERVER['HTTP_HOST'],
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
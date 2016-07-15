<?php
/**
 * This file defines the config class for LoginLib
 * 
 * This is a helper class that manages the configuration of LoginLib
 */
namespace LoginLib;

use LoginLib\Exceptions\ConfigurationException;

/**
 * The Config class is a helper class for LoginLib
 */
class Config {
	/** @var array The main config array of LoginLib */
	private $config = array();
	
	/** @var array The default configuration for LoginLib */
	private static $default = array(
		'authentication' => array(
			'type' => "both"
		),
		'database' => array(
			'host' => "localhost",
			'username' => "root",
			'password' => "",
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
				'col_token' => "token",
				'col_account_id' => "account_id",
				'col_created_at' => "created_at",
				'col_logged_out' => "logged_out"
			)
		),
		'cookie' => array(
			'path' => "*",
			'domain' => "",//$_SERVER['HTTP_HOST'],
			'login_token' => array(
				'name' => "ll_lt",
				'expire' => 2419200 // 1 month
			),
			'token_id' => array(
				'name' => "ll_ti",
				'expire' => 2419200 // 1 month
			)
		)
	);
	
	/**
	 * The constructor checks the given config array and merges it with the default config
	 * 
	 * @throws ConfigurationException
	 * 
	 * @param array custom config
	 * 
	 * @return Config
	 */
	public function __construct($arr) {
		$this->config = $this->merge($arr, Config::$default);
	}
	
	/**
	 * This function returns a specific part of the config array
	 * 
	 * @throws ConfigurationException if the requested type could not be found
	 * 
	 * @param string $type The section of the config array
	 * 
	 * @return array
	 */
	public function get($type) {
		if (isset($this->config[$type]))
			return $this->config[$type];
		else {
			throw new ConfigurationException("[".$type."]", "The requested section could not be found!");
			exit;
		}
	}
	
	/**
	 * This functions merges the $src array with the $fallback array and returns the result
	 * 
	 * @param $src The source array
	 * @param $fallback The default array
	 * 
	 * @return array The merged array
	 */
	private function merge($src, $fallback) {
		$ret = $fallback;
		foreach ($src as $key => $value)
			if (array_key_exists($key, $ret))
				if (is_array($value))
					$ret[$key] = $this->merge($value, $fallback[$key]);
				else
					$ret[$key] = $value;

		return $ret;
	}
}
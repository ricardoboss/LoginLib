<?php
/**
 * This file contains the LoginLib class
 * 
 * The LoginLib class contains all the logic and mechanisms for it to work properly
 */
namespace LoginLib;

use LoginLib\Results\LoginResult;
use LoginLib\Results\RegisterResult;
use LoginLib\User;
use LoginLib\Config;
use LoginLib\Exceptions\ClassNotFoundException;
use LoginLib\Exceptions\ConfigurationException;

/**
 * A class that provides the background mechanics for login and registration forms
 *
 * Use this class to authenticate your users on your website. Design a corresponding
 * login/registration page on your website and this class will do the logic behind it.
 *
 * @author Ricardo (MCMainiac) Boss <ricardo.boss@web.de>
 * @copyright &copy; 2016 Ricardo Boss
 * @license https://creativecommons.org/licenses/by-sa/4.0/ Creative Commons BY SA 4.0
 * @link https://github.com/MCMainiac/LoginLib
 * @version 1.0.0
 */
class LoginLib {
	/** @var Config Used to store the configuration array of LoginLib */
	private $config;
	
	/** @var MysqliDb The database class object used to communitcate with the database */
	private $db;
	
	/**
	 * The constructor of LoginLib.
	 * 
	 * @throws ClassNotFoundException if the required MysqliDb class cannot be found or autoloaded
	 * @throws ConfigurationException if there is a problem with the provided config array
	 * 
	 * @param array $config The configuration array of LoginLib
	 * 
	 * @return LoginLib
	 */
	public function __construct($config) {
		$this->config = new Config($config);
		
		if (!class_exists("MysqliDb")) {
			throw new ClassNotFoundException("MysqliDb", "LoginLib requires MysqliDb to communicate with your database!");
			exit;
		}
		
		$this->db = new \MysqliDb($this->config->get('database'));
		
		foreach($this->config->get('table') as $table)
			if (!$this->db->tableExists($table['name'])) {
				throw new ConfigurationException("[table] => [".$table['name']."]", "Table does not exist: ".$table['name']);
				exit;
			}
	}
	
	/**
	 * This method is used to register a new user
	 * 
	 * @param string $username The username
	 * @param string $email The email address
	 * @param string $password The password
	 * @param string $confirm The password confirmation
	 * @param \function $callback A callback function that gets called when the function finished processing
	 * 
	 * @return RegisterResult
	 */
	public function register($username, $email, $password, $confirm, $callback = null) {
		// first of all, check the db
		$this->checkDb();
		
		// first of all check if the passwords are equal
		if ($password === $confirm) {
			// check if the username is given
			$this->db->where($this->getProp('table', 'accounts', 'col_username'), $username);
			$account = $this->db->getOne($this->getProp('table', 'accounts', 'name'));
			
			// check if this is NOT an account
			if (!$account) {
				// next check if the email exists
				// We need to check the username and the email address seperately (although they 
				// both can be used as the username) to tell the user what he has to change.
				$this->db->where($this->getProp('table', 'accounts', 'col_email'), $email);
				$account = $this->db->getOne($this->getProp('table', 'accounts', 'name'));
				
				// again, check if this is NOT an account
				if (!$account) {
					// seems like the passwords match, the username and the email address are not in use, sooo register the user
					
					// create password hash
					$passhash = crypt($password, sprintf("$2a$%02d$", 10) . strtr(base64_encode(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM)), '+', '.'));
					
					// insert new user into database and obtain id
					$id = $this->db->insert(
						$this->getProp('table', 'accounts', 'name'),
						array(
							$this->getProp('table', 'accounts', 'col_username') => $username,
							$this->getProp('table', 'accounts', 'col_email') => $email,
							$this->getProp('table', 'accounts', 'col_password_hash') => $passhash,
							$this->getProp('table', 'accounts', 'col_updated_at') => $this->db->now(),
							$this->getProp('table', 'accounts', 'col_registered_at') => $this->db->now(),
						)
					);
					
					$code = RegisterResult::SUCCESS;
				} else {
					$code = RegisterResult::EMAIL_GIVEN;
				}
			} else {
				$code = RegisterResult::USERNAME_GIVEN;
			}
		} else {
			$code = RegisterResult::PASSWORD_MISMATCH;
		}
		
		$result = new RegisterResult($code);
		
		if ($callback !== null)
			$callback($result);
		
		return $result;
	}
	
	/**
	 * Call this method to authenticate a registered user
	 * 
	 * @throws ConfigurationException when the user misconfigured his config.php
	 * 
	 * @param string $username The username or email-address of the user
	 * @param string $password The password or key the user provides
	 * @param \function $callback A callback function that gets called when the function finished processing
	 * 
	 * @return LoginResult
	 */
	public function login($username, $password, $callback = null) {
		// check the db, just in case a script runs very long
		$this->checkDb();

		// add where selector based on authentication type
		switch ($this->getProp('authentication', 'type')) {
			case 'username':
				$this->db->where($this->getProp('table', 'accounts', 'col_username'), $username);
				break;
			case 'email':
				$this->db->where($this->getProp('table', 'accounts', 'col_email'), $username);
				break;
			case 'both':
				$this->db->orWhere($this->getProp('table', 'accounts', 'col_username'), $username);
				$this->db->orWhere($this->getProp('table', 'accounts', 'col_email'), $username);
				break;
			
			default:
				throw new ConfigurationException("[authentication] => [type]", "Invalid authentication type: ".$this->getProp('authentication', 'type'));
		}
		
		// get one row only
		$account = $this->db->getOne($this->getProp('table', 'accounts', 'name'));
		
		// if the result is an account, proceed, otherweise return that no account is associated with that username/email address
		if (isset($account)) {
			// check if the password hashs are equal
			if (hash_equals($account[$this->getProp('table', 'accounts', 'col_password_hash')], crypt($password, $account[$this->getProp('table', 'accounts', 'col_password_hash')]))) {
				// if they are, the user is logged in
				
				// convert table row into a user object
				$user = new User($account, $this->getProp('table', 'accounts'));
				
				// generate a secure random string as a login token
				$login_token = bin2hex(openssl_random_pseudo_bytes(32));
				
				// store login token in database
				$id = $this->db->insert(
					$this->getProp('table', 'login_tokens', 'name'),
					array(
						$this->getProp('table', 'login_tokens', 'col_account_id') => $account[$this->getProp('table', 'accounts', 'col_id')],
						$this->getProp('table', 'login_tokens', 'col_created_at') => $this->db->now(),
						$this->getProp('table', 'login_tokens', 'col_token') => $login_token
					)
				);
				
				// store login token and token id in cookie
				$this->setCookie('login_token', $login_token);
				$this->setCookie('token_id', $id);
				
				$code = LoginResult::SUCCESS;
			} else {
				$code = LoginResult::PASSWORD_WRONG;
			}
		} else {
			$code = LoginResult::USERNAME_NOT_FOUND;
		}
		
		$result = new LoginResult($code);
		
		// call the callback in case one was specified
		if ($callback !== null)
			$callback($result);
			
		// return the result anyway
		return $result;
	}
	
	/**
	 * This method is used to log users out
	 * 
	 * @return bool
	 */
	public function logout() {
		if ($this->isLoggedIn()) {
			$this->setCookie('login_token', null, -1);
			$this->setCookie('token_id', null, -1);
		}
		
		return true;
	}
	
	/**
	 * This function returns true if the browser is logged in or false if not
	 * 
	 * @return bool
	 */
	public function isLoggedIn() {
		$login_token = @$_COOKIE[$this->getProp('cookie', 'login_token', 'name')];
		$token_id = @$_COOKIE[$this->getProp('cookie', 'token_id', 'name')];
		
		if (isset($login_token) && isset($token_id)) {
			$this->db->where($this->getProp('table', 'login_tokens', 'col_id'), $token_id);
			$this->db->where($this->getProp('table', 'login_tokens', 'col_token'), $login_token);
			$r = $this->db->getOne($this->getProp('table', 'login_tokens', 'name'));
			return $r ? true : false;
		} else {
			return false;
		}
	}
	
	/**
	 * Check the database connection, ping it or reconnect if neccessary
	 * 
	 * @return void
	 */
	private function checkDb() {
		if (! $this->db->ping())
			$this->db->connect();
	}
	
	/**
	 * Sets a cookie with custom expire time
	 * 
	 * @paran string $id The id of the cookie
	 * @param string $value The value of the cookie
	 * @param int|null $expires The expiration time of the cookie
	 * 
	 * @return bool
	 */
	private function setCookie($id, $value, $expires = null) {
		if ($expires == null)
			$expires = time() + $this->getProp('cookie', $id, 'expire');

		return \setcookie(
			$this->getProp('cookie', $id, 'name'), 
			$value, 
			$expires, 
			$this->getProp('cookie', 'path'), 
			$this->getProp('cookie', 'domain'), 
			false, 
			false
		);
	}
	
	/**
	 * Get a specific config property
	 * 
	 * @throws ConfigurationException if the requested property is not set
	 *  
	 * @param string $type Either 'table', 'cookie' or 'database'
	 * @param string $id The id of the (parent) prop in the config
	 * @param string|null $prop The id of the prop itself
	 * 
	 * @return string|null
	 */
	private function getProp($type, $id, $prop = null) {
		$t = $this->config->get($type);
		if ($prop !== null) {
			$p = @$t[$id][$prop];
			if (!isset($p)) {
				throw new ConfigurationException("[".$type."] => [".$id."] => [".$prop."]", "The requested property could not be found!");
			}
			return $p;
		} else {
			$p = $t[$id];
			if (!isset($p)) {
				throw new ConfigurationException("[".$type."] => [".$id."]", "The requested config id could not be found!");
			}
			return $p;
		}
	}
}

<?php
/**
 * This file contains the LoginLib class
 *
 * The LoginLib class contains all the logic and mechanisms for it to work properly
 */
namespace LoginLib;

use LoginLib\Exceptions\ConfigurationException;
use LoginLib\Exceptions\DatabaseException;
use LoginLib\Results\LoginResult;
use LoginLib\Results\RegisterResult;

/**
 * A class that provides the background mechanics for login and registration forms
 *
 * Use this class to authenticate your users on your website. Design a corresponding
 * login/registration page on your website and this class will do the logic behind it.
 *
 * @author Ricardo Boss <ricardo.boss@web.de>, aka MCMainiac
 * @copyright &copy; 2016 Ricardo Boss
 * @License https://creativecommons.org/licenses/by-sa/4.0/ Creative Commons BY SA 4.0
 * @link https://github.com/MCMainiac/LoginLib
 * @version 1.1.0
 */
class LoginLib {
	/** @var Config Used to store the configuration array of LoginLib */
	private $config;
	
	/** @var IDatabase The database class object used to communitcate with the database */
	private $db;
	
	/** @var string The current LoginLib version */
	const version = "1.1.0";
	
	/**
	 * The constructor of LoginLib.
	 *
	 * @throws DatabaseException
	 * @throws ConfigurationException
	 *
	 * @param array $config The configuration array of LoginLib
	 * @param IDatabase $database Your database implementation of the IDatabase interface
	 *
	 * @return LoginLib
	 */
	public function __construct(array $config, IDatabase &$database) {
		$this->config = new Config($config);
		
		$this->db = &$database;
		
		if (!$this->checkDb())
			throw new DatabaseException("Could not connect to database: " . $this->db->getLastError());
		
		foreach($this->config->get('table') as $table)
			if (!$this->db->tableExists($table['name'])) {
				throw new ConfigurationException("[table] => [".$table['name']."]", "Table does not exist: ".$table['name']);
			}

        return $this;
	}
	
	/**
	 * This method is used to register a new user
	 *
	 * @throws ConfigurationException
	 *
	 * @param string $username The username
	 * @param string $email The email address
	 * @param string $password The password
	 * @param string $confirm The password confirmation
	 * @param \callable $registercallback A callback function that gets called when the function finished processing
	 * @param \callable $logincallback A callback function that gets passed to the login method if login_after_registration is true
	 *
	 * @return RegisterResult
	 */
	public function register($username, $email, $password, $confirm, callable $registercallback = null, callable $logincallback = null) {
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
					$this->db->insert(
						$this->getProp('table', 'accounts', 'name'),
						array(
							$this->getProp('table', 'accounts', 'col_username') => $username,
							$this->getProp('table', 'accounts', 'col_email') => $email,
							$this->getProp('table', 'accounts', 'col_password_hash') => $passhash,
							$this->getProp('table', 'accounts', 'col_updated_at') => $this->db->now(),
							$this->getProp('table', 'accounts', 'col_registered_at') => $this->db->now()
						)
					);
					
					if ($this->getProp('authentication', 'login_after_registration') == true) {
						switch (strtolower($this->getProp('authentication', 'username'))) {
							case 'both':
							case 'username':
								$loginname = $username;
								break;
							case 'email':
								$loginname = $email;
								break;
									
							default:
								throw new ConfigurationException("[authentication] => [username]", "Invalid authentication type for username: ".$this->getProp('authentication', 'username'));
						}
						
						$this->login($loginname, $password, $logincallback);
					}
					
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
		
		if ($registercallback !== null)
			$registercallback($result);
		
		return $result;
	}
	
	/**
	 * Call this method to authenticate a registered user
	 *
	 * @throws ConfigurationException
	 *
	 * @param string $username The username or email-address of the user
	 * @param string $password The password or key the user provides
	 * @param \callable $callback A callback function that gets called when the function finished processing
	 *
	 * @return LoginResult
	 */
	public function login($username, $password, callable $callback = null) {
		// check the db, just in case a script runs very long
		$this->checkDb();

		// add where selector based on authentication type
		switch (strtolower($this->getProp('authentication', 'username'))) {
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
				throw new ConfigurationException("[authentication] => [username]", "Invalid authentication type for username: ".$this->getProp('authentication', 'username'));
		}
		
		// get one row only
		$account = $this->db->getOne($this->getProp('table', 'accounts', 'name'));
		
		// if the result is an account, proceed, otherweise return that no account is associated with that username/email address
		if (isset($account)) {
			// check if the password hashs are equal
			if (hash_equals($account[$this->getProp('table', 'accounts', 'col_password_hash')], crypt($password, $account[$this->getProp('table', 'accounts', 'col_password_hash')]))) {
				// if they are, the user is logged in
				
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
				
				$storing = strtolower($this->getProp('authentication', 'storing'));
				if ($storing == "cookie") {
					
					// store login token and token id in cookie
					$this->setCookie('login_token', $login_token);
					$this->setCookie('token_id', $id);
					
				} else if ($storing == "session") {
					
					// start session and store token and id in session
					$this->initSession();
					
					$_SESSION['login_token'] = $login_token;
					$_SESSION['token_id'] = $id;
					
				} else
					throw new ConfigurationException("[authentication] => [storing]", "Invalid storing type for login credentials: ".$storing);
				
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
	 * @throws ConfigurationException
	 *
	 * @return bool
	 */
	public function logout() {
		if ($this->isLoggedIn()) {
			$storing = strtolower($this->getProp('authentication', 'storing'));
			
			if ($storing == "cookie") {
				
				$token_id = @$_COOKIE[$this->getProp('cookie', 'token_id', 'name')];
				$login_token = @$_COOKIE[$this->getProp('cookie', 'login_token', 'name')];
				
				// unset cookies
				if (!$this->setCookie('login_token', null, -1) || !$this->setCookie('token_id', null, -1))
					return false;
					
			} else if ($storing == "session") {
				
				$token_id = @$_SESSION['token_id'];
				$login_token = @$_SESSION['login_token'];
				
				// destroy session
				$this->closeSession(true);
				
			} else
				throw new ConfigurationException("[authentication] => [storing]", "Invalid storing type for login credentials: ".$storing);

			// null-check the values
			if ($token_id == null || $login_token == null)
				return false;
			
			// add row selectors
			$this->db->where(
				$this->getProp('table', 'login_tokens', 'col_id'),
				$token_id
			);
			
			$this->db->where(
				$this->getProp('table', 'login_tokens', 'col_token'),
				$login_token
			);
			
			// set logout timestamp
			$r = $this->db->update(
				$this->getProp('table', 'login_tokens', 'name'),
				array(
					'logged_out' => $this->db->now()
				)
			);
			
			// if the result is not set, the login token could not be found although the user is logged in
			if (!$r)
				return false;
		}
		
		return true;
	}
	
	/**
	 * This function returns true if the browser is logged in or false if not
	 *
	 * @throws ConfigurationException
	 *
	 * @return bool
	 */
	public function isLoggedIn() {
		$storing = strtolower($this->getProp('authentication', 'storing'));
		
		if ($storing == "cookie") {
			
			$login_token = @$_COOKIE[$this->getProp('cookie', 'login_token', 'name')];
			$token_id = @$_COOKIE[$this->getProp('cookie', 'token_id', 'name')];
			
		} else if ($storing == "session") {
			
			$login_token = @$_SESSION['login_token'];
			$token_id = @$_SESSION['token_id'];
			
		} else
			throw new ConfigurationException("[authentication] => [storing]", "Invalid storing type for login credentials: ".$storing);
		
		if (isset($login_token) && isset($token_id)) {
			
			$this->db->where($this->getProp('table', 'login_tokens', 'col_id'), $token_id);
			$this->db->where($this->getProp('table', 'login_tokens', 'col_token'), $login_token);
			
			$r = $this->db->getOne($this->getProp('table', 'login_tokens', 'name'));
			
			return $r ? true : false;
			
		} else
			return false;
	}
	
	/**
	 * A simple to string method that returns the version string
	 *
	 * @return string The textual representation of LoginLib
	 */
	public function __toString() {
		return LoginLib::version();
	}
	
	/**
	 * The destructor is used to close the current session
	 *
	 * @return void
	 */
	public function __destruct() {
		$this->closeSession();
	}
	
	/**
	 * This method returns (or echoes) the current LoginLib version
	 *
	 * @param bool $echo
	 *
	 * @return string The current LoginLib version
	 */
	public static function version($echo = false) {
		if ($echo)
			echo "LoginLib v".LoginLib::version."\n";
		
		return LoginLib::version;
	}
	
	/**
	 * Check the database connection, ping it and reconnect if neccessary
	 *
	 * @return bool if everything is ok
	 */
	private function checkDb() {
		if (! $this->db->ping()) {
            $this->db->connect();

            return $this->db->ping();
        } else
			return true;
	}
	
	/**
	 * Sets a cookie with custom expire time
	 *
	 * @param string $id The id of the cookie
	 * @param mixed $value The value of the cookie
	 * @param int|null $expires The expiration time of the cookie
	 *
	 * @return bool True if the cookie has been set
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
	 * @return string|null The requested property of the config
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
	
	/**
	 * Initializes a session
	 *
	 * @return void
	 */
	private function initSession() {
		if (\session_status() != PHP_SESSION_ACTIVE) {
			
			// set session cookie params
			\session_set_cookie_params(
				0, // delete session at the end
				$this->getProp('cookie', 'path'),
				$this->getProp('cookie', 'domain'),
				false,
				false
			);
			
			// start a new session
			\session_start();
		}
	}
	
	/**
	 * Closes a session
	 *
	 * @param bool $destroy If the session should be destroyed or not
	 *
	 * @return void
	 */
	private function closeSession($destroy = false) {
		if (\session_status() != PHP_SESSION_DISABLED) {
			if ($destroy) {
				\session_unset(); // remove all the data
					
				\session_destroy(); // destroy the session
			} else {
				\session_write_close(); // save all the data and close the session
			}
		}
	}
}

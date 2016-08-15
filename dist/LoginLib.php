<?php
/**
 * This file contains all classes for LoginLib.
 * 
 * Dont't forget the dependency on MysqliDb!
 */
namespace LoginLib;

/**
 * The Config class is a helper class for LoginLib
 */
class Config {
	/** @var array The main config array of LoginLib */
	private $config = array();
	
	/** @var array The default configuration for LoginLib */
	private static $default = array(
		'authentication' => array(
			'username' => "both",
			'storing' => "cookie",
			'login_after_registration' => true
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

/**
 * Exception class for the case that a class was not found
 */
class DatabaseException extends \Exception {
	/**
	 * The constrcutor of DatabaseException
	 *
	 * @param string $message The message of the exception
	 * @param int $code The code of the exception
	 * @param \Exception $previous The previous exception
	 *
	 * @return DatabaseException
	 */
	public function __construct($message = "", $code = 0, $previous = null) {
		parent::__construct ($message, $code, $previous);
	}
}

/**
 * Exception that gets thrown if the user miconfigured their config
 */
class ConfigurationException extends \Exception {
	/** @var string the misconfigured property */
	private $prop;

	/**
	 * The constrcutor of ConfigurationExceptions
	 *
	 * @param string $prop The misconfigured property; format: [key] => [prop]
	 * @param string $message The message of the exception
	 * @param int $code The code of the exception
	 * @param \Exception $previous The previous exception
	 *
	 * @return ConfigurationException
	 */
	public function __construct($prop, $message = "", $code = 0, $previous = null) {
		parent::__construct ($message, $code, $previous);
		
		$this->prop = $prop;
	}
	
	/**
	 * A method to return the misconfigured property
	 *
	 * @return string The misconfigured config property
	 */
	public function getProp() {
		return $this->prop;
	}
}

/**
 * This is the interface used to communicate with your database
 */
interface IDatabase {
	/**
	 * The constructor of your Database implementation must use an array as configuration
	 *
	 * @param array $config Your config should have the following keys: 'host', 'username', 'password', 'db'
	 *
	 * @return void
	 */
	function __construct(array $config);
	
	/**
	 * This function checks if a given table exists in your database
	 *
	 * @param string $tableName The name of the table
	 *
	 * @return bool True if the table exists, false otherwise
	 */
	function tableExists($tableName);
	
	/**
	 * Add an " AND " to your where query.
	 * Example:
	 * 	<code>"SELECT * FROM users WHERE col1 = 1"
	 *	=> "SELECT * FROM users WHERE col1 = 1 <u>AND</u> col2 = 2"</code>
	 *
	 * @param string $column The column name
	 * @param string $andValue The needed value
	 */
	function where($column, $andValue);

	/**
	 * Add an <code>" OR "</code> to your where query.
	 * Example:
	 * 	<code>"SELECT * FROM users WHERE col1 = 1"
	 *	=> "SELECT * FROM users WHERE col1 = 1 <u>OR</u> col2 = 2"</code>
	 *
	 * @param string $column The column name
	 * @param string $orValue The other value
	 */
	function orWhere($column, $orValue);
	
	/**
	 * This functions returns the result of selecting one row with the prepared query
	 *
	 * @param string $tableName The name of the table
	 *
	 * @return array|null The selected row
	 */
	function getOne($tableName);
	
	/**
	 * This function inserts data in selected columns (using an associative array) into a table
	 *
	 * @param string $tableName The name of the table
	 * @param array $data The data to insert
	 */
	function insert($tableName, array $data);
	
	/**
	 * This function updates values in selected columns (using an associative array) in a table
	 *
	 * @param string $tableName The name of the table
	 * @param array $data The data to update
	 */
	function update($tableName, array $data);
	
	/**
	 * Used to keep unused databse connections open
	 *
	 * @return bool True if the connection is established
	 */
	function ping();
	
	/**
	 * This function reconnects to your Database, if neccessary
	 *
	 * @return void
	 */
	function connect();
	
	/**
	 * Method returns generated interval function as an insert/update function
	 *
	 * @return array
	 */
	function now();
	
	/**
	 * A function to run raw sql queries
	 *
	 * @param string the query
	 *
	 * @return array
	 */
	function rawQuery($q);
}

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
 * @version 1.0.1
 */
class LoginLib {
	/** @var Config Used to store the configuration array of LoginLib */
	private $config;
	
	/** @var IDatabase The database class object used to communitcate with the database */
	private $db;
	
	/** @var string The current LoginLib version */
	const version = "1.0.1";
	
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
			throw new DatabaseException("Could not connect to database!");
		
		foreach($this->config->get('table') as $table)
			if (!$this->db->tableExists($table['name'])) {
				throw new ConfigurationException("[table] => [".$table['name']."]", "Table does not exist: ".$table['name']);
				exit;
			}
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
	 * @param \function $registercallback A callback function that gets called when the function finished processing
	 * @param \function $logincallback A callback function that gets passed to the login method if login_after_registration is true
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
					$id = $this->db->insert(
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
	 * @param \function $callback A callback function that gets called when the function finished processing
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
		if (! $this->db->ping())
			return $this->db->connect();
		else
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


/**
 * An abstract class that is used to provide results of methods
 */
abstract class MethodResult {
	const UNDEFINED = - 1;
	
	/** @var int Contains the method result */
	protected $result = MethodResult::UNDEFINED;
	
	/**
	 * A constructor for LoginResults
	 *
	 * @param int $result The result of one of the methods
	 *
	 * @return MethodResult
	 */
	public function __construct($result) {
		$this->result = $result;
	}
	
	/**
	 * Returns the result of this LoginResult
	 *
	 * @return int
	 */
	public function getResult() {
		return $this->result;
	}
	
	/**
	 * Returns a simple result in form of a boolean value
	 *
	 * @return bool
	 */
	public abstract function getSimpleResult();
	
	/**
	 * Overwrite the toString() method to return the string version of the result
	 *
	 * @return string
	 */
	public abstract function __toString();
}

/**
 * This class is for results of the login method
 */
class LoginResult extends MethodResult {
	const USERNAME_NOT_FOUND = 0;
	const PASSWORD_WRONG = 1;
	const SUCCESS = 2;
	
	/**
	 * Returns a simple result in form of a boolean value
	 *
	 * @return bool
	 */
	public function getSimpleResult() {
		switch ($this->result) {
			case LoginResult::SUCCESS:
				return true;
			
			default:
				return false;
		}
	}
	
	/**
	 * Overwrite the toString() method to return the string version of the result
	 *
	 * @return string
	 */
	public function __toString() {
		switch ($this->result) {
			case LoginResult::USERNAME_NOT_FOUND:
				return "LoginResult::USERNAME_NOT_FOUND";
			case LoginResult::PASSWORD_WRONG:
				return "LoginResult::PASSWORD_WRONG";
			case LoginResult::SUCCESS:
				return "LoginResult::SUCCESS";
			default:
				return "LoginResult::UNDEFINED";
		}
	}
}

/**
 * This class is for results of the register method
 */
class RegisterResult extends MethodResult {
	const USERNAME_GIVEN = 0;
	const EMAIL_GIVEN = 1;
	const PASSWORD_MISMATCH = 2;
	const SUCCESS = 3;
	
	/**
	 * Returns a simple result in form of a boolean value
	 *
	 * @return bool
	 */
	public function getSimpleResult() {
		switch ($this->result) {
			case RegisterResult::SUCCESS:
				return true;
			
			default:
				return false;
		}
	}
	
	/**
	 * Overwrite the toString() method to return the string version of the result
	 *
	 * @return string
	 */
	public function __toString() {
		switch ($this->result) {
			case RegisterResult::USERNAME_GIVEN:
				return "RegisterResult::USERNAME_GIVEN";
			case RegisterResult::EMAIL_GIVEN:
				return "RegisterResult::EMAIL_GIVEN";
			case RegisterResult::PASSWORD_MISMATCH:
				return "RegisterResult::PASSWORD_MISMATCH";
			case RegisterResult::SUCCESS:
				return "RegisterResult::SUCCESS";
			default:
				return "RegisterResult::UNDEFINED";
		}
	}
}


/**
 * The user class is used to hold data secure and easy to access (for LoginLib). 
 */
class User {
	/** @var int The id of the user */
	protected $id;
	
	/** @var string The username of this user */
	protected $username;
	
	/** @var string The email address of the user */
	protected $email;
	
	/** @var int When the user was last updated (UNIX timestamp) */
	protected $updated_at;
	
	/** @var int The time when the user created their account (UNIX timestamp) */
	protected $registered_at;
	
	/**
	 * The constructor of the User class 
	 * 
	 * @param array $account_row          The corresponding account row for this user from the accounts table
	 * @param array $account_table_config The config array holding the column names for the account table
	 * 
	 * @return User
	 */
	public function __construct($account_row, $account_table_config) {
		$this->id = $account_table_config['col_id'];
		$this->username = $account_table_config['col_username'];
		$this->email = $account_table_config['col_email'];
		$this->updated_at = strtotime($account_table_config['col_updated_at']);
		$this->registered_at = strtotime($account_table_config['col_registered_at']);
	}
	
	/**
	 * This function returns the id of the account associated with this user
	 * 
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Returns the username of this user
	 * 
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}
	
	/**
	 * Returns the email address of this user
	 * 
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}
	
	
	/**
	 * The timestamp of the last time this user has been updated in the database
	 * 
	 * @return int
	 */
	public function getUpdated() {
		return $this->updated_at;
	}
	
	/**
	 * The time when the user created their account
	 * 
	 * @return int
	 */
	public function getRegistered() {
		return $this->registered_at;
	}
	
	
	/**
	 * Returns this user as an array
	 * 
	 * @return array
	 */
	public function getAsArray() {
		return array(
			'id' => $this->id,
			'username' => $this->username,
			'email' => $this->email,
			'updated_at' => $this->updated_at,
			'registered_at' => $this->registered_at
		);
	}
}
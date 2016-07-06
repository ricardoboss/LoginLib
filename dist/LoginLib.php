<?php
/**
 * This file contains all classes for LoginLib.
 * 
 * Dont't forget the dependency on MysqliDb!
 */
namespace LoginLib;

use MysqliDb\MysqliDb;
use LoginLib\Results\MethodResult;
use LoginLib\Results\LoginResult;
use LoginLib\Results\RegisterResult;
use LoginLib\Users\User;
use LoginLib\Exceptions\ClassNotFoundException;

/**
 * A class that provides the background mechanics for login and registration forms
 *
 * Use this class to authenticate your users on your website. Design a corresponding login/registration page on your website and this class will do the logic behind it.
 *
 * @author Ricardo (MCMainiac) Boss <ricardo.boss@web.de>
 * @copyright &copy; 2016
 * @license https://creativecommons.org/licenses/by-sa/4.0/ Creative Commons BY SA 4.0
 * @link https://github.com/MCMainiac/LoginLib
 * @version 0.1.0
 */
class LoginLib {
	/** @var array Used to store the configuration array of LoginLib */
	private $config;
	
	/** @var MysqliDb The database class object used to communitcate with the database */
	private $db;
	
	/**
	 * The constructor of LoginLib.
	 * 
	 * @throws ClassNotFoundException if the required MysqliDb class cannot be found or autoloaded
	 * 
	 * @param array $config The configuration array of LoginLib
	 * 
	 * @return LoginLib
	 */
	public function __construct($config) {
		if (!class_exists("MysqliDb\MysqliDb")) {
			throw new ClassNotFoundException("LoginLib requires MysqliDb to run!", 1);
			exit;
		}
		
		$this->config = $config;
		
		$this->db = new MysqliDb($config['database']);
		// TODO: check if tables exist, if not => create them
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
	 * @return R\RegisterResult
	 */
	public function register($username, $email, $password, $confirm, $callback = null) {
		// first of all, check the db
		$this->checkDb();
		
		// first of all check if the passwords are equal
		if ($password === $confirm) {
			// check if the username is given
			$this->db->where($this->config['table']['accounts']['col_username'], $username);
			$account = $this->db->getOne($this->config['table']['accounts']['name']);
			
			// check if this is NOT an account
			if (!$account) {
				// next check if the email exists
				// We need to check the username and the email address seperately (although they 
				// both can be used as the username) to tell the user what he has to change.
				$this->db->where($this->config['table']['accounts']['col_email'], $email);
				$account = $this->db->getOne($this->config['table']['accounts']['name']);
				
				// again, check if this is NOT an account
				if (!$account) {
					// seems like the passwords match, the username and the email address are not in use, sooo register the user
					// TODO: database stuff to register the user
					
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
	 * @param string $username The username or email-address of the user
	 * @param string $password The password or key the user provides
	 * @param \function $callback A callback function that gets called when the function finished processing
	 * 
	 * @return LoginResult
	 */
	public function login($username, $password, $callback = null) {
		// check the db, just in case a script runs very long
		$this->checkDb();
		
		// add where selector
		$this->db->where($this->config['table']['accounts']['col_username'], $username);
		
		// get one row only
		$account = $this->db->getOne($this->config['table']['accounts']['name']);
		
		// if the result is an account, proceed, otherweise return that no account is associated with that username/email address
		if (isset($account)) {
			// check if the password hashs are equal
			if (hash_equals($account[$this->config['table']['accounts']['col_password_hash']], crypt($password, $account[$this->config['table']['accounts']['col_password_hash']]))) {
				// if they are, the user is logged in
				
				// convert table row into a user object
				$user = new User($account, $this->config['table']['accounts']);
				
				// generate a secure random string as a login token
				$login_token = bin2hex(openssl_random_pseudo_bytes(32));
				
				// store login token in database
				$id = $db->insert(
					$this->config['table']['login_tokens']['name'],
					array(
						$this->config['table']['login_tokens']['col_account_id'] => $user->getId(),
						$this->config['table']['login_tokens']['col_created_at'] => $db->now(),
						$this->config['table']['login_tokens']['col_token'] => $login_token
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
	 * @return void
	 */
	public function logout() {
		// TODO: remove cookies, sessions, do database shit and ya
	}
	
	/**
	 * This function returns true if the browser is logged in or false if not
	 * 
	 * @return bool
	 */
	public function isLoggedIn() {
		$login_token = @$_COOKIE[$this->config['cookie']['login_token']['name']];
		$token_id = @$_COOKIE[$this->config['cookie']['token_id']['name']];
		
		if (isset($login_token) && isset($token_id)) {
			$db->where($this->config['table']['login_tokens']['col_id'], $token_id);
			$db->where($this->config['table']['login_tokens']['col_token'], $login_token);
			$token = $db->getOne($this->config['table']['login_tokens']['name']);
			
			if ($token) {
				return true;
			} else {
				return false;
			}
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
	 * Sets a cookie
	 * 
	 * @param string $name The name of the cookie
	 * @param mixed $value The value of the cookie
	 * @param int $expires The lifetime of the cookie
	 * 
	 * @return bool
	 */
	private function setCookie($id, $value) {
		return \setcookie($this->config['cookie'][$id]['name'], $value, $this->config['cookie'][$id]['expires'], $this->config['cookie']['path'], $this->config['cookie']['domain'], false, false);
	}
}
<?php
/**
 * This file defines the MethodResults
 * 
 * MethodResults get return from the methods of LoginLib
 */
namespace LoginLib\Results;

/**
 * An abstract class that is used to provide results of methods
 */
abstract class MethodResult {
	const UNDEFINED = - 1;
	
	/** @var int Contains the method result */
	private $result = MethodResult::UNDEFINED;
	
	/**
	 * A constructor for LoginResults
	 *
	 * @param int $result The result of one of the methods
	 *
	 * @return LoginResult
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
			case SUCCESS:
				return true;
			
			default:
				return false;
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
			case SUCCESS:
				return true;
			
			default:
				return false;
		}
	}
}
<?php
/**
 * This file provides the user class
 */
namespace LoginLib\Users;

/**
 * The user class is used to hold data secure and easy to access (for LoginLib). 
 */
class User {
	/** @var int The id of the user */
	private $id;
	
	/** @var string The username of this user */
	private $username;
	
	/** @var string The email address of the user */
	private $email;
	
	/** @var int When the user was last updated (UNIX timestamp) */
	private $updated_at;
	
	/** @var int The time when the user created their account (UNIX timestamp) */
	private $registered_at;
	
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
}<?php
/**
 * This file contains all exception classes that may be thrown by method of the LoginLib class
 */
namespace LoginLib\Exceptions;

/**
 * Exception class for the case that a class was not found
 */
class ClassNotFoundException extends \Exception {
	/**
	 * The constrcutor of ClassNotFoundExceptions just use the default exception class atm
	 *
	 * @param string $message The message of the exception
	 * @param int $code The code of the exception
	 * @param \Exception $previous The previous exception
	 * 
	 * @return ClassNotFoundException
	 */
	public function __construct($message = "", $code = 0, $previous = null) {
		parent::__construct ( $message, $code, $previous );
	}
}

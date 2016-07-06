<?php
/**
 * This file contains the LoginLib class
 * 
 * The LoginLib class contains all the logic and mechanisms for it to work properly
 */
namespace LoginLib;

use MysqliDb\MysqliDb as MysqliDb;

/**
 * A class that provides the background mechanics for login and registration forms
 * 
 * Use this class to authenticate your users on your website. Design a corresponding login/registration page on your website and this class will do the logic behind it.
 * 
 * @category Library
 * @package LoginLib
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
		if (!class_exists("MysqliDb")) {
			throw new ClassNotFoundException("LoginLib requires MysqliDb to run!", 1);
			exit;
		}
		
		$this->config = $config;
		
		$this->db = new MysqliDb ( $config ['database'] );
		// TODO: check if tables exist, if not => create them
	}
	
	/**
	 * Call this method to authenticate a registered user
	 * 
	 * @param string $username The username or email-address of the user
	 * @param string $password The password or key the user provides
	 * @param function $callback A callback function that gets called when the function finished processing
	 * 
	 * @return LoginResult
	 */
	public function login($username, $password, $callback = null) {
		// check the db, just in case a script runs very long
		checkDb ();
		
		// add where selector
		$db->where($this->config['table']['accounts']['col_username'], $username);
		
		// get one row only
		$account = $db->getOne($this->config['table']['accounts']['name']);
		
		// if the result is an account, proceed, otherweise return that no account is associated with that username/email address
		if (isset($account)) {
			// check if the password hashs are equal
			if (hash_equals($account[$config['table']['accounts']['col_password_hash']], crypt($password, $account[$config['table']['accounts']['col_password_hash']]))) {
				// if they are, the user is logged in
				
				// TODO: cookie and session stuff for login checks and so on
				
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
	 * This method is used to register a new user
	 * 
	 * @param string $username The username
	 * @param string $email The email address
	 * @param string $password The password
	 * @param string $confirm The password confirmation
	 * @param function $callback A callback function that gets called when the function finished processing
	 * 
	 * @return RegisterResult
	 */
	public function register($username, $email, $password, $confirm, $callback = null) {
		// first of all, check the db
		checkDb();
		
		// first of all check if the passwords are equal
		$equal = true;
		for ($i = 0; $i = length($password); $i++) {
			if ($password[$i] !== $confirm[$i]) {
				$equal = false;
				break;
			}
		}
		
		if ($equal) {
			// check if the username is given
			$db->where($this->config['table']['accounts']['col_username'], $username);
			$account = $db->getOne($this->config['table']['accounts']['name']);
			
			// check if this is NOT an account
			if (!$account) {
				// next check if the email exists
				// We need to check the username and the email address seperately (although they 
				// both can be used as the username) to tell the user what he has to change.
				$db->where($this->config['table']['accounts']['col_email'], $email);
				$account = $db->getOne($this->config['table']['accounts']['name']);
				
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
		// TODO: do cookie and session and database checks and so on to authenticate the user
		return false;
	}
	
	/**
	 * Check the database connection, ping it or reconnect if neccessary
	 * 
	 * @return void
	 */
	private function checkDb() {
		if (! $this->db->ping)
			$this->db->connect ();
	}
}

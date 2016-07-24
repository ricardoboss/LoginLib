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

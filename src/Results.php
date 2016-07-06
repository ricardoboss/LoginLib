<?php
/**
 * This file defines the MethodResults
 * 
 * MethodResults get return from the methods of LoginLib
 */
namespace LoginLib;

/**
 * An abstract class that is used to provide results of methods
 */
abstract class MethodResult {
	const UNDEFINED = -1;

	/** @var int Contains the method result */
	private $result = MethodResult::UNDEFINED;

	/**
	 * A constructor for LoginResults
	 * 
	 * @param int $result The result of the login function, has to be one of the constants of this class
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
}

/**
 * This class is for results of the login method
 */
class LoginResult extends MethodResult {
	const USERNAME_NOT_FOUND =	0;
	const PASSWORD_WRONG =		1;
	const SUCCESS =				2;
}

/**
 * This class is for results of the register method
 */
class RegisterResult extends MethodResult {
	const USERNAME_GIVEN =		0;
	const EMAIL_GIVEN =			1;
	const PASSWORD_MITMATCH =	2;
}

<?php
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

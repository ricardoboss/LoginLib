<?php
/**
 * This file contains all exception classes that may be thrown by methods of the LoginLib class
 */
namespace LoginLib\Exceptions;

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
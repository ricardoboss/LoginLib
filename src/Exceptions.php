<?php
/**
 * This file contains all exception classes that may be thrown by methods of the LoginLib class
 */
namespace LoginLib\Exceptions;

/**
 * Exception class for the case that a class was not found
 */
class ClassNotFoundException extends \Exception {
	/** @var string The classname that wasn't found */
	private $classname;

	/**
	 * The constrcutor of ClassNotFoundExceptions
	 *
	 * @param string $class The searched classname
	 * @param string $message The message of the exception
	 * @param int $code The code of the exception
	 * @param \Exception $previous The previous exception
	 * 
	 * @return ClassNotFoundException
	 */
	public function __construct($classname, $message = "", $code = 0, $previous = null) {
		parent::__construct ($message, $code, $previous);
		
		$this->classname = $classname;
	}
	
	/**
	 * Returns the missing classname
	 * 
	 * @return string The required class name
	 */
	public function getClassname() {
		return $this->classname;
	}
}

/**
 * Ecxeption that gets thrown if the user miconfigured their config
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
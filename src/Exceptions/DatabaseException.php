<?php
/**
 * This file defines a DatabaseException with the superclass \Exception
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

        return $this;
    }
}
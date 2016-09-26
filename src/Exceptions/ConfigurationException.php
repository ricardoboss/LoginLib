<?php
/**
 * This file defines the ConfigurationException with the superclass \Exception
 */
namespace LoginLib\Exceptions;

/**
 * Exception that gets thrown if the user misconfigured their config
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

        return $this;
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
<?php
/**
 * Created by PhpStorm.
 * User: Ricardo
 * Date: 26.09.2016
 * Time: 15:29
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

        return $this;
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
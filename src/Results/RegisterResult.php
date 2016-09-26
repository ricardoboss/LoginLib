<?php
/**
 * Created by PhpStorm.
 * User: Ricardo
 * Date: 26.09.2016
 * Time: 15:30
 */

namespace LoginLib\Results;

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

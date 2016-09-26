<?php
/**
 * Created by PhpStorm.
 * User: Ricardo
 * Date: 26.09.2016
 * Time: 15:30
 */

namespace LoginLib\Results;

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

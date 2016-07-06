<?php
/**
 * This file contains the LoginLib class
 * 
 * The LoginLib class contains all the logic and mechanisms for it to work properly
 */

/**
 * A class that provides the background mechanics for login and registration forms
 * 
 * Use this class to authenticate your users on your website. Design a corresponding login/registration page on your website and this class will do the logic behind it.
 * 
 * @author Ricardo (MCMainiac) Boss: @MCMainiac_ (Twitter)
 * @version 0.1.0
 */
class LoginLib {
	/**
	 * Call this method to authenticate a registered user
	 * 
	 * @param string $username The username or email-address of the user
	 * @param string $password The password or key the user provides
	 * 
	 * @return LoginResult
	 */
	function login(string $username, string $password) {
		// TODO do database and logic things
	}

	// TODO add register method
}

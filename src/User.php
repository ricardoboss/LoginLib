<?php
/**
 * This file provides the user class
 */
namespace LoginLib\Users;

/**
 * The user class is used to hold data secure and easy to access (for LoginLib). 
 */
class User {
	/** @var int The id of the user */
	protected $id;
	
	/** @var string The username of this user */
	protected $username;
	
	/** @var string The email address of the user */
	protected $email;
	
	/** @var int When the user was last updated (UNIX timestamp) */
	protected $updated_at;
	
	/** @var int The time when the user created their account (UNIX timestamp) */
	protected $registered_at;
	
	/**
	 * The constructor of the User class 
	 * 
	 * @param array $account_row          The corresponding account row for this user from the accounts table
	 * @param array $account_table_config The config array holding the column names for the account table
	 * 
	 * @return User
	 */
	public function __construct($account_row, $account_table_config) {
		$this->id = $account_table_config['col_id'];
		$this->username = $account_table_config['col_username'];
		$this->email = $account_table_config['col_email'];
		$this->updated_at = strtotime($account_table_config['col_updated_at']);
		$this->registered_at = strtotime($account_table_config['col_registered_at']);
	}
	
	/**
	 * This function returns the id of the account associated with this user
	 * 
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Returns the username of this user
	 * 
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}
	
	/**
	 * Returns the email address of this user
	 * 
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}
	
	
	/**
	 * The timestamp of the last time this user has been updated in the database
	 * 
	 * @return int
	 */
	public function getUpdated() {
		return $this->updated_at;
	}
	
	/**
	 * The time when the user created their account
	 * 
	 * @return int
	 */
	public function getRegistered() {
		return $this->registered_at;
	}
	
	
	/**
	 * Returns this user as an array
	 * 
	 * @return array
	 */
	public function getAsArray() {
		return array(
			'id' => $this->id,
			'username' => $this->username,
			'email' => $this->email,
			'updated_at' => $this->updated_at,
			'registered_at' => $this->registered_at
		);
	}
}
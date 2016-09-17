<?php
/**
 * This file defines the IDatabase interface
 */
namespace LoginLib;

/**
 * This is the interface used to communicate with your database
 */
interface IDatabase {
	/**
	 * The constructor of your Database implementation must use an array as configuration
	 *
	 * @param array $config Your config should have the following keys: 'host', 'username', 'password', 'db'
	 *
	 * @return IDatabase
	 */
	function __construct(array $config);
	
	/**
	 * This function checks if a given table exists in your database
	 *
	 * @param string $tableName The name of the table
	 *
	 * @return bool True if the table exists, false otherwise
	 */
	function tableExists($tableName);
	
	/**
	 * Add an " AND " to your where query.
	 * Example:
	 * 	<code>"SELECT * FROM users WHERE col1 = 1"
	 *	=> "SELECT * FROM users WHERE col1 = 1 <u>AND</u> col2 = 2"</code>
	 *
	 * @param string $column The column name
	 * @param string $andValue The needed value
	 */
	function where($column, $andValue);

	/**
	 * Add an <code>" OR "</code> to your where query.
	 * Example:
	 * 	<code>"SELECT * FROM users WHERE col1 = 1"
	 *	=> "SELECT * FROM users WHERE col1 = 1 <u>OR</u> col2 = 2"</code>
	 *
	 * @param string $column The column name
	 * @param string $orValue The other value
	 */
	function orWhere($column, $orValue);
	
	/**
	 * This functions returns the result of selecting one row with the prepared query
	 *
	 * @param string $tableName The name of the table
	 *
	 * @return array|null The selected row
	 */
	function getOne($tableName);
	
	/**
	 * This function inserts data in selected columns (using an associative array) into a table
	 *
	 * @param string $tableName The name of the table
	 * @param array $data The data to insert
	 */
	function insert($tableName, array $data);
	
	/**
	 * This function updates values in selected columns (using an associative array) in a table
	 *
	 * @param string $tableName The name of the table
	 * @param array $data The data to update
	 */
	function update($tableName, array $data);
	
	/**
	 * Used to keep unused databse connections open
	 *
	 * @return bool True if the connection is established
	 */
	function ping();
	
	/**
	 * This function reconnects to your Database, if neccessary
	 *
	 * @return void
	 */
	function connect();
	
	/**
	 * Method returns generated interval function as an insert/update function
	 *
	 * @return array
	 */
	function now();
	
	/**
	 * This method returns the textual representation of the last mysqli error
	 * 
	 * @return string
	 */
	function getLastError();
	
	/**
	 * A function to run raw sql queries
	 *
	 * @param string $q the query
	 *
	 * @return array
	 */
	function rawQuery($q);
}
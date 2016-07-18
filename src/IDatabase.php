<?php
/**
 * This file defines the IDatabase interface
 */

/**
 * This is the interface used to communicate with your database
 */
interface IDatabase {
	function __constructor();
	function tableExists($tableName);
	function where($column, $andValue);
	function orWhere($column, $orValue);
	function getOne($tableName);
	function insert($tableName, $data);
	function update($tableName, $data);
	function ping();
	function connect();
}
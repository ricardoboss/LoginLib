<?php
class DatabaseAdapter implements LoginLib\IDatabase {
	private $mysqlidb;
	
	public function __construct(array $config = null) {
		if ($config == null) {
			throw new Exception("Config cannot be null!");
			exit;
		}
		
		if (!class_exists("MysqliDb")) {
			throw new Exception("Class not found: MysqliDb");
			exit;
		}
		
		$this->mysqlidb = new MysqliDb($config);
		
		return $this->mysqlidb;
	}
	
	public function tableExists($tableName) {
		return $this->mysqlidb->tableExists($tableName);
	}
	
	public function where($column, $andValue) {
		return $this->mysqlidb->where($column, $andValue);
	}
	
	public function orWhere($column, $orValue) {
		return $this->mysqlidb->orWhere($column, $orValue);
	}
	
	public function getOne($tableName) {
		return $this->mysqlidb->getOne($tableName);
	}
	
	public function insert($tableName, array $data) {
		return $this->mysqlidb->insert($tableName, $data);
	}
	
	public function update($tableName, array $data) {
		return $this->mysqlidb->update($tableName, $data);
	}
	
	public function ping() {
		return $this->mysqlidb->ping();
	}
	
	public function connect() {
		return $this->mysqlidb->connect();
	}
	
	public function now() {
		return $this->mysqlidb->now();
	}
	
	public function getLastQuery() {
		return $this->mysqlidb->getLastQuery();
	}
	
	public function rawQuery($q) {
		return $this->mysqlidb->rawQuery($q);
	}
}
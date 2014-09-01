<?php
class Database extends PDO {
	
	public function __construct($info) {
		try {
			parent::__construct('mysql:dbname=' . $info['database'] . ';host=' . $info['hostname'], $info['username'], $info['password'], array());
		} catch (PDOException $e) {
			die($e->getMessage());
		}
	}
	
	public function describe($table) {
		return $this->run("DESCRIBE $table", array())->fetchAll(PDO::FETCH_ASSOC);
	} 
	
	public function run($sql, $params = array()) {
		$run = $this->prepare($sql);
		$run->execute($params);
		return $run;	
	}
	
	public function insert($table, $data) {
		$params = array();
		foreach ($data as $k => $v) 
			$params[':' . $k] = $v;
		$sql = 'INSERT INTO ' . $table . ' (' . implode(', ', array_keys($data)) . ') VALUES (' . implode(', ', array_keys($params)) . ')';
		$this->run($sql, $params);
	}
	
	public function get($table, $params = null, $limit_start = null, $limit_end = null, $order = null) {
		$sql = 'SELECT * FROM ' . $table . ' ';
		if(!is_null($params)) {
			$w = true;
			foreach(array_keys($params) as $param) {
				$sql .= ($w ? "WHERE " : " AND ") . "$param = :$param ";
				if($w) $w = false;
			}
		}
		if(!is_null($order)) $sql .= "ORDER BY $order ";
		if(!is_null($limit_start)) {
			$sql .= " LIMIT $limit_start";
			if(!is_null($limit_end)) $sql .= ", $limit_end";
		}else if(!is_null($limit_end) && is_null($limit_start)) {
			$sql .= " LIMIT $limit_end";
		}
		$data = array();
		foreach($params as $k => $v) 
			$data[':' . $k] = $v;
		if($limit_end == 1) {
			return $this->run($sql, $data)->fetch(PDO::FETCH_ASSOC);
		}else{
			return $this->run($sql, $data)->fetchAll(PDO::FETCH_ASSOC)[0];
		}
		return null;
	}
	
}

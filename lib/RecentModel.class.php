<?php
class RecentModel {
	public $recent = array();
	private $pdo;
	private $categories = array('TV Shows', 'Movies');
	private $tableName = '`atv`.`recent`';
	
	public function __construct($server, $user, $pass) {
		$this->pdo = new PDO("mysql:host={$server}", $user, $pass);
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		//$this->pdo->exec('SET time_zone = "US/Eastern";');
	}
	public function __destruct() {
		// close connection
		$this->pdo = null;
	}
	
	private function prepAndExec($queryBase, $dataRow=array()) {
		$statement = $this->pdo->prepare($queryBase);
		$result = $statement->execute($dataRow);
		return $statement;
	}
	
	private function purge($limit) {
		// http://stackoverflow.com/a/578926
		$queryBase = "DELETE FROM {$this->tableName} WHERE `type`=? AND `id` NOT IN ( SELECT `id` FROM ( SELECT `id` FROM {$this->tableName} ORDER BY `played` DESC LIMIT ? ) `foo`);";
		foreach ($this->categories as $cat) {
			// execute query
			$result = $this->prepAndExec($queryBase, array($cat, $limit));
		}
	}
	
	public function add($type, $value) {
		$queryBase = "INSERT INTO {$this->tableName} (`type`, `path`, `played`) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE `played`=NOW();";
		$result = $this->prepAndExec($queryBase, array($type, $value));
	}
		
	public function get($limit) {
		$output = array();
		$queryBase = "SELECT `path` FROM {$this->tableName} WHERE `type`=? ORDER BY `played` DESC;";
		foreach ($this->categories as $cat) {
			// execute query
			$result = $this->prepAndExec($queryBase, array($cat));
			$output[$cat] = $result->fetchAll();
		}
		return $output;
	}
}
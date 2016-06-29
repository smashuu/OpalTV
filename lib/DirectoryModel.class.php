<?php
class DirectoryModel implements Iterator, Countable, ArrayAccess {
	private $position = 0;
	private $currentVal;
	
	private $dir = array();
	private $extensions = array();
	public $path;
	public $isDir;
	public $pathinfo;
	
	public function __construct($path='', $extensions=array()) {
		if (!empty($path)) {
			$this->path = $this->globSafe($path);
			$this->pathinfo = pathinfo($path);
			$this->isDir = is_dir($path);
			$this->extensions = $extensions;
		}
	}
	private function globSafe($str) {
		return preg_replace('#/$#', '', $str);
	}
	
	public function push($newDir) {
		$this->dir[] = $newDir;
	}
	
	public static function files($path='', $extensions=array()) {
		$dirObj = new DirectoryModel($path, $extensions);
		$dirScanned = glob("{$dirObj->path}/*.{".implode(',', $extensions).'}', GLOB_BRACE);
var_dump($dirScanned); exit;
		foreach ($dirScanned as $entry) {
			$dirObj->dir[] = new DirectoryModel($entry, $extensions);
		}
		return $dirObj;
	}
	public static function folders($path='', $extensions=array()) {
		$dirObj = new DirectoryModel($path, $extensions);
		$dirScanned = glob("{$dirObj->path}/*", GLOB_ONLYDIR);
		foreach ($dirScanned as $entry) {
			$dirObj->dir[] = new DirectoryModel($entry, $extensions);
		}
		return $dirObj;
	}
	public static function folderFiles($path='', $extensions=array()) {
		$dirObj = new DirectoryModel($path, $extensions);
		$dirScanned = glob("{$dirObj->path}/*", GLOB_ONLYDIR);
		foreach ($dirScanned as $entry) {
			$dirObj->dir[] = DirectoryModel::files($entry, $extensions);
		}
		return $dirObj;
	}
	public static function folderFolders($path='', $extensions=array()) {
		$dirObj = new DirectoryModel($path, $extensions);
		$dirScanned = glob("{$dirObj->path}/*", GLOB_ONLYDIR);
		foreach ($dirScanned as $entry) {
			$dirObj->dir[] = DirectoryModel::folders($entry, $extensions);
		}
		return $dirObj;
	}
	
	private function groupByAlpha() {
		$sorter = array();
		foreach ($this->dir as $currentDir) {
			$firstChar = strtoupper(substr($currentDir->pathinfo['filename'], 0, 1));
			if (!isset($sorter[$firstChar])) {
				$sorter[$firstChar] = array();
			}
			$sorter[$firstChar][] = $currentDir;
		}
		$outputDir = new DirectoryModel();
		foreach ($sorter as $letter=>$subDirs) {
			$fakeDir = new DirectoryModel();
			$fakeDir->pathinfo['filename'] = $letter;
			$fakeDir->dir = $subDirs;
			$outputDir->dir[] = $fakeDir;
		}
		return $outputDir;
	}
	
	public static function alphaFolders($path='', $extensions=array()) {
		$dirObj = DirectoryModel::folders($path, $extensions);
		return $dirObj->groupByAlpha();
	}
	public static function alphaFiles($path='', $extensions=array()) {
		$dirObj = DirectoryModel::files($path, $extensions);
		return $dirObj->groupByAlpha();
	}
	
	// Iterator interface methods
	private function setCurrent() {
		$this->currentVal = ($this->position < count($this->dir) ? $this->dir[$this->position] : false);
	}
	public function rewind() {
		$this->position = 0;
		$this->setCurrent();
	}
	public function current() {
		return $this->currentVal;
	}
	public function key() {
		return $this->position;
	}
	public function next() {
		++$this->position;
		$this->setCurrent();
	}
	public function valid()	{
		return $this->currentVal !== false;
	}
	
	// Countable interface methods
	public function count() {
		return count($this->dir) ;
	}
	
	// ArrayAccess interface methods
	public function offsetSet($offset, $value) {
	if (is_null($offset)) {
			$this->dir[] = $value;
		} else {
			$this->dir[$offset] = $value;
		}
	}
	public function offsetExists($offset) {
		return isset($this->dir[$offset]);
	}
	public function offsetUnset($offset) {
		unset($this->dir[$offset]);
	}
	public function offsetGet($offset) {
		return isset($this->dir[$offset]) ? $this->dir[$offset] : null;
	}
}

<?php
class DirectoryModel implements Iterator, Countable, ArrayAccess {
	private $position = 0;
	private $currentVal;
	private $titleRegex = '/^(A |An |The )?(\d\d-)?(.+)(\.mp4|\.m4v)?$/';
	
	private $dir = array();
	public $path;
	public $isDir;
	public $pathinfo;
	public $sortTitle;
	public $displayTitle;
	
	public function __construct($path='') {
		if (!empty($path)) {
			$this->path = $this->globSafe($path);
			$this->pathinfo = pathinfo($path);
			$this->isDir = is_dir($path);
			$this->sortTitle = $this->sortTitle();
			$this->displayTitle = $this->displayTitle();
		}
	}
	public function sortTitle() {
		preg_match($this->titleRegex, $this->pathinfo['filename'], $matches);
		return $matches[2] . $matches[3];
	}
	public function displayTitle() {
		preg_match($this->titleRegex, $this->pathinfo['filename'], $matches);
		return $matches[1] . $matches[3];
	}
	private function globSafe($str) {
		return preg_replace('/\/$/', '', $str);
	}
	
	public function pushPath($path) {
		$newDir = new DirectoryModel($path);
		$this->push($newDir);
	}
	public function push($newDir) {
		$this->dir[] = $newDir;
	}
	public function fill($dirArray) {
		$this->dir = $dirArray;
	}
	
	private function populate($glob) {
		foreach ($glob as $entry) {
			$this->pushPath($entry);
		}
		usort($this->dir, function ($a, $b) {
			return strnatcmp($a->sortTitle, $b->sortTitle);
		});
	}
	private function populateFiles() {
		$this->populate(glob("{$this->path}/*.{mp4,m4v}", GLOB_BRACE));
	}
	private function populateFolders() {
		$this->populate(glob("{$this->path}/*", GLOB_ONLYDIR));
		
	}
	private function populateChildFiles() {
		foreach ($this as $child) {
			$child->populateFiles();
		}
	}
	private function populateChildFolders() {
		foreach ($this as $child) {
			$child->populateFiles();
		}
	}
	
	
	public static function files($path) {
		$dirObj = new DirectoryModel($path);
		$dirObj->populateFiles();
		return $dirObj;
	}
	public static function folders($path) {
		$dirObj = new DirectoryModel($path);
		$dirObj->populateFolders();
		return $dirObj;
	}
	public static function folderFiles($path) {
		$dirObj = self::folders($path);
		$dirObj->populateChildFiles();
		return $dirObj;
	}
	public static function folderFolders($path) {
		$dirObj = self::folders($path);
		$dirObj->populateChildFolders();
		return $dirObj;
	}
	
	private function groupByAlpha() {
		$sorter = array();
		foreach ($this as $currentDir) {
			$firstChar = strtoupper(substr($currentDir->sortTitle, 0, 1));
			if (!isset($sorter[$firstChar])) {
				$sorter[$firstChar] = array();
			}
			$sorter[$firstChar][] = $currentDir;
		}
		$outputDir = new DirectoryModel();
		foreach ($sorter as $letter=>$subDirs) {
			$fakeDir = new DirectoryModel();
			$fakeDir->displayTitle = $letter;
			$fakeDir->fill($subDirs);
			$outputDir->push($fakeDir);
		}
		return $outputDir;
	}
	
	public static function alphaFolders($path='') {
		$dirObj = DirectoryModel::folders($path);
		return $dirObj->groupByAlpha();
	}
	public static function alphaFiles($path='') {
		$dirObj = DirectoryModel::files($path);
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

<?php
use FlipWays\MVCJr\Controller;

require_once 'MVCJr/Controller.class.php';
require_once 'DirectoryView.class.php';
require_once 'DirectoryModel.class.php';

class TvEpisodesController extends Controller {
	public function __construct($application) {
		parent::__construct($application);
		$this->models = array(
			'directory' => DirectoryModel::folderFiles($this->application->mediaDir.$this->application->url, $this->application->vidExtensions)
		);
		$this->view = new DirectoryView($this);
	}
}

<?php
use FlipWays\MVCJr\Controller;

require_once 'MVCJr/Controller.class.php';
require_once 'DirectoryView.class.php';
require_once 'DirectoryModel.class.php';

class TvShowsController extends Controller {
	public function __construct($application) {
		parent::__construct($application);
		$this->models = array(
			'directory' => DirectoryModel::alphaFolders($this->application->mediaDir.'TV Shows')
		);
		$this->view = new DirectoryView($this);
	}
}

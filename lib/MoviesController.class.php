<?php
use FlipWays\MVCJr\Controller;

require_once 'MVCJr/Controller.class.php';
require_once 'DirectoryView.class.php';
require_once 'DirectoryModel.class.php';

class MoviesController extends Controller {
	public function __construct($application) {
		parent::__construct($application);
		$this->models = array(
			'directory' => DirectoryModel::alphaFiles($this->application->mediaDir.'Movies', $this->application->vidExtensions)
		);
		$this->view = new DirectoryView($this);
	}
}

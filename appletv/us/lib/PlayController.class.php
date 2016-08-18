<?php
use FlipWays\MVCJr\Controller;

require_once 'MVCJr/Controller.class.php';
require_once 'PlayView.class.php';
require_once 'RecentModel.class.php';

class PlayController extends Controller {
	public function __construct($application) {
		parent::__construct($application);
		$this->view = new PlayView($this);
		$recentDb = new RecentModel();
		$recentDb->add($this->application->route[0], $this->application->route[0].'/'.$this->application->route[1]);
	}
}

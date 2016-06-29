<?php
use FlipWays\MVCJr\Controller;

require_once 'MVCJr/Controller.class.php';
require_once 'DirectoryView.class.php';
require_once 'DirectoryModel.class.php';
require_once 'RecentModel.class.php';

class RecentController extends Controller {
	public function __construct($application) {
		parent::__construct($application);
		
		$recentDb = new RecentModel('localhost', 'root', 'Mighty Quinn');
		$recentPlays = $recentDb->get(5);
		$recents = new DirectoryModel();
		
		$recentTV = new DirectoryModel();
		$recentTV->pathinfo['filename'] = 'Recent TV Shows';
		foreach ($recentPlays['TV Shows'] as $show) {
			$recentTV->push(new DirectoryModel($show['path']));
		}
		$recents->push($recentTV);
		
		$recentMovies = new DirectoryModel();
		$recentMovies->pathinfo['filename'] = 'Recent Movies';
		foreach ($recentPlays['Movies'] as $movie) {
			$recentMovies->push(new DirectoryModel($movie['path']));
		}
		$recents->push($recentMovies);
		
		$this->models = array(
			'directory' => $recents
		);
		$this->view = new DirectoryView($this);
	}
}

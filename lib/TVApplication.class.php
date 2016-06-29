<?php
use FlipWays\MVCJr\Application;

require_once 'MVCJr/Application.class.php';

class TVApplication extends Application {
	public $mediaDir = '/mnt/MEDIA/video/';
	public $mediaUrl= 'http://vidboy.local/video/';
	public $imagesUrl = 'http://trailers.apple.com/appletv/us/images/';
	public $thumbsUrl = 'http://vidboy.local/media/thumbs/';
	public $vidExtensions = array('mp4','m4v');
	
	public function __construct() {
		parent::__construct();
		
		$this->rootDir = $this->scheme . $_SERVER['SERVER_NAME'] . '/appletv/us/';
		
		/*
		$this->url = 'TV Shows/King of the Hill';
		$this->route[0] = 'TV Shows';
		$this->route[1] = 'King of the Hill';
		*/
		
		if (preg_match('/.*\\.('.implode('|', $this->vidExtensions).')$/', $this->url)) {
			require_once 'PlayController.class.php';
			$controller = new PlayController($this);
		} elseif ($this->route[0] === 'TV Shows') {
			if (!empty($this->route[1])) {
				require_once 'TvEpisodesController.class.php';
				$controller = new TvEpisodesController($this);
			} else {
				require_once 'TvShowsController.class.php';
				$controller = new TvShowsController($this);
			}
		} elseif ($this->route[0] === 'Movies') {
			require_once 'MoviesController.class.php';
			$controller = new MoviesController($this);
		} else {
			require_once 'RecentController.class.php';
			$controller = new RecentController($this);
		}
		
		//header('Content-type: application/xml');
		header('Content-type: text/plain');
		$controller->execute();
	}
}

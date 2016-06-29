<?php
use FlipWays\MVCJr\View;
use FlipWays\DOMplate;

require_once 'MVCJr/View.class.php';
require_once 'DOMplate.class.php';

class PlayView extends View {
	public function render() {
		$mainDom = new DOMplate('templates/play.xml', true);
		$pathParts = array();
		foreach ($this->controller->application->route as $part) {
			$pathParts[] = rawurlencode($part);
		}
		$mediaPath = $this->controller->application->mediaUrl . implode('/', $pathParts);
		$mainDom->setText("//mediaURL", $mediaPath);
		$mainDom->setText("//description", $mediaPath);
		return $mainDom->saveXML();
	}
}


<?php
use FlipWays\MVCJr\View;
use FlipWays\DOMplate;

require_once 'MVCJr/View.class.php';
require_once 'DOMplate.class.php';

class DirectoryView extends View {
	public function render() {
		$mainDom = $this->prepare($this->models['directory']);
		return $mainDom->saveXML();
	}
	
	public function prepare($data) {
		$mainDom = new DOMplate('templates/scroller.xml', true);
		
		$mainDom->repeat("//shelfWrapper", count($data));
		
		$shelfItemCounter = 0;
		foreach ($data as $sectionIdx=>$section) {
			$sectionIdx++;
			$mainDom->setText("//shelfWrapper[{$sectionIdx}]/collectionDivider/title", $section->pathinfo['filename']);
			$mainDom->formatAttr("//shelfWrapper[{$sectionIdx}]/shelf", 'id', array($sectionIdx));
			
			$mainDom->repeat("//shelfWrapper[{$sectionIdx}]//moviePoster", count($section));
			foreach ($section as $posterIdx=>$poster) {
				$posterIdx++;
				$shelfItemCounter++;
				$mediaPath = str_replace('%2F', '/', rawurlencode(preg_replace('/^' . addcslashes($this->controller->application->mediaDir,'/') . '/', '', $poster->path)));
				$mediaUrl = $this->controller->application->rootDir . $mediaPath;
				
				$mainDom->setText("//shelfWrapper[{$sectionIdx}]//moviePoster[{$posterIdx}]/title", $poster->pathinfo['filename']);
				$mainDom->formatAttr("//shelfWrapper[{$sectionIdx}]//moviePoster[{$posterIdx}]", 'id', array($shelfItemCounter));
				$mainDom->setText("//shelfWrapper[{$sectionIdx}]//moviePoster[{$posterIdx}]/image", $this->controller->application->thumbsUrl . $mediaPath . '.jpg');
				$mainDom->setAttr("//shelfWrapper[{$sectionIdx}]//moviePoster[{$posterIdx}]", 'onSelect', "atv.loadURL('{$mediaUrl}');");
				$mainDom->setAttr("//shelfWrapper[{$sectionIdx}]//moviePoster[{$posterIdx}]", 'onPlay', "atv.loadURL('{$mediaUrl}');");
			}
		}
		
		$mainItems = $mainDom->fetchElement("//items[@id='pageItems']");
		$wrapperItems = $mainDom->fetch("//shelfWrapper/*");
		foreach ($wrapperItems as $idx=>$item) {
			$mainItems->appendChild($item);
		}
		$mainDom->remove("//shelfWrapper");
		
		return $mainDom;
	}
}

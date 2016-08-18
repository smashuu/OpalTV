<?php
if (!isset($argv[1]) || !is_dir($argv[1])) {
	die("Invalid directory\n");
}

function sanitize($str) {
	//$output = escapeshellcmd($str);
	$output = str_replace(array("(", ")"), array("\\(", "\\)"), $str);
	return $output;
}

function createThumb($vid, $time) {
	$frameCmd = 'ffmpeg -ss %2$s -i "%1$s" -vframes 1 "../thumbs/%1$s.jpg"';
	$thumbEpisodeCmd = 'convert -compose Dst_Over -size 261x75 -background rgba\(0,0,0,0\) gradient:rgba\(0,0,0,0\)-black -gravity south -extent 261x385 \( "../thumbs/%1$s.jpg" -resize "261x385^" -gravity center -crop 261x385+0+0 +repage \)  -composite -font "~/Arial_Black.ttf" -fill white -pointsize 36 -gravity south -annotate +0+8 "%2$s" "../thumbs/%1$s.jpg"';
	$thumbNoEpisodeCmd = 'convert -size 261x385 \( "../thumbs/%1$s.jpg" -resize "261x385^" -gravity center -crop 261x385+0+0 +repage \) "../thumbs/%1$s.jpg"';
	
	$pathinfo = pathinfo($vid);
	if (preg_match('/^([0-9][0-9])-/', $pathinfo['filename'], $matches)) {
		$episode = sprintf('Episode %d', $matches[1]);
		$thumbCmd = $thumbEpisodeCmd;
	} else {
		$episode = '';
		$thumbCmd = $thumbNoEpisodeCmd;
	}
	
	print("\n  Creating ../thumbs/{$vid}.jpg from {$time}...");
//	print("\n");
	exec(sprintf($frameCmd, sanitize($vid), $time) . (isset($argv[3]) ? '' : ' 2>/dev/null'));
//	print("\n");
	exec(sprintf($thumbCmd, sanitize($vid), sanitize($episode)) . (isset($argv[3]) ? '' : ' 2>/dev/null'));
	print(" finished.");
//	print("\n");
}

$timecode = isset($argv[2]) ? $argv[2] : '00:05:0.000';

$vidDir = preg_replace('|^(.+)/$|', '$1', $argv[1]);
$thumbDir = '../thumbs/' . $vidDir;
if (!is_dir($thumbDir)) {
	mkdir($thumbDir, 0777, true);
	print("Created: $thumbDir");
} else {
	print("Exists: $thumbDir");
}

if (isset($argv[3])) {
	createThumb($argv[3], $timecode);
} else {
	$videos = glob("{$vidDir}/*.{mp4,m4v}", GLOB_BRACE);
	foreach ($videos as $vid) {
		createThumb($vid, $timecode);
	}
}
print("\nAll done!\n");

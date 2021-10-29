<?php
require '_up.php';

define('ACTION_VIEW', 0);
define('ACTION_DOWNLOAD', 1);
define('ACTION_REMOVE', 2);

function showFile($path, $fname, $mimeType, $disp) {
	header('Content-Disposition: ' . $disp . '; filename=' . $fname);
	header('Content-Type: ' . $mimeType);
	readfile($path);
}

function remove($uid, $fname) {
	$f = up_getFileByName($uid, $fname);
	if(isset($f)) {
		up_removeFile($uid, $f['fid']);
	}
	header('Location: uploader.php');
}

function showErrorPage() {

/* ----- START OF ERROR PAGE ----- */

?>
<!doctype html>
<html>
<head>
<title>miniupload</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="favicon.ico" />
<link rel="stylesheet" type="text/css" href="style.css" />
</head>

<body>
<div class="box">
<p class="err">Invalid parameters!</p>
</div>
</body>
</html>

<?php

/* ----- END OF ERROR PAGE ----- */

}

/* ----- EXECUTION BEGINS ----- */

if(!ss_check()) {
	ss_redir('index.php');
}

if(!isset($_GET['name'])) {
	showErrorPage();
} else {
	$uid = ss_get('uid');
	$fname = $_GET['name'];
	$f = up_getFileByName($uid, $fname);

	if(!isset($f)) {
		showErrorPage();
	} else {
		$userRoot = 'files' . DIRECTORY_SEPARATOR . $uid;
		$path = $userRoot . DIRECTORY_SEPARATOR . $f['fid'];
		$mimeType = $f['mimetype'];

		if(!isset($_GET['action'])) {
			$action = ACTION_DOWNLOAD;
		} else if($_GET['action'] == 'view') {
			$type = explode('/', $mimeType)[0];
			$a = explode('.', $fname);
			$ext = array_pop($a);
			if($ext != 'php' && ($type == 'text' || $type == 'image')) {
				$action = ACTION_VIEW;
			} else {
				$action = ACTION_DOWNLOAD;
			}
		} else if($_GET['action'] == 'remove') {
			$action = ACTION_REMOVE;
		} else {
			$action = ACTION_DOWNLOAD;
		}

		switch($action) {
		       case ACTION_VIEW: showFile($path, $fname, $mimeType, 'inline');
		break; case ACTION_DOWNLOAD: showFile($path, $fname, $mimeType, 'attachment');
		break; case ACTION_REMOVE: remove($uid, $fname); break;
		}
	}
}
?>

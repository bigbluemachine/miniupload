<?php
/*
	File system functions.
	Recursive file size and remove.
*/

function fs_getSize($fname) {
	if(is_dir($fname)) {
		$ans = filesize($fname);
		$files = scandir($fname);
		foreach($files as $f) {
			if($f == '.' || $f == '..') {
				continue;
			}
			$ans += fs_getSize($fname . DIRECTORY_SEPARATOR . $f);
		}
		return $ans;
	} else {
		return filesize($fname);
	}
}

function fs_rm($fname) {
	if(is_dir($fname)) {
		$files = scandir($fname);
		foreach($files as $f) {
			if($f == '.' || $f == '..') {
				continue;
			}
			fs_rm($fname . DIRECTORY_SEPARATOR . $f);
		}
		return rmdir($fname);
	} else {
		return unlink($fname);
	}
}
?>

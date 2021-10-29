<?php
require '_up.php';

$GLOBALS['sort'] = 0;
$GLOBALS['desc'] = false;

function compare($a, $b) {
	$p = $a[$GLOBALS['sort']];
	$q = $b[$GLOBALS['sort']];
	return $p < $q ? -1 : $p == $q ? 0 : 1;
}

function compare_desc($a, $b) {
	$p = $b[$GLOBALS['sort']];
	$q = $a[$GLOBALS['sort']];
	return $p < $q ? -1 : $p == $q ? 0 : 1;
}

function th($inner, $align) {
	echo '<th align="' . $align . '">' . $inner . '</th>';
}

function td($inner, $align) {
	echo '<td align="' . $align . '">' . $inner . '</td>';
}

function viewLink($fname) {
	return '<a href="file.php?action=view&name=' . $fname . '" target="_blank">' . $fname . '</a>';
}

function removeLink($fname) {
	return '<a href="file.php?action=remove&name=' . $fname . '">x</a>';
}

function sortLink($index, $name) {
	if($index == $GLOBALS['sort'] && !$GLOBALS['desc']) {
		$a = '<a href="uploader.php?sort=' . $index . '&desc">' . $name . '</a>';
	} else {
		$a = '<a href="uploader.php?sort=' . $index . '">' . $name . '</a>';
	}

	if($index == $GLOBALS['sort']) {
		return $a . ($GLOBALS['desc'] ? ' &#x25BC;' : ' &#x25B2;');
	} else {
		return $a;
	}
}

function showFiles() {
	$userRoot = 'files' . DIRECTORY_SEPARATOR . ss_get('uid');
	$files = scandir($userRoot);

	if(count($files) <= 2) {
		echo '<p class="grey6"><i>You have not yet uploaded any files.</i></p>' . "\n";
		return;
	}

	$totalSize = 0;
	foreach($files as $fid) {
		if($fid == '.' || $fid == '..') {
			continue;
		}
		$f = up_getFileByFID($fid);
		$fname = $f['fname'];
		$mimeType = $f['mimetype'];
		$size = (int) $f['size'];
		$time = (int) $f['time'];
		$info[] = [$fname, $mimeType, $size, $time];
		$totalSize += $size;
	}
	$percentage = round(($totalSize / UPLOAD_LIMIT) * 100, 2);

	if(!isset($_GET['sort'])) {
		$GLOBALS['sort'] = 0;
	} else {
		$GLOBALS['sort'] = (int) $_GET['sort'];
		if($GLOBALS['sort'] < 0 || $GLOBALS['sort'] > 3) {
			$GLOBALS['sort'] = 0;
		}
	}

	$GLOBALS['desc'] = isset($_GET['desc']);
	usort($info, $GLOBALS['desc'] ? 'compare_desc' : 'compare');

	echo '<p>You are using ' . $totalSize . ' of ' . UPLOAD_LIMIT . ' bytes, or ' . $percentage . '%.</p>' . "\n";
	echo '<table cellspacing="0">' . "\n";
	echo '<tr>';
	th(sortLink(0, 'Name'), 'left');
	th(sortLink(1, 'MIME Type'), 'left');
	th(sortLink(2, 'Size'), 'left');
	th(sortLink(3, 'Date Uploaded'), 'left');
	th('Remove', 'center');
	echo '</tr>' . "\n";

	foreach($info as $i) {
		echo '<tr>';
		td(viewLink($i[0]), 'left');
		td($i[1], 'left');
		td($i[2], 'right');
		td(date('Y/m/d H:i:s', $i[3]), 'left');
		td(removeLink($i[0]), 'center');
		echo '</tr>' . "\n";
	}

	echo '</table>';
}

/* ----- EXECUTION BEGINS ----- */

if(!ss_check()) {
	ss_redir('index.php');
}

$uid = ss_get('uid');
up_sanityCheck($uid);
?>

<!doctype html>
<html>
<head>
<title>miniupload</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="favicon.ico" />
<link rel="stylesheet" type="text/css" href="style.css" />

<style type="text/css">
table {
	margin: 10px 5px 10px 5px;
	border-right: 1px solid #666666;
	border-bottom: 1px solid #666666;
}

th, td {
	padding: 10px;
	border-top: 1px solid #666666;
	border-left: 1px solid #666666;
}

th {
	background-color: #ffffcc;
}
</style>
</head>

<body>
<div class="box">
<h2>
	<a href="index.php">Home</a>
	|
	Your files
	|
	<a href="options.php">Options</a>
	|
	<a href="logout.php">Log out</a>
</h2>

<p class="grey6"><i>Logged in as <?php echo ss_get('email'); ?></i></p>

<hr />

<p>Choose one or more files to upload:</p>

<form method="POST" action="upload.php" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
<p><input name="f[]" type="file" multiple size="50" /></p>
<p>
	<input type="submit" value="Upload" />
	<input id="overwrite" name="overwrite" type="checkbox" /> <label for="overwrite">Overwrite if existing</label>
</p>
</form>

<hr />

<?php
showFiles();
?>

</div>

<center><p class="grey6"><?php echo up_footer(); ?></p></center>
</body>
</html>

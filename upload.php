<?php
require '_up.php';

define('FILES_NOT_SET', 255);

function formatErr($e, $out) {
	global $uploadErr;
	if($e == FILES_NOT_SET) {
		$err = error_get_last();
		return 'PHP error: ' . $err['message'];
	} else if($e == UPLOAD_ERR_NO_UPLOAD || $e == UPLOAD_ERR_SOME_UPLOAD) {
		$str = '';
		foreach($out as $fname => $ferr) {
			$str .= $fname . ': ' . $uploadErr[$ferr] . '<br />';
		}
		return $str;
	} else {
		return $uploadErr[$e];
	}
}

/* ----- EXECUTION BEGINS ----- */

if(!ss_check()) {
	ss_redir('index.php');
}

$uid = ss_get('uid');
$out = array();

if(!isset($_FILES['f'])) {
	$status = FILES_NOT_SET;
} else {
	$info = $_FILES['f'];
	$status = up_uploadFiles($uid, $info, isset($_POST['overwrite']), $out);
}
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
<?php
if($status == UPLOAD_ERR_OK) {
	echo '<h2>Upload successful</h2>';
	echo '<hr />';
	echo '<p>Your files have been uploaded.</p>';
	echo '<p><a href="uploader.php">Go back</a>.</p>';
} else {
	echo '<h2>Upload failed</h2>';
	echo '<hr />';
	echo '<p class="err">' . formatErr($status, $out) . '</p>';
	echo '<p><a href="uploader.php">Try again</a>.</p>';
}
?>
</div>
</body>
</html>

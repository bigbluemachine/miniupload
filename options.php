<?php
require '_up.php';

define('OPT_OK', 0);
define('OPT_BAD_PASSWORD', 1);
define('OPT_PASS_CHANGED', 2);

function processOpt($password, $confirm) {
	if(strlen($password) < 6 || $password != $confirm) {
		return OPT_BAD_PASSWORD;
	} else {
		return OPT_OK;
	}
}

/* ----- EXECUTION BEGINS ----- */

if(!ss_check()) {
	ss_redir('index.php');
}

$GLOBALS['status'] = OPT_OK;

if(isset($_POST['submit'])) {
	$GLOBALS['status'] = processOpt($_POST['password'], $_POST['confirm']);
	if($GLOBALS['status'] == OPT_OK) {
		$GLOBALS['status'] = OPT_PASS_CHANGED;
		up_setPassword(ss_get('uid'), $_POST['password']);
	}
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
<h2>
	<a href="index.php">Home</a>
	|
	<a href="uploader.php">Your files</a>
	|
	Options
	|
	<a href="logout.php">Log out</a>
</h2>

<p class="grey6"><i>Logged in as <?php echo ss_get('email'); ?></i></p>

<hr />

<?php
if($GLOBALS['status'] == OPT_PASS_CHANGED) {
	echo '<p class="err">Your password has been changed.</p>';
}
?>

<p>You may change your password here.</p>

<p>A password must be 6 to 20 characters long.</p>

<p>Enter the password once more to confirm.</p>

<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<table cellpadding="5">
		<tr>
			<td><label for="password">New password:</label></td>
			<td><input id="password" name="password" type="password" size="30" maxlength="20" /></td>
			<td>
<?php
if($GLOBALS['status'] == OPT_BAD_PASSWORD) {
	echo '<span class="err">Could not change password; try again.</span>';
}
?>
			</td>
		</tr>
		<tr>
			<td><label for="password">Repeat password:</label></td>
			<td><input id="password" name="confirm" type="password" size="30" maxlength="20" /></td>
			<td></td>
		</tr>
	</table>
	<p><input id="submit" name="submit" type="submit" value="Change password" /></p>
</form>
</div>

<center><p class="grey6"><?php echo up_footer(); ?></p></center>
</body>
</html>

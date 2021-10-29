<?php
require '_up.php';

define('REG_OK', 0);
define('REG_INVALID_EMAIL', 1);
define('REG_ACCOUNT_EXISTS', 2);

function processReg($email) {
	if(!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return REG_INVALID_EMAIL;
	} else if(up_getUID($email)) {
		return REG_ACCOUNT_EXISTS;
	} else {
		return REG_OK;
	}
}

function showPage0() {

/* ----- START OF PAGE 0 ----- */

?>
<!doctype html>
<html>
<head>
<title>miniupload</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="favicon.ico" />
<link rel="stylesheet" type="text/css" href="style.css" />

<script type="text/javascript">
function _(id) {
	return document.getElementById(id);
}

window.onload = function() {
	_('email').focus();
	_('email').select();
};
</script>
</head>

<body>
<div class="box">
<h2>
	<a href="index.php">Home</a>
	|
	<a href="login.php">Log in</a>
	|
	Register
</h2>

<hr />

<p>To register, enter your e-mail address.</p>
<p>You will receive your password in an e-mail message.</p>
<p>You may change your password once you log in.</p>

<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<table cellpadding="5">
		<tr>
			<td><label for="email">E-mail address:</label></td>
			<td><input id="email" name="email" type="text" size="30" value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>" /></td>
			<td>
<?php
if($GLOBALS['status'] == REG_INVALID_EMAIL) {
	echo '<span class="err">Enter a valid e-mail address.</span>';
} else if($GLOBALS['status'] == REG_ACCOUNT_EXISTS) {
	echo '<span class="err">An account already exists with this e-mail address.</span>';
}
?>
			</td>
		</tr>
	</table>
	<p><input id="submit" name="submit" type="submit" value="Register" /></p>
</form>
</div>

<center><p class="grey6"><?php echo up_footer(); ?></p></center>
</body>
</html>
<?php

/* ----- END OF PAGE 0 ----- */

}

function showPage1() {

/* ----- START OF PAGE 1 ----- */

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
	Register
</h2>

<hr />

<p>Your password has been sent to your e-mail address.</p>
<p>You may change your password once you log in.</p>
</div>

<center><p class="grey6"><?php echo up_footer(); ?></p></center>
</body>
</html>
<?php

/* ----- END OF PAGE 1 ----- */

}

/* ----- EXECUTION BEGINS ----- */

if(ss_check()) {
	ss_redir('uploader.php');
}

$GLOBALS['status'] = REG_OK;

if(isset($_POST['submit'])) {
	$GLOBALS['status'] = processReg($_POST['email']);
	if($GLOBALS['status'] == REG_OK) {
		up_register($_POST['email']);
		showPage1();
		exit;
	}
}

showPage0();
?>

<?php
require '_up.php';

function showDonePage() {

/* ----- START OF DONE PAGE ----- */

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
	Remove account
</h2>

<hr />

<p>Your account and all associated files were removed.</p>

<p><a href="index.php">Return to the main page</a>.</p>
</div>

<center><p class="grey6"><?php echo up_footer(); ?></p></center>
</body>
</html>
<?php

/* ----- END OF DONE PAGE ----- */

}

/* ----- EXECUTION BEGINS ----- */

if(isset($_POST['submit'])) {
	if($_POST['uid'] = up_auth($_POST['email'], $_POST['password'])) {
		ss_begin($_POST['uid']);
		up_removeAccount($_POST['uid']);
		showDonePage();
		ss_end();
		exit;
	}
	$GLOBALS['bad_login'] = true;
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
	Remove account
</h2>

<hr />

<p class="err"><b>This action will remove your account and all associated files!</b></p>

<p>Enter your information to continue.</p>

<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<table cellpadding="5">
		<tr>
			<td><label for="email">E-mail address:</label></td>
			<td><input id="email" name="email" type="text" size="30" value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>" /></td>
			<td><?php if(isset($GLOBALS['bad_login'])) echo ' <span class="err">Could not log in; try again.</span>'; ?></td>
		</tr>
		<tr>
			<td><label for="password">Password:</label></td>
			<td><input id="password" name="password" type="password" size="30" /></td>
			<td></td>
		</tr>
	</table>
	<p><input id="submit" name="submit" type="submit" value="Remove" /></p>
</form>
</div>

<center><p class="grey6"><?php echo up_footer(); ?></p></center>
</body>
</html>

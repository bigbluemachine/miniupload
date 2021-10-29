<?php
require '_up.php';

/* ----- EXECUTION BEGINS ----- */
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
if(ss_check()) {
?>

<h2>
	Home
	|
	<a href="uploader.php">Your files</a>
	|
	<a href="options.php">Options</a>
	|
	<a href="logout.php">Log out</a>
</h2>

<p class="grey6"><i>Logged in as <?php echo ss_get('email'); ?></i></p>

<?php
} else {
?>

<h2>
	Home
	|
	<a href="login.php">Log in</a>
	|
	<a href="reg.php">Register</a>
</h2>

<?php
}
?>

<hr />

<p>Welcome to <b>miniupload</b>! Register an account and start storing files.</p>
<p>Each account can hold up to 10 megabytes of space, or 10000000 bytes.</p>
<p>Any type of file can be uploaded.</p>

<hr />

<h3>Frequently Asked Questions</h3>

<p>
<b>Q:</b> Who can view my files?
<br />
<b>A:</b> Only you can view your own files when logged in.
</p>

<p>
<b>Q:</b> What if I forget or don't have my password?
<br />
<b>A:</b> You can reset your password <a href="reset.php">here</a>.
</p>

<p>
<b>Q:</b> How do I remove my account?
<br />
<b>A:</b> You can remove your account <a href="unreg.php">here</a>. This removes all of your files.
</p>

<p>
<b>Q:</b> Why can I only preview some of my files in my browser?
<br />
<b>A:</b> Only text and image files can be previewed, since they are well-supported on all browsers. However, you can still download any file.
</p>

<p>
<b>Q:</b> Wait, isn't 10 megabytes very little space?
<br />
<b>A:</b> Please visit <a href="https://www.google.com/drive/">Google Drive</a>.
</p>
</div>

<center><p class="grey6"><?php echo up_footer(); ?></p></center>
</body>
</html>
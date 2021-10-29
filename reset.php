<?php
require '_up.php';

function processReset($email) {
  if(!isset($email) || !($uid = up_getUID($email))) {
    $GLOBALS['bad_email'] = true;
    return false;
  }
  return $uid;
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
  Reset password
</h2>

<hr />

<p>To reset your password, enter the e-mail address of your existing account.</p>
<p>You will receive your new password in an e-mail message.</p>
<p>You may change your password once you log in.</p>

<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
  <table cellpadding="5">
    <tr>
      <td><label for="email">E-mail address:</label></td>
      <td><input id="email" name="email" type="text" size="30" value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>" /></td>
      <td><?php if(isset($GLOBALS['bad_email'])) echo ' <span class="err">No account exists for this e-mail address.</span>'; ?></td>
    </tr>
  </table>
  <p><input id="submit" name="submit" type="submit" value="Reset password" /></p>
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
  Reset password
</h2>

<hr />

<p>Your new password has been sent to your e-mail address.</p>
<p>You may change your password once you log in.</p>
</div>

<center><p class="grey6"><?php echo up_footer(); ?></p></center>
</body>
</html>
<?php

/* ----- END OF PAGE 1 ----- */

}

/* ----- EXECUTION BEGINS ----- */

if(isset($_POST['submit'])) {
  if($uid = processReset($_POST['email'])) {
    up_resetPassword($uid, $_POST['email']);
    showPage1();
    exit;
  }
}

showPage0();
?>
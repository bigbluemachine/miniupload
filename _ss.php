<?php
/*
	Session handling functions.

	ss_redir($loc)		redirects, usually after checking login status
	ss_begin($uid)		starts session with user ID
	ss_check()			checks login status
	ss_end()			ends session
	ss_get($var)		gets session variable
	ss_set($var, $val)	sets session variable
*/

function ss_redir($loc) {
	header('Location: ' . $loc);
}

function ss_begin($uid) {
	session_start();
	$_SESSION['logged_in'] = true;
	$_SESSION['last_act'] = time();
	$_SESSION['uid'] = $uid;
}

function ss_check() {
	if(!isset($_SESSION)) {
		session_start();
	}
	if(!isset($_SESSION['logged_in'])) {
		session_unset();
		session_destroy();
		return $GLOBALS['login'] = false;
	} else {
		$_SESSION['last_act'] = time();
		return $GLOBALS['login'] = true;
	}
}

function ss_end() {
	if(!isset($_SESSION)) {
		session_start();
	}
	session_unset();
	session_destroy();
}

function ss_get($var) {
	return isset($_SESSION[$var]) ? $_SESSION[$var] : false;
}

function ss_set($var, $val) {
	$_SESSION[$var] = $val;
}
?>

<?php
/*
	Database handling functions.

	The multi-purpose query function returns:
	- null on failed query; follow with mysqli_error()
	- true or false for queries other than SELECT
	- single value if query returns single value, such as COUNT
	- result set (or false) otherwise
*/

$_DB = array();

function db_set($server, $username, $password, $database) {
	global $_DB;
	$_DB['server'] = $server;
	$_DB['username'] = $username;
	$_DB['password'] = $password;
	$_DB['database'] = $database;
}

function db_quote($str) {
	return "'" . $str . "'";
}

function db_fetch($res, $assoc) {
	return $assoc ? mysqli_fetch_assoc($res) : mysqli_fetch_row($res);
}

function db_query($query, $assoc = true) {
	global $_DB;
	$res = mysqli_query($_DB['link'], $query);

	if(mysqli_errno($_DB['link'])) {
		return null;
	}

	if(strtolower(substr($query, 0, 6)) != 'select') {
		return $res;
	}

	$ans = array();

	while($row = db_fetch($res, $assoc)) {
		$ans[] = $row;
	}

	if(count($ans) == 1 && count($ans[0]) == 1) {
		list($key) = array_keys($ans[0]);
		return $ans[0][$key];
	}

	return $ans;
}

function db_insertId() {
	global $_DB;
	return mysqli_insert_id($_DB['link']);
}

function db_escapeGet() {
	global $_DB;
	foreach($_GET as $key => $value) {
		$_GET[$key] = mysqli_real_escape_string($_DB['link'], $value);
	}
}

function db_escapePost() {
	global $_DB;
	foreach($_POST as $key => $value) {
		$_POST[$key] = mysqli_real_escape_string($_DB['link'], $value);
	}
}

function db_connect() {
	global $_DB;
	$_DB['link'] = mysqli_connect($_DB['server'], $_DB['username'], $_DB['password']);
	return $_DB['link'] && mysqli_select_db($_DB['link'], $_DB['database']);
}
?>
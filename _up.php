<?php
// Debug purposes.
error_reporting(E_ALL);
ini_set('display_errors', 'on');

/*
  Definitions specific to uploader.

  - 'pw' prefix denotes password handling functions
  - Originally in a separate file

  pw_genSalt()          generates 8-digit salt
  pw_hash($pwd, $salt)      calculates hash using SHA-256
  pw_test($pwd, $salt, $hash)    checks attempt against hash
*/

require '_db.php';
require '_fs.php';
require '_ss.php';

/*
  Database settings.
*/

db_set(
  'localhost', // server
  'uploader', // username
  'YOUR_PASSWORD_HERE', // password
  'Uploader' // database
);

/*
  Automatically connect, escape POST and GET.
*/

db_connect();
db_escapeGet();
db_escapePost();

/*
  File upload definitions.
*/

define('UPLOAD_LIMIT', 10000000);
define('UPLOAD_ERR_EXISTS', 9);
define('UPLOAD_ERR_OVER_LIMIT', 10);
define('UPLOAD_ERR_NOT_POST', 11);
define('UPLOAD_ERR_CANT_MOVE', 12);
define('UPLOAD_ERR_NO_UPLOAD', 127);
define('UPLOAD_ERR_SOME_UPLOAD', 255);

$uploadErr = array(
  UPLOAD_ERR_OK => 'File uploaded successfully.',
  UPLOAD_ERR_INI_SIZE => 'File size limit exceeded.',
  UPLOAD_ERR_FORM_SIZE => 'File size limit exceeded.',
  UPLOAD_ERR_PARTIAL => 'The file was not fully uploaded.',
  UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
  UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary directory.',
  UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
  UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
  UPLOAD_ERR_EXISTS => 'A file with the same name already exists.',
  UPLOAD_ERR_OVER_LIMIT => 'Account capacity exceeded.',
  UPLOAD_ERR_NOT_POST => 'File was not uploaded properly using POST method.',
  UPLOAD_ERR_CANT_MOVE => 'Could not move uploaded file.'
);

function pw_genSalt() {
  return substr(md5(uniqid()), 0, 8);
}

function pw_hash($pwd, $salt) {
  return hash('sha256', $pwd . $salt);
}

function pw_test($pwd, $salt, $hash) {
  return strcmp(pw_hash($pwd, $salt), $hash) == 0;
}

function up_footer() {
  $timeStr = date('H:i:s', time());
  return "Page loaded at " . $timeStr . " (UTC) / <span class=\"copyright\"></span>";
}

/*
  Authenticates information. Returns uid on success, false on failure.
*/
function up_auth($email, $password) {
  $queryStr = "SELECT uid, hash, salt FROM Login WHERE email=" . db_quote($email);
  $result = db_query($queryStr);

  if(!empty($result)) {
    $result = $result[0];
    return pw_test($password, $result['salt'], $result['hash']) ? $result['uid'] : false;
  }
  return false;
}

function up_beginSession($uid, $email) {
  ss_begin($uid);
  ss_set('email', $email);
}

/*
  Important management functions!
  Require database access.
*/

function up_mailPassword($email, $password) {
  $subject = 'Your miniupload password';

  $body  = "<p>Your password is:</p>\n";
  $body .= "<blockquote>" . $password . "</blockquote>\n";
  $body .= "<p>You may change your password once you log in.</p>\n";
  $body .= "<hr />\n";
  $body .= "<p>joak.io - <a href=\"http://joak.io/miniupload\">miniupload</a></p>";

  $headers  = "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=iso-8859-1\n";
  $headers .= "From: joak <noreply@joak.io>\n";

  mail($email, $subject, $body, $headers);
}

function up_register($email) {
  $salt = pw_genSalt();
  $password = pw_genSalt();
  $hash = pw_hash($password, $salt);

  $queryStr  = 'INSERT INTO Login (email, salt, hash) VALUES (';
  $queryStr .= db_quote($email) . ',';
  $queryStr .= db_quote($salt) . ',';
  $queryStr .= db_quote($hash) . ')';
  db_query($queryStr);

  $uid = db_insertId();
  mkdir('files' . DIRECTORY_SEPARATOR . $uid);

  up_mailPassword($email, $password);
}

function up_setPassword($uid, $password) {
  $salt = pw_genSalt();
  $hash = pw_hash($password, $salt);

  $queryStr  = 'UPDATE Login SET ';
  $queryStr .= 'salt = ' . db_quote($salt) . ',';
  $queryStr .= 'hash = ' . db_quote($hash) . ' ';
  $queryStr .= 'WHERE uid = ' . $uid;
  db_query($queryStr);
}

function up_resetPassword($uid, $email) {
  $salt = pw_genSalt();
  $password = pw_genSalt();
  $hash = pw_hash($password, $salt);

  $queryStr  = 'UPDATE Login SET ';
  $queryStr .= 'salt = ' . db_quote($salt) . ',';
  $queryStr .= 'hash = ' . db_quote($hash) . ' ';
  $queryStr .= 'WHERE uid = ' . $uid;
  db_query($queryStr);

  up_mailPassword($email, $password);
}

function up_getFileByName($uid, $fname) {
  $queryStr = "SELECT fid, mimetype, size, time FROM Files WHERE uid=" . $uid . " AND fname=" . db_quote($fname);
  $result = db_query($queryStr);
  return empty($result) ? null : $result[0];
}

function up_getFileByFID($fid) {
  $queryStr = "SELECT uid, fname, mimetype, size, time FROM Files WHERE fid=" . $fid;
  $result = db_query($queryStr);
  return empty($result) ? null : $result[0];
}

function up_insertFile($uid, $fname, $mimeType, $size) {
  $queryStr = "INSERT INTO Files (uid, fname, mimetype, size, time) VALUES (" .
    implode(',', array($uid, db_quote($fname), db_quote($mimeType), $size, time())) .
  ")";
  db_query($queryStr);
  return db_insertId();
}

function up_updateFile($uid, $fname, $mimeType, $size) {
  $queryStr = "UPDATE Files SET mimetype = " . db_quote($mimeType) . ", size = " . $size . ", time = " . time() .
    " WHERE uid=" . $uid . " AND fname=" . db_quote($fname);
  db_query($queryStr);
  return db_insertId();
}

function up_removeFile($uid, $fid) {
  $userRoot = 'files' . DIRECTORY_SEPARATOR . $uid;
  fs_rm($userRoot . DIRECTORY_SEPARATOR . $fid);

  $queryStr = "DELETE FROM Files WHERE fid=" . $fid;
  db_query($queryStr);
}

function up_removeAccount($uid) {
  $userRoot = 'files' . DIRECTORY_SEPARATOR . $uid;
  fs_rm($userRoot);

  $queryStr = "DELETE FROM Files WHERE uid=" . $uid;
  db_query($queryStr);

  $queryStr = "DELETE FROM Login WHERE uid=" . $uid;
  db_query($queryStr);
}

function up_getUID($email) {
  $queryStr = "SELECT uid FROM Login WHERE email=" . db_quote($email);
  $result = db_query($queryStr);
  return empty($result) ? null : $result;
}

function up_uploadFiles($uid, $f, $overwrite, &$out) {
  if($f['error'][0] == UPLOAD_ERR_NO_FILE) {
    return UPLOAD_ERR_NO_FILE;
  }

  $n = count($f['error']);
  $overFiles = array();
  $overSize = 0;
  $uploadSize = 0;

  for($i = 0; $i < $n; $i++) {
    if($f['error'][$i] != UPLOAD_ERR_OK) {
      $out[$f['name'][$i]] = $f['error'][$i];
    } else if(!is_uploaded_file($f['tmp_name'][$i])) {
      $out[$f['name'][$i]] = UPLOAD_ERR_NOT_POST;
    } else {
      $uploadSize += $f['size'][$i];
      $oldFile = up_getFileByName($uid, $f['name'][$i]);
      if(isset($oldFile)) {
        if(!$overwrite) {
          $out[$f['name'][$i]] = UPLOAD_ERR_EXISTS;
        } else {
          $overFiles[$f['name'][$i]] = $oldFile;
          $overSize += (int) $oldFile['size'];
        }
      }
    }
  }

  if(!empty($out)) {
    return UPLOAD_ERR_NO_UPLOAD;
  }

  $userRoot = 'files' . DIRECTORY_SEPARATOR . $uid;
  $totalSize = fs_getSize($userRoot) + $uploadSize - $overSize;
  if($totalSize > UPLOAD_LIMIT) {
    return UPLOAD_ERR_OVER_LIMIT;
  }

  for($i = 0; $i < $n; $i++) {
    if(isset($overFiles[$f['name'][$i]])) {
      $fid = (int) $overFiles[$f['name'][$i]]['fid'];
      up_updateFile($uid, $f['name'][$i], $f['type'][$i], $f['size'][$i]);
    } else {
      $fid = up_insertFile($uid, $f['name'][$i], $f['type'][$i], $f['size'][$i]);
    }
    if(!move_uploaded_file($f['tmp_name'][$i], $userRoot . DIRECTORY_SEPARATOR . $fid)) {
      $out[$f['name'][$i]] = UPLOAD_ERR_CANT_MOVE;
    }
  }

  return empty($out) ? UPLOAD_ERR_OK : UPLOAD_ERR_SOME_UPLOAD;
}

function up_sanityCheck($uid) {
  // TODO ???
  return true;
}
?>
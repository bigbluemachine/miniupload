# miniupload

Very minimalist file uploader service I made in PHP many years ago.

## Requirements

* PHP (works with verseion 7, but also worked with 5 IIRC)
* MySQL
* Apache (any web server really, but need to replace .htaccess with something suitable)

## Database Setup

Initialize stuff in SQL.

```
CREATE DATABASE 'Uploader';
CREATE USER 'uploader'@'localhost' identified by 'YOUR_PASSWORD_HERE';
GRANT ALL ON Uploader.* TO 'uploader'@'localhost';

#
# Login table.
# Uses SHA-256 for encryption.
#
CREATE TABLE Login (
  uid       int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  email     varchar(256) UNIQUE NOT NULL,
  hash      varchar(64),
  salt      varchar(8) NOT NULL
);

#
# Table of all files.
# Each file has a unique ID, as well as owner and path.
#
CREATE TABLE Files (
  fid       int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  uid       int NOT NULL,
  fname     text NOT NULL,
  mimetype  varchar(256) NOT NULL,
  size      int NOT NULL,
  time      int NOT NULL
);
```

## Additional Setup

This application sends confirmation e-mails for newly registered accounts. The e-mail address and contents are defined in the function `up_mailPassword` in the file `_up.php`. Most hosting providers offer you e-mail addresses from which you can send mail; just create an e-mail address and change the contents of that function. If hosting it from your own server, you will have to set up SMTP (which I will not go into here).

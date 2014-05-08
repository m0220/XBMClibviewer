XBMClibviewer 0.1
============================================================
Html viewer of the data stored in the Mysql's database managed by the popular XBMC media center


Tested on
============================================================

XBMC 12.3 FRODO (Linux and Windows)

Scraper Series: The TVDB (Language IT)

Scraper Films: The movie database (Preferred Lang:it)

Apache/2.2.22 (Debian)

PHP 5.4.4-14+deb7u9

mysql 5.5.35-0+wheezy1 (Debian)



Pre-Requisites
============================================================
1. XBMC configured to work with MySQL DB (*http://wiki.xbmc.org/index.php?title=MySQL)
2. A webserver apache with php5 (mysql server is not necessary that resides on the same server of XBMClibviewer)
*See this guide - http://www.howtoforge.com/installing-apache2-with-php5-and-mysql-support-on-ubuntu-10.10-lamp or search on google.


Installation/Configuraton
============================================================
- Download the package
- Create a folder in the wwwroot of your webserver
  eg: /var/www/XBMClibviewer/ (or in a subfolder that you like)
- Unzip the package in the folder that you have chose
- Edit the file /var/www/XBMClibviewer/config.php with the parameters of your MySQL server (XBMC userid and password):

example:

```
......
  $hostname = "localhost";
  $database = "MyVideos75";
  $db_user = "xbmc";
  $db_pass = "xbmc";
  $xbmc_lounge = "xbmc:xbmc@192.168.0.101:8080";
......
```

- Verify that this url display your XBMC library correctly:
  http://servername-or-ip/XBMClibviewer/films.php se il tutto funziona.


Option with access credentials
============================================================
To authenticate on our XBMClibviewer with credentials, edit the file:
/var/www/XBMClibviewer/access.php and insert username and password that you like to use:

```
....
// set your username and password
$setusername = 'xbmc';
$setpassword = 'xbmc';
....
```

To enable the option uncomment the line "//require ('access.php');":
```
....
<?php
   //richiamo pagina per accesso con utente e password
---> require ('access.php'); <---
....
```

of these files:
 
/var/www/XBMClibviewer/films.php 

/var/www/XBMClibviewer/series.php 


Link Demo
============================================================
http://m0220.it

http://mozzo.altervista.org/m0220/xbmclibviewer


Mail
============================================================
dr_mozzo@hotmail.it

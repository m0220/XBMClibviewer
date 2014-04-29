<?php
session_start();
header("Cache-control: private");

//CONFIG
// --------------------------------------------------

// set your username and password
$setusername = 'xbmc';
$setpassword = 'xbmc';


// don't edit below this line
// --------------------------------------------------

if ($_GET['action'] == 'logout') {
	$_SESSION = array();
	session_destroy();
}

$showform = true;

if ($_POST['action'] == 'login') {

   $replace = array('(' => '&#40;', ')' => '&#41;', '#' => '&#35;');
   $username = strtr(htmlspecialchars(strip_tags($_POST['username'])), $replace);
   $password = strtr(htmlspecialchars(strip_tags($_POST['password'])), $replace);

	if (($username != $setusername) || ($password != $setpassword)) {
		$errormsg = true;
	} else {	
		$showform = false;
		$_SESSION['username'] = $username;
		$_SESSION['password'] = md5($password);
	}

} else {
	
	if (($_SESSION['username'] == $setusername) && ($_SESSION['password'] == md5($setpassword))) {
      $showform = false;
	}

}

if ($showform) {

// don't edit above this line
// --------------------------------------------------
?>


<!-- login page -->

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<title>Login Required</title>
<style type="text/css">
            .normale { font-size:11px;font-family:monospace;}
            .titolo { font-size:11px;font-family:monospace; }
            a:link { color:black;font-size:11px;font-family:monospace; }
            a:visited { color:black;font-size:11px;font-family:monospace; }
</style>
</head>
<body>
<center><font class='titolo'>
Login Required
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="action" value="login" />

<?php
// don't edit this unless you know what you are doing
if ($errormsg) {
   echo '<p class="errormsg">Invalid login!  Try again.</p>';
}
?>

<p>
Username:<br />
<input type="text" name="username" />
</p>

<p>
Password:<br />
<input type="password" name="password" />
</p>

<p>
<input type="submit" value="Submit" />
</p>
</font>
</form>
</center>
</body>
</html>

<!-- login page ends -->


<?php
// don't edit below this line
// --------------------------------------------------

	exit;
}
?>

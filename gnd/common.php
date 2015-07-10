<!DOCTYPE = html>
<html lang="hu">
<head>
	<meta charset="utf-8">
	<title>Budapest University of Technology and Economics terrestrial transmitter homepage</title>
</head>
<body>
<h1>On a greaat big clipper ship...</h1>
<?php
include_once('func.php');

sec_session_start();

if(isset($_POST['logout'])){
	logout();
	header('Location: /gnd/common.php');
}

if(isset($_GET['lang'])){
	$language = htmlspecialchars($_GET['lang']);
	$_SESSION['lang'] = $language;
	setcookie('lang', $language, time() + (3600*24*30));
}else if(isSet($_SESSION['lang'])){
	$language = $_SESSION['lang'];
}else if(isSet ($_COOKIE['lang'])){
	$language = $_COOKIE['lang'];
}else{
	$language = 'hu';
}
if($language != 'hu' && $language != 'en'){
	$language = 'hu';
}
include_once('lang.'.$language.'.php');

echo '<figure><a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?lang=hu"><img src="/gnd/pic/huFlag.png" /></a>';
echo '<a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?lang=en"><img src="/gnd/pic/enFlag.png" /></a></figure>';
echo '<br><br>';

if(isset($_SESSION['username'])){
	echo 'YOU ARE LOGGED IN AS: ' . $_SESSION['username'];
	echo '<form method=\'post\' action=\'' . htmlspecialchars($_SERVER['PHP_SELF']) . '\'><br>
<input type="submit" name="logout" value="logout" />
</form><br><br>';
}else{
	echo 'you are not logged in';
}

echo '<nav>
<h2 hidden>navigation: </h2>
<table>
<tr><td><a href="/gnd/index.php">' . $lang['HOME'] . '</a></td>
<td><a href="/gnd/news.php">' . $lang['NEWS'] . '</td>
<td><a href="/gnd/gallery.php">' . $lang['GALLERY'] . '</td>
<td><a href="/gnd/members.php">' . $lang['MEMBERS'] . '</td>
<td><a href="/gnd/sponsors.php">' . $lang['SPONSORS'] . '</td>
<td><a href="/gnd/account.php">' . $lang['ACCOUNT'] . '</a></td></tr>
</table>
</nav>';

/*echo '<nav>
<h2 hidden>navigation: </h2>
<ul>
<li><a href="/gnd/common.php">' . $lang['HOME'] . '</a></li>
<li><a href="/gnd/news.php">' . $lang['NEWS'] . '</li><li><a href="/gnd/account.php">' . $lang['ACCOUNT'] . '</a></li>
</ul>
</nav><br>';*/

/*if(isset($_SESSION['test'])){
	echo $_SESSION['test'];
}*/
//echo $_SERVER['PHP_SELF'];
if($_SERVER['PHP_SELF'] == '/gnd/common'){
	echo '</body>
	</html>';
}
?>
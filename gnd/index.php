<!DOCTYPE = html>
<html lang="hu">
<head>
	<meta charset="utf-8">
	<title>Budapest University of Technology and Economics terrestrial transmitter homepage</title>
	<style>
	input:invalid{ background-color: lightPink;}
	input:valid {background-color: lightGreen; }
	</style>
	<script>
	//messegaLog.InnerHtml()
	</script>
</head>
<body>
<h1>On a greaat big clipper ship...</h1>
<?php
//back-end
include_once('func.php');
include_once('connectDB.php');

sec_session_start();

if(isset($_POST['logout'])){
	logout();
	header('Location: /gnd/index.php');
}
if(isset($_SESSION['timeouter'])){
	if($_SESSION['timeouter'] < (time() - $logoutTime)){
		logout();
		$_SESSION['msg'] = $lang['SESSION_TIMED_OUT'];
		header('Location: /gnd/index.php');
	}else{
		$_SESSION['timeouter'] = time();
	}
}else{
	$_SESSION['timeouter'] = time();
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

if(isset($_FILES['pictureUpload'])){
	if($_FILES['pictureUpload']['error']==0) {
		$target_dir = $_SERVER['DOCUMENT_ROOT'] . '/gnd/pic/';
		$target_file = $target_dir . basename($_FILES["pictureUpload"]["name"]);
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

		$check = getimagesize($_FILES["pictureUpload"]["tmp_name"]);
		if($check !== false) {
			$uploadOk = 1;
		} else {
			$uploadOk = 0;
			$uploadError = $lang['ERR_NOT_PICTURE'];
		}
		if (file_exists($target_file)) {
			$uploadOk = 0;
			$uploadError = $lang['ERR_FILE_EXISTS'];
		}
		if ($_FILES["pictureUpload"]["size"] > $maxPictureSize) {
			$uploadOk = 0;
			$uploadError = $lang['ERR_FILE_TOO_BIG'];
		}
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
			$uploadOk = 0;
			$uploadError = $lang['ERR_NOT_PICTURE'];
		}
		if ($uploadOk == 0) {
			die($uploadError);
		} else {
			if (move_uploaded_file($_FILES["pictureUpload"]["tmp_name"], $target_file)) {
				//echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
			} else {
				die($lang['ERR_UPLOAD_DATABASE']);
			}
		}
	}
}
if(isset($_FILES['fileUpload'])){
	if($_FILES['fileUpload']['error']==0) {
		$target_dir = $_SERVER['DOCUMENT_ROOT'] . '/gnd/file/';
		$target_file = $target_dir . basename($_FILES["fileUpload"]["name"]);
		$uploadOk = 1;
		$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
		if (file_exists($target_file)) {
			$uploadOk = 0;
			$uploadError = $lang['ERR_FILE_EXISTS'];
		}
		if ($_FILES["fileUpload"]["size"] > $maxFileSize) {
			$uploadOk = 0;
			$uploadError = $lang['ERR_FILE_TOO_BIG'];
		}
		if ($uploadOk == 0) {
			die($uploadError);
		} else {
			if (move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $target_file)) {
				//echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
			} else {
				die($lang['ERR_UPLOAD_DATABASE']);
			}
		}
	}
}

//front-end
echo '<figure><a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?lang=hu"><img src="/gnd/pic/huFlag.png" /></a>';
echo '<a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?lang=en"><img src="/gnd/pic/enFlag.png" /></a></figure>';
echo '<br><br>';
if(isset($_SESSION['username'])){
	echo 'YOU ARE LOGGED IN AS: ' . $_SESSION['username'];
	echo '<form method=\'post\' action=\'' . htmlspecialchars($_SERVER['PHP_SELF']) . '\'><br>
<button type="submit" name="logout" value="logout">logout</button>
</form><br><br\>';
}else{
	echo 'you are not logged in';
}

echo '<nav>
<header>
<h2 hidden>navigation: </h2>
</header>
<table>
<tr><td><a href="/gnd/index.php">' . $lang['HOME'] . '</a></td>';

if($stmt = $mysqli->prepare("SELECT GNDMenu.id, GNDMenu.name, nameEng, text, textEng FROM GNDMenu
ORDER BY GNDMenu.id ASC")){
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $name, $nameEn, $text, $textEn);
	$stmt->fetch();
	$rowMax = $stmt->num_rows;
	for($iter = 0; $iter < $rowMax; ++$iter){
		if($language == 'hu'){
			echo '<td><a href="/gnd/index.php?article=' . $id . '">' . $name . '</a></td>';
		}else{
			echo '<td><a href="/gnd/index.php?article=' . $id . '">' . $nameEn . '</a></td>';
		}
		$stmt->fetch();
	}
}else{
	echo 'DATABASE ERROR';
	$_SESSION['msg'] = 'DATABASE ERROR';
}

echo '<td><a href="/gnd/news.php">' . $lang['NEWS'] . '</a></td>
<td><a href="/gnd/gallery.php">' . $lang['GALLERY'] . '</a></td>
<td><a href="/gnd/members.php">' . $lang['MEMBERS'] . '</a></td>
<td><a href="/gnd/sponsors.php">' . $lang['SPONSORS'] . '</a></td>
<td><a href="/gnd/account.php">' . $lang['ACCOUNT'] . '</a></td></tr>
</table>
</nav>';

if(isset($_SESSION['msg'])){
	echo $_SESSION['msg'];
	unset($_SESSION['msg']);
}

if($_SERVER['PHP_SELF'] == '/gnd/index.php'){
	if(isset($_GET['article'])){
		if($stmt = $mysqli->prepare("SELECT name, nameEng, text, textEng FROM GNDMenu  
		WHERE GNDMenu.id = ? ORDER BY GNDMenu.id DESC")){
			$stmt->bind_param('i', $_GET['article']);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($name, $nameEn, $text, $textEn);
			$stmt->fetch();
			if($stmt->num_rows < 1){
				echo '<br\> Menu not found <br\>';
			}else{
				echo '<section>
				<header>
				<h2 hidden> Articles </h2>
				</header>';
				echo '<article>
				<header>
				<h3 hidden> entry </h3>
				</header>';
				if($language == 'hu'){
					echo '<br\>' . $name . '<br\><br\>' . $text . '<br\><br\>';
				}else{
					echo '<br\>' . $nameEn . '<br\><br\>' . $textEn . '<br\><br\>';
				}
				echo '<footer>
				</footer><hr>
				</article>
				</section>';
			}
		}else{
			echo 'Database error';
		}
	}

/*echo '<nav>
<h2 hidden>navigation: </h2>
<ul>
<li><a href="/gnd/index.php">' . $lang['HOME'] . '</a></li>
<li><a href="/gnd/news.php">' . $lang['NEWS'] . '</li><li><a href="/gnd/account.php">' . $lang['ACCOUNT'] . '</a></li>
</ul>
</nav><br>';*/

/*if(isset($_SESSION['test'])){
	echo $_SESSION['test'];
}*/
//echo $_SERVER['PHP_SELF'];

	echo '</body>
	</html>';
}
?>
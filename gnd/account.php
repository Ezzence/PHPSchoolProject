<?php
include_once('index.php');
include_once('connectDB.php');

if(isset($_POST['login'])){
	login($mysqli, $_POST['loginUsername'], $_POST['loginPassword']);
	$_SESSION['logintmp'] = $_POST['loginUsername'];
	header('Location: account.php');
}else{
	unset($_SESSION['msg']);
}
if(!isset($_SESSION['logintmp'])){
	$_SESSION['logintmp'] = '';
}

if(login_check($mysqli)){
	echo '<br><table>';
	if($_SESSION['newsPriv']){
		echo '<tr><td><a href=\'/gnd/priv/edit_news.php\'>' . $lang['EDIT_NEWS'] . '</a></td></tr>';
	}
	if($_SESSION['registerPriv']){
		echo '<tr><td><a href=\'/gnd/priv/add_account.php\'>' . $lang['ADD_ACCOUNT'] . '</a></td></tr>';
	}
	if($_SESSION['admin']){
		echo '<tr><td><a href=\'/gnd/priv/manage_accounts.php\'>' . $lang['MANAGE_ACCOUNTS'] . '</a></td></tr>';
	}
	if($_SESSION['galleryPriv']){
		echo '<tr><td><a href=\'/gnd/priv/edit_gallery.php\'>' . $lang['EDIT_GALLERY'] . '</a></td></tr>';
	}
	if($_SESSION['teamPriv']){
		echo '<tr><td><a href=\'/gnd/priv/edit_members.php\'>' . $lang['EDIT_MEMBERS'] . '</a></td></tr>';
	}
	if($_SESSION['menuPriv']){
		echo '<tr><td><a href=\'/gnd/priv/edit_menu.php\'>' . $lang['EDIT_MENU'] . '</a></td></tr>';
	}
	if($_SESSION['sponsorPriv']){
		echo '<tr><td><a href=\'/gnd/priv/edit_sponsors.php\'>' . $lang['EDIT_SPONSORS'] . '</a></td></tr>';
	}
	if($_SESSION['filePriv']){
		echo '<tr><td><a href=\'/gnd/priv/edit_files.php\'>' . $lang['EDIT_FILES'] . '</a></td></tr>';
	}
	echo '<tr><td><a href=\'/gnd/edit_account.php\'>' . $lang['EDIT_ACCOUNT'] . '</a></td></tr>';
	echo '</table>';
}
else{
	if(isset($_SESSION['error'])){
		echo $_SESSION['error'];
	}
	echo '<form method=\'post\' action=\'' . htmlspecialchars($_SERVER['PHP_SELF']) . '\'><br>
	Username: <input type=\'text\' name = \'loginUsername\' value =\'' . $_SESSION['logintmp'] . '\'/><br>
	Password: <input type=\'password\' name = \'loginPassword\' /><br>
	<button type="submit" name="login" value="' . $lang['LOGIN'] . '">' . $lang['LOGIN'] . '</button>
	</form><br><br>';
 
}
echo '</body>
	</html>';

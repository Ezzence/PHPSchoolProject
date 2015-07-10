<?php
include_once('../index.php');
include_once('../connectDB.php');

if(isset($_SESSION['msg'])){
	echo $_SESSION['msg'];
}

if(login_check($mysqli) && $_SESSION['admin']){
	
	if(isset($_POST['save']) && ($_SESSION['tmpid'] != 1)){
		if ($stmt = $mysqli->prepare("UPDATE Account SET newsPriv = ?, teamPriv = ?, sponsorPriv = ?, galleryPriv = ?, 
		menuPriv = ?, registerPriv = ?, filePriv = ?, Admin = ? WHERE Account.id = ?")) {
			if(isset($_POST['adminCheck'])){
				$adminCheck = 1;
			}else{
				$adminCheck = 0;
			}
			if(isset($_POST['newsCheck'])){
				$newsCheck = 1;
				$_SESSION['msg'] = $newsCheck;
			}
			else{
				$newsCheck = 0;
				$_SESSION['msg'] = $newsCheck;
			}
			if(isset($_POST['registerCheck'])){
				$registerCheck = 1;
			}
			else{
				$registerCheck = 0;
			}
			if(isset($_POST['galleryCheck'])){
				$galleryCheck = 1;
			}
			else{
				$galleryCheck = 0;
			}
			if(isset($_POST['teamCheck'])){
				$teamCheck = 1;
			}
			else{
				$teamCheck = 0;
			}
			if(isset($_POST['menuCheck'])){
				$menuCheck = 1;
			}
			else{
				$menuCheck = 0;
			}
			if(isset($_POST['sponsorCheck'])){
				$sponsorCheck = 1;
			}
			else{
				$sponsorCheck = 0;
			}
			if(isset($_POST['fileCheck'])){
				$fileCheck = 1;
			}
			else{
				$fileCheck = 0;
			}
			$userid = $_SESSION['tmpid'];
			if($userid != 1){
				$stmt->bind_param('iiiiiiiii', $newsCheck, $teamCheck, $sponsorCheck, $galleryCheck, $menuCheck, $registerCheck, 
				$fileCheck, $adminCheck, $userid);
				$stmt->execute();
				$_SESSION['msg'] = 'PRIVILEGES UPDATED SUCCESSFULLY';
			}
			header('Location: manage_accounts.php');
		}else{
			$_SESSION['msg'] = 'DATABASE ERROR';
			header('Location: manage_accounts.php');
		}
	}else{
		unset($_SESSION['msg']);
	}
	
	if(isset($_GET['username'])){
		if($stmt = $mysqli->prepare("SELECT id, accName, password, email, name, newsPriv, teamPriv, sponsorPriv, galleryPriv, menuPriv, 
		registerPriv, filePriv, Admin FROM Account WHERE accName = ? LIMIT 1")){
			$username = htmlspecialchars($_GET['username']);
			$stmt->bind_param('s', $username);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($userid, $accName, $dbPassword, $email, $name, $newsPriv, $teamPriv, $sponsorPriv, $galleryPriv, $menuPriv, 
			$registerPriv, $filePriv, $admin);
			$stmt->fetch();
			$_SESSION['tmpid'] = $userid;
			if($stmt->num_rows < 1 || $userid == 1){
				echo '<br> REQUEST NOT VALID <br>';
			}
			else{
				if($admin){
					$adminChecked = 'checked';
				}else{
					$adminChecked = '';
				}
				if($newsPriv){
					$newsChecked = 'checked';
				}else{
					$newsChecked = '';
				}
				if($registerPriv){
					$registerChecked = 'checked';
				}else{
					$registerChecked = '';
				}
				if($galleryPriv){
					$galleryChecked = 'checked';
				}else{
					$galleryChecked = '';
				}
				if($teamPriv){
					$teamChecked = 'checked';
				}else{
					$teamChecked = '';
				}
				if($menuPriv){
					$menuChecked = 'checked';
				}else{
					$menuChecked = '';
				}
				if($sponsorPriv){
					$sponsorChecked = 'checked';
				}else{
					$sponsorChecked = '';
				}
				if($filePriv){
					$fileChecked = 'checked';
				}else{
					$fileChecked = '';
				}
				echo '<br>' . $_GET['username'] . ': Edit Privileges: <br> <form method=\'post\' action=\'' . htmlspecialchars($_SERVER['PHP_SELF']) . '\'><br>
				<table>
				<tr><td>' . $lang['ADMIN_PRIVILEGE'] . '</td><td>' . $lang['NEWS_PRIVILEGE'] . '</td><td>' . $lang['REGISTER_PRIVILEGE'] . '</td>
				<td>' . $lang['GALLERY_PRIVILEGE'] . '</td><td>' . $lang['TEAM_PRIVILEGE'] . '</td><td>' . $lang['MENU_PRIVILEGE'] . '</td>
				<td>' . $lang['SPONSORS_PRIVILEGE'] . '</td><td>' . $lang['FILES_PRIVILEGE'] . '</td></tr>
				<tr><form method=\'post\' action=\'' . htmlspecialchars($_SERVER['PHP_SELF']) . '\'>
				<td><input type=\'checkbox\' name = \'adminCheck\' ' . $adminChecked . '/></td>
				<td><input type=\'checkbox\' name = \'newsCheck\' value="1"' . $newsChecked . '/></td>
				<td><input type=\'checkbox\' name = \'registerCheck\' '. $registerChecked . ' /></td>
				<td><input type=\'checkbox\' name = \'galleryCheck\' ' . $galleryChecked . '/></td>
				<td><input type=\'checkbox\' name = \'teamCheck\' ' . $teamChecked . ' /></td>
				<td><input type=\'checkbox\' name = \'menuCheck\' ' . $menuChecked . '/></td>
				<td><input type=\'checkbox\' name = \'sponsorCheck\' ' . $sponsorChecked . '/></td>
				<td><input type=\'checkbox\' name = \'fileCheck\' ' . $fileChecked . '/></td>
				<td><input type=\'submit\' name=\'save\' value=\'save\' /></td>
				</tr>
				</form>
				</table><br><br>';
			}
		}

		
	}
	if($stmt = $mysqli->prepare("SELECT accName FROM Account ORDER BY accName ASC")){
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($accName);
		$stmt->fetch();
		$rowMax = $stmt->num_rows;
		echo '<br>';
		for($iter = 0; $iter < $rowMax; ++$iter){
			echo '<a href=' . htmlspecialchars($_SERVER['PHP_SELF']) . '?username=' . $accName . '>' . $accName . '</a><br>';
			$stmt->fetch();
		}
	}

}else{
	echo "NOT ALLOWED";
}
echo '</body>
	</html>';
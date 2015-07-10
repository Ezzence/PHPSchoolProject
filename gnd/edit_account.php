<?php
include_once('/index.php');
include_once('/connectDB.php');

if(isset($_SESSION['msg'])){
	echo $_SESSION['msg'];
}

if(login_check($mysqli)){
	
	if(isset($_POST['editPassword'])){
		editPassword($mysqli, $_POST['oldPasswordText'], $_POST['passwordText1'], $_POST['passwordText2']);
		header('Location: edit_account.php');
	}
	if(isset($_POST['editName'])){
		editName($mysqli, $_POST['passwordText'], $_POST['nameText']);
		header('Location: edit_account.php');
	}
	if(isset($_POST['editAccountName'])){
		editAccontName($mysqli, $_POST['passwordText'], $_POST['accountNameText1'], $_POST['accountNameText2']);
		header('Location: edit_account.php');
	}
	if(isset($_POST['editEmail'])){
		editEmail($mysqli, $_POST['passwordText'], $_POST['emailText1'], $_POST['emailText2']);
		header('Location: edit_account.php');
	}
	
	
	echo '<br>Edit Password: <br> <form method=\'post\' action=\'' . htmlspecialchars($_SERVER['PHP_SELF']) . '\'><br>
	Old Password: <input type=\'password\' name = \'oldPasswordText\' /><br>
	New Password : <input type=\'password\' name = \'passwordText1\' /><br>
	New Password again: <input type=\'password\' name = \'passwordText2\' /><br>
	<input type=\'submit\' name=\'editPassword\' value=\'edit\' />
	</form><br><br>';
	echo 'Edit Name: <br> <form method=\'post\' action=\'' . htmlspecialchars($_SERVER['PHP_SELF']) . '\'><br>
	Password: <input type=\'password\' name = \'passwordText\' /><br>
	New Name: <input type=\'text\' name = \'nameText\' /><br>
	<input type=\'submit\' name=\'editName\' value=\'edit\' />
	</form><br><br>';
	echo 'Edit Account Name:<br><form method=\'post\' action=\'' . htmlspecialchars($_SERVER['PHP_SELF']) . '\'><br>
	Password: <input type=\'password\' name = \'passwordText\' /><br>
	New Account Name: <input type=\'text\' name = \'accountNameText1\' /><br>
	New Account Name again: <input type=\'text\' name = \'accountNameText2\' /><br>
	<input type=\'submit\' name=\'editAccountName\' value=\'edit\' />
	</form><br><br>';
	echo 'Edit Email:<br> <form method=\'post\' action=\'' . htmlspecialchars($_SERVER['PHP_SELF']) . '\'><br>
	Password: <input type=\'password\' name = \'passwordText\' /><br>
	New Email: <input type=\'text\' name = \'emailText1\' /><br>
	New Email again: <input type=\'text\' name = \'emailText2\' /><br>
	<input type=\'submit\' name=\'editEmail\' value=\'edit\' />
	</form><br><br>';
}else{
	echo "NOT ALLOWED";
}
echo '</body>
	</html>';
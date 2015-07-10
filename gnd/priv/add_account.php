<?php
include_once('../index.php');
include_once('../connectDB.php');

if(isset($_SESSION['msg'])){
	echo $_SESSION['msg'];
}

if(login_check($mysqli) && $_SESSION['registerPriv']){
	
	if(isset($_POST['add'])){
		addAccount($mysqli, $_POST['emailText'], $_POST['accountText'], $_POST['nameText']);
		header('Location: add_account.php');
	}
	echo '<form method=\'post\' action=\'' . htmlspecialchars($_SERVER['PHP_SELF']) . '\'><br>
	<fieldset>
	<legend>új felhasználó</legend>
	<label for="emailText"> Email: </label> <input type="email" name = "emailText" id="emailText" required /><br>
	<label for="accountText"> Account: </label> <input type="text" name="accountText" id="accountText" 
	pattern="^[a-zA-Z][a-zA-Z0-9-_\.]{1,20}$"
	title="2-20 karakter, betűvel kezdve"
	required /><br>
	<label for="nameText"> Name: </label><input type="text" name = "nameText" id="nameText" required/><br>
	<button type="submit" name="add" value="add" > add </button>
	</fieldset>
	</form><br><br>';
}else{
	echo "NOT ALLOWED";
}
echo '</body>
	</html>';
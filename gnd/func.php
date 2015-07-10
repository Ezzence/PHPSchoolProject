<?php

include_once 'init.php';

function sec_session_start() {
    $session_name = 'sec_session_id';  
    $secure = SECURE;
    $httponly = true;

    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
        exit();
    }

    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"],
        $cookieParams["path"], 
        $cookieParams["domain"], 
        $secure,
        $httponly);

    session_name($session_name);
    session_start();           
    session_regenerate_id(true);
}
function generatePassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $count = mb_strlen($chars);

    for ($i = 0, $result = ''; $i < $length; $i++) {
        $index = rand(0, $count - 1);
        $result .= mb_substr($chars, $index, 1);
    }

    return $result;
}
function logout(){
	sec_session_start();
	$_SESSION = array();
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time()-42000, $params['path'], 
		$params['domain'], $params['secure'], $params['httponly']);
	session_destroy();
	unset($_SESSION);
	session_regenerate_id(true);
	header('Location: ' . htmlspecialchars($_SERVER['PHP_SELF']));
}
function bruteCheck($mysqli){
	$now = time();
	
	$stmt = $mysqli->prepare("DELETE FROM BruteCheck WHERE loginStamp < (? - 600)");
	$stmt->bind_param('i', $now);
	$stmt->execute();
	
	$address = $_SERVER['REMOTE_ADDR'];
	
	$stmt = $mysqli->prepare("SELECT ip, loginStamp, loginNum FROM BruteCheck WHERE ip = ? LIMIT 1");
	$stmt->bind_param('s', $address);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($ip, $loginStamp, $loginNum);
	$stmt->fetch();
	
	if($stmt->num_rows < 1){
		$stmt = $mysqli->prepare("INSERT INTO BruteCheck (ip, loginStamp, loginNum) VALUES (?, ?, ?)");
		$default = 1;
		$stmt->bind_param('sii', $address, $now, $default);
		$stmt->execute();
		return false;
	}else{
		if($loginNum < 10){
			$stmt = $mysqli->prepare("UPDATE BruteCheck SET BruteCheck.loginNum = ? WHERE BruteCheck.ip = ?");
			++$loginNum;
			$stmt->bind_param('ii',$loginNum, $ip);
			$stmt->execute();
			return false;
		}else{
			$_SESSION['error'] = 'TOO MANY ATTEMPTS, YOU CAN TRY AGAIN IN '. ($loginStamp + 10*60 - $now) . ' SECONDS';
			return true;
		}
	}
}
	
function login($mysqli, $username, $password){
	if(bruteCheck($mysqli) == true){
		return false;
	}
	if($stmt = $mysqli->prepare("SELECT id, accName, password, email, name, newsPriv, teamPriv, sponsorPriv, galleryPriv, menuPriv, 
	registerPriv, filePriv, Admin FROM Account WHERE accName = ? LIMIT 1")){
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($userid, $accName, $dbPassword, $email, $name, $newsPriv, $teamPriv, $sponsorPriv, $galleryPriv, $menuPriv, 
		$registerPriv, $filePriv, $admin);
		$stmt->fetch();
		$password = sha1($password);
		if($stmt->num_rows == 1){
			if($dbPassword == $password){
				 $userid = preg_replace("/[^0-9]+/", "", $userid);
                 $_SESSION['userid'] = $userid;
                 $accName = htmlspecialchars($accName);
                 $_SESSION['username'] = $accName;
				 if($newsPriv){
					$_SESSION['newsPriv'] = true;
				 }else{
					 $_SESSION['newsPriv'] = false;
				 }
				 if($teamPriv){
					$_SESSION['teamPriv'] = true;
				 }else{
					 $_SESSION['teamPriv'] = false;
				 }
				 if($sponsorPriv){
					$_SESSION['sponsorPriv'] = true;
				 }else{
					 $_SESSION['sponsorPriv'] = false;
				 }
				 if($galleryPriv){
					$_SESSION['galleryPriv'] = true;
				 }else{
					 $_SESSION['galleryPriv'] = false;
				 }
				 if($menuPriv){
					 $_SESSION['menuPriv'] = true;
				 }else{
					 $_SESSION['menuPriv'] = false;
				 }
				  if($registerPriv){
					 $_SESSION['registerPriv'] = true;
				 }else{
					 $_SESSION['registerPriv'] = false;
				 }
				 if($filePriv){
					 $_SESSION['filePriv'] = true;
				 }else{
					 $_SESSION['filePriv'] = false;
				 }
				 if($admin){
					$_SESSION['admin'] = true;
				 }else{
					 $_SESSION['admin'] = false;
				 }
				 
				 $stmt = $mysqli->prepare("UPDATE Account SET Account.sessionPass = ? WHERE Account.id = ?");
				 $_SESSION['pass'] = rand();
				 $tmp = (sha1($_SESSION['pass'] . $_SERVER['HTTP_USER_AGENT']));
				 $stmt->bind_param('si', $tmp, $userid);
				 $stmt->execute();
				 
				 $address = $_SERVER['REMOTE_ADDR'];
				 $stmt = $mysqli->prepare("UPDATE BruteCheck SET BruteCheck.loginNum = ? WHERE BruteCheck.ip = ?");
				 $default = 0;
				 $stmt->bind_param('is', $default, $address);
				 $stmt->execute();
				 
				 return true;
			}else{
				$_SESSION['error'] = 'INVALID USERNAME OR PASSWORD';
				return false;
			}
		}else{
			$_SESSION['error'] = 'INVALID USERNAME OR PASSWORD';
			return false;
		}
	}
	$_SESSION['error'] = 'DATABASE ERROR';
}
function login_check($mysqli){
	if(isset($_SESSION['userid'], $_SESSION['username'], $_SESSION['pass'])){
		$userid = $_SESSION['userid'];
		$username = $_SESSION['username'];
		$pass = sha1($_SESSION['pass'] . $_SERVER['HTTP_USER_AGENT']);
		if($stmt = $mysqli->prepare("SELECT sessionPass FROM Account WHERE id = ? LIMIT 1")){
			$stmt->bind_param('i', $userid);
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows == 1){
				$stmt->bind_result($password);
				$stmt->fetch();
				if($password == $pass){
					return true;
				}else{
					logout();
					return false;
				}
			}else{
				logout();
				return false;
			}
		}else{
		return false;    //mysqli ERROR ???
		}
	}else{
	return false;
	}
}
function addAccount($mysqli, $email, $accName = 'default', $name = 'default'){
	if(!login_check($mysqli)){
		return false;
	}
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
		$_SESSION['msg'] = 'INVALID EMAIL';
		return false;
	}
	if(mb_strlen($accName) < 3){
		$_SESSION['msg'] = 'ACCOUNT NAME MUST BE AT LEAST 3 CHARACTERS LONG';
		return false;
	}
	if($accName != htmlspecialchars($accName)){
		$_SESSION['msg'] = 'ACCOUNT NAME MAY ONLY CONTAIN LETTERS AND NUMBERS';
		return false;
	}
	if($name != htmlspecialchars($name)){
		$_SESSION['msg'] = 'NAME MAY ONLY CONTAIN LETTERS AND NUMBERS';
		return false;
	}
	$password = generatePassword();
	$passwordDB = sha1($password);
	
	$tmp = 'this should remain empy';
	if($stmt = $mysqli->prepare("SELECT accName FROM Account WHERE accName = ?")){
		$stmt->bind_param('s', $accName);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($tmp);
		$stmt->fetch();
		if($stmt->num_rows > 0){
			$_SESSION['msg'] = 'ACCOUNT NAME ALREADY IN USE';
			return false;
		}
	}
	if($stmt = $mysqli->prepare("SELECT email FROM Account WHERE email = ?")){
		$stmt->bind_param('s', $email);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($tmp);
		$stmt->fetch();
		if($stmt->num_rows > 0){
			$_SESSION['msg'] = 'EMAIL ALREADY IN USE';
			return false;
		}
	}
	
	if ($stmt = $mysqli->prepare("INSERT INTO Account (accName, password, email, name, regDate, newsPriv, teamPriv, 
	sponsorPriv, galleryPriv, menuPriv, registerPriv, filePriv, Admin) VALUES (?, ?, ?, ?, CURDATE(), 0, 0, 0, 0, 0, 0, 0, 0)")) {
		$stmt->bind_param('ssss', $accName, $passwordDB, $email, $name);
		$stmt->execute();
	}else{
		$_SESSION['msg'] = 'DATABASE ERROR';
	}
	$to = $email;
	$subject = 'Registration at BMEGND';
	$message = "An account with your email has been registered at" . WEBSITE
	. "\r\n You can now log in using the following: \r\n Username: {$accName}
	\r\n Password: {$password} \r\n You can change your account details "
	. "on the website after you have logged in";
	$message = wordwrap($message, 70, "\r\n");
	$header = 'From: '. GNDEMAIL;
	$_SESSION['msg'] = mail($to, $subject, $message, $header);
	if($_SESSION['msg'] === true){
		$_SESSION['msg'] = 'NEW USER ADDED SUCCESSFULLY';
	}	
}
function editPassword($mysqli, $oldPassword, $password1, $password2){
	$userid = $_SESSION['userid'];
	if(!login_check($mysqli)){
		return false;
	}
	if($password1 != $password2){
		$_SESSION['msg'] = 'PASSWORDS MUST MATCH';
		return false;
	}
	if(mb_strlen($password1) < 6){
		$_SESSION['msg'] = 'PASSWORDS MUST BE AT LEAST 6 CHARACTERS LONG';
		return false;
	}
	if($stmt = $mysqli->prepare("SELECT password FROM Account WHERE id = ? LIMIT 1")){
		$stmt->bind_param('i', $userid);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($dbPassword);
		$stmt->fetch();
		$oldPassword = sha1($oldPassword);
		if($stmt->num_rows == 1){
			if($dbPassword != $oldPassword){
				$_SESSION['msg'] = 'INCORRECT PASSWORD';
				return false;
			}
		}else{
			$_SESSION['msg'] = 'DATABASE ERROR';
			return false;
		}
	}else{
		$_SESSION['msg'] = 'DATABASE ERROR';
		return false;
	}
	if ($stmt = $mysqli->prepare("UPDATE Account SET Account.password = ? WHERE Account.id = ?")) {
		$password1 = sha1($password1);
		$stmt->bind_param('si', $password1, $userid);
		$stmt->execute();
		$_SESSION['msg'] = 'PASSWORD UPDATED SUCCESSFULLY';
		return true;
	}else{
		$_SESSION['msg'] = 'DATABASE ERROR';
		return false;
	}
}
function editName($mysqli, $password, $name){
	$userid = $_SESSION['userid'];
	if(!login_check($mysqli)){
		return false;
	}
	if($name != htmlspecialchars($name)){
		$_SESSION['msg'] = 'NAME MAY ONLY CONTAIN LETTERS AND NUMBERS';
		return false;
	}
	if($stmt = $mysqli->prepare("SELECT password FROM Account WHERE id = ? LIMIT 1")){
		$stmt->bind_param('i', $userid);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($dbPassword);
		$stmt->fetch();
		$password = sha1($password);
		if($stmt->num_rows == 1){
			if($dbPassword != $password){
				$_SESSION['msg'] = 'INCORRECT PASSWORD';
				return false;
			}
		}else{
			$_SESSION['msg'] = 'DATABASE ERROR';
			return false;
		}
	}else{
		$_SESSION['msg'] = 'DATABASE ERROR';
		return false;
	}
	if ($stmt = $mysqli->prepare("UPDATE Account SET Account.name = ? WHERE Account.id = ?")) {
		$stmt->bind_param('si', $name, $userid);
		$stmt->execute();
		$_SESSION['msg'] = 'NAME UPDATED SUCCESSFULLY';
		return true;
	}else{
		$_SESSION['msg'] = 'DATABASE ERROR';
		return false;
	}	
}

function editAccontName($mysqli, $password, $accountName1, $accountName2){
	$userid = $_SESSION['userid'];
	if(!login_check($mysqli)){
		return false;
	}
	if($accountName1 != $accountName2){
		$_SESSION['msg'] = 'ACCOUNT NAMES MUST MATCH';
		return false;
	}
	if(mb_strlen($accountName1) < 3){
		$_SESSION['msg'] = 'ACCOUNT NAME MUST BE AT LEAST 4 CHARACTERS LONG';
		return false;
	}
	if($accountName1 != htmlspecialchars($accountName1)){
		$_SESSION['msg'] = 'ACCOUNT NAME MAY ONLY CONTAIN LETTERS AND NUMBERS';
		return false;
	}
	if($stmt = $mysqli->prepare("SELECT password FROM Account WHERE id = ? LIMIT 1")){
		$stmt->bind_param('i', $userid);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($dbPassword);
		$stmt->fetch();
		$password = sha1($password);
		if($stmt->num_rows == 1){
			if($dbPassword != $password){
				$_SESSION['msg'] = 'INCORRECT PASSWORD';
				return false;
			}
		}else{
			$_SESSION['msg'] = 'DATABASE ERROR';
			return false;
		}
	}else{
		$_SESSION['msg'] = 'DATABASE ERROR';
		return false;
	}
	if($stmt = $mysqli->prepare("SELECT accName FROM Account WHERE accName = ?")){
		$stmt->bind_param('s', $accountName1);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($tmp);
		$stmt->fetch();
		if($stmt->num_rows > 0){
			$_SESSION['msg'] = 'ACCOUNT NAME ALREADY IN USE';
			return false;
		}
	}
	if ($stmt = $mysqli->prepare("UPDATE Account SET Account.accName = ? WHERE Account.id = ?")) {
		$stmt->bind_param('si', $accountName1, $userid);
		$stmt->execute();
		$_SESSION['msg'] = 'ACCOUNT NAME UPDATED SUCCESSFULLY';
		$_SESSION['username'] = $accountName1;
		return true;
	}else{
		$_SESSION['msg'] = 'DATABASE ERROR';
		return false;
	}	
}
function editEmail($mysqli, $password, $email1, $email2){
	$userid = $_SESSION['userid'];
	if(!loginCheck($mysqli)){
		return false;
	}
	if($email1 != $email2){
		$_SESSION['msg'] = 'EMAIL ADRESSES MUST MATCH';
		return false;
	}
	if($stmt = $mysqli->prepare("SELECT password FROM Account WHERE id = ? LIMIT 1")){
		$stmt->bind_param('i', $userid);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($dbPassword);
		$stmt->fetch();
		$password = sha1($password);
		if($stmt->num_rows == 1){
			if($dbPassword != $password){
				$_SESSION['msg'] = 'INCORRECT PASSWORD';
				return false;
			}
		}else{
			$_SESSION['msg'] = 'DATABASE ERROR';
			return false;
		}
	}else{
		$_SESSION['msg'] = 'DATABASE ERROR';
		return false;
	}
	if($stmt = $mysqli->prepare("SELECT email FROM Account WHERE email = ?")){
		$stmt->bind_param('s', $email1);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($tmp);
		$stmt->fetch();
		if($stmt->num_rows > 0){
			$_SESSION['msg'] = 'EMAIL ALREADY IN USE';
			return false;
		}
	}
	if ($stmt = $mysqli->prepare("UPDATE Account SET Account.email = ? WHERE Account.id = ?")) {
		$stmt->bind_param('si', $email1, $userid);
		$stmt->execute();
		$_SESSION['msg'] = 'EMAIL ADRESS UPDATED SUCCESSFULLY';
		return true;
	}else{
		$_SESSION['msg'] = 'DATABASE ERROR';
		return false;
	}	
}








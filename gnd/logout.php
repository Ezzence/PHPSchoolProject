<?php
include_once 'func.php';
sec_session_start();
$_SESSION = array();
$params = session_get_cookie_params();
setcookie(session_name(), '', time()-42000, $params['path'], 
		$params['domain'], $params['secure'], $params['httponly']);
	session__destroy();
	unset($_SESSION);
	session_regenerate_id(true);
	header('Location: ../common.php');
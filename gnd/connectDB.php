<?php

include_once 'init.php';
$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);

if(mysqli_connect_errno()){
	printf("Connection to the database failed: %s\n", mysqli_connect_error());
	exit();
}
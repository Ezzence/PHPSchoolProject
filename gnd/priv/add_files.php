<?php
include_once('../index.php');
include_once('../connectDB.php');

if(login_check($mysqli) && $_SESSION['filePriv'] == true){
	
	if(isset($_POST['addFiles'])){
		if($_FILES['fileUpload']['error']==0) {
			if ($stmt = $mysqli->prepare("INSERT INTO GNDFiles (accountid, addDate, file) 
				VALUES (?, CURDATE(), ?)")) {
				$file = basename($_FILES["fileUpload"]["name"]);
				$stmt->bind_param('is', $_SESSION['userid'], $file);
				$stmt->execute();
				echo $lang['FILE_UPLOAD_SUCCESS'];
			}else{
				echo 'DATABASE ERROR';
				$_SESSION['msg'] = 'DATABASE ERROR';
			}
		}else{
			echo '<br>There was an error uploading the file.';
		}
	}
	
	echo '<br><div id="filesEditor">
	<br><form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '" enctype="multipart/form-data">
	<br>File: <input type="file" name="fileUpload" id="fileUpload">
	<br><br><input type="submit" name="addFiles" value="submit" />
	</form>
	</div>';
}else{
	echo "NOT ALLOWED";
}
echo '</body>
	</html>';
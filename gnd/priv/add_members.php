<?php
include_once('../index.php');
include_once('../connectDB.php');

if(login_check($mysqli) && $_SESSION['teamPriv'] == true){
	
	if(isset($_POST['addMembers'])){
		echo '<br><br>';
		echo  $_POST['membersNameArea'];
		echo '<br><br>';
		echo  $_POST['membersTextAreaHu'];
		echo '<br><br>';
		echo  $_POST['membersTextAreaEn'];
		
		if($_FILES['pictureUpload']['error']==0) {
			if ($stmt = $mysqli->prepare("INSERT INTO GNDTeam (accountid, name, text, textEng, 
			pic) VALUES (?, ?, ?, ?, ?)")) {
				$pic = '/gnd/pic/' .  basename($_FILES["pictureUpload"]["name"]);
				$stmt->bind_param('issss', $_SESSION['userid'], $_POST['membersNameArea'],
				$_POST['membersTextAreaHu'], $_POST['membersTextAreaEn'], $pic);
				$stmt->execute();
			}else{
				echo 'DATABASE ERROR';
				$_SESSION['msg'] = 'DATABASE ERROR';
			}
		}else{
			if ($stmt = $mysqli->prepare("INSERT INTO GNDTeam (accountid, name, text, textEng, 
			pic) VALUES (?, ?, ?, ?, '/gnd/pic/default.jpg')")) {
				$stmt->bind_param('isss', $_SESSION['userid'], $_POST['membersNameArea'],
				$_POST['membersTextAreaHu'], $_POST['membersTextAreaEn']);
				$stmt->execute();
			}else{
				echo 'DATABASE ERROR';
				$_SESSION['msg'] = 'DATABASE ERROR';
			}
		}
	}
	
	echo '<br><div id="membersEditor">
	<script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script> <script type="text/javascript">
	 bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
	</script><br><br>
	<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '" enctype="multipart/form-data">
	<br>Név (name):
	<textarea name="membersNameArea" style="width: 30%; height: 30px;">
	</textarea>
	<br>Leírás (magyar):
	<textarea name="membersTextAreaHu" style="width: 50%; height: 300px;">
    Szöveg
	</textarea>
	<br>Description (in english):
	<textarea name="membersTextAreaEn" style="width: 50%; height: 300px;">
    Text not available in english
	</textarea>
	<br>Kép: <input type="file" name="pictureUpload" id="pictureUpload">
	<br><br><input type="submit" name="addMembers" value="submit" />
	</form>
	</div>';
}else{
	echo "NOT ALLOWED";
}
echo '</body>
	</html>';
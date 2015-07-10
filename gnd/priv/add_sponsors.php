<?php
include_once('../index.php');
include_once('../connectDB.php');

if(login_check($mysqli) && $_SESSION['teamPriv'] == true){
	
	if(isset($_POST['addSponsors'])){
		echo '<br><br>';
		echo  $_POST['sponsorsLinkArea'];
		echo '<br><br>';
		echo  $_POST['sponsorsNameArea'];
		echo '<br><br>';
		echo  $_POST['sponsorsTextAreaHu'];
		echo '<br><br>';
		echo  $_POST['sponsorsTextAreaEn'];
		
		if($_FILES['pictureUpload']['error']==0) {
			if ($stmt = $mysqli->prepare("INSERT INTO GNDSponsor (accountid, name, text, textEng, link,
			pic) VALUES (?, ?, ?, ?, ?, ?)")){
				$pic = '/gnd/pic/' .  basename($_FILES["pictureUpload"]["name"]);
				$stmt->bind_param('isssss', $_SESSION['userid'], $_POST['sponsorsNameArea'],
				$_POST['sponsorsTextAreaHu'], $_POST['sponsorsTextAreaEn'], $_POST['sponsorsLinkArea']);
				$stmt->execute();
			}else{
				echo 'DATABASE ERROR';
				$_SESSION['msg'] = 'DATABASE ERROR';
			}
			
		}else{
			if ($stmt = $mysqli->prepare("INSERT INTO GNDSponsor (accountid, name, text, textEng, link,
			pic) VALUES (?, ?, ?, ?, ?,'/gnd/pic/default.jpg')")) {
				$stmt->bind_param('issss', $_SESSION['userid'], $_POST['sponsorsNameArea'],
				$_POST['sponsorsTextAreaHu'], $_POST['sponsorsTextAreaEn'], $_POST['sponsorsLinkArea']);
				$stmt->execute();
			}else{
				echo 'DATABASE ERROR';
				$_SESSION['msg'] = 'DATABASE ERROR';
			}
		}
	}
	
	echo '<br><div id="sponsorsEditor">
	<script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script> <script type="text/javascript">
	 bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
	</script><br><br>
	<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '" enctype="multipart/form-data">
	Link:
	<textarea name="sponsorsLinkArea" style="width: 40%; height: 30px;">
	http://www.valami.valami
	</textarea>
	<br>Név (name):
	<textarea name="sponsorsNameArea" style="width: 30%; height: 30px;">
	</textarea>
	<br>Leírás (magyar):
	<textarea name="sponsorsTextAreaHu" style="width: 50%; height: 300px;">
    Szöveg
	</textarea>
	<br>Description (in english):
	<textarea name="sponsorsTextAreaEn" style="width: 50%; height: 300px;">
    Text not available in english
	</textarea>
	<br>Kép: <input type="file" name="pictureUpload" id="pictureUpload">
	<br><br><input type="submit" name="addSponsors" value="submit" />
	</form>
	</div>';
}else{
	echo "NOT ALLOWED";
}
echo '</body>
	</html>';
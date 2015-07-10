<?php
include_once('../index.php');
include_once('../connectDB.php');

if(login_check($mysqli) && $_SESSION['newsPriv'] == true){
	
	if(isset($_POST['addNews'])){
		echo '<br\><br>';
		echo  $_POST['newsTitleAreaHu'];
		echo '<br><br>';
		echo  $_POST['newsTextAreaHu'];
		echo '<br><br>';
		echo  $_POST['newsTitleAreaEn'];
		echo '<br><br>';
		echo  $_POST['newsTextAreaEn'];
		
		if ($stmt = $mysqli->prepare("INSERT INTO GNDDocs (accountid, title, titleEng, link, text, textEng, 
		addDate, pic, revealDate) VALUES (?, ?, ?, 'NO', ?, ? , CURDATE(), ?, ?)")) {
			if($_FILES['pictureUpload']['error']==0) {
				$pic = '/gnd/pic/' .  basename($_FILES["pictureUpload"]["name"]);
			}else{
				$pic = '/gnd/pic/default.jpg';
			}
			$stmt->bind_param('issssss', $_SESSION['userid'], $_POST['newsTitleAreaHu'], 
			$_POST['newsTitleAreaEn'], $_POST['newsTextAreaHu'], $_POST['newsTextAreaEn'], $pic, $_POST['newsRevealDate']);
			$stmt->execute();
		}else{
			echo 'DATABASE ERROR';
			$_SESSION['msg'] = 'DATABASE ERROR';
		}
		
		/*if($_FILES['pictureUpload']['error']==0) {
			if ($stmt = $mysqli->prepare("INSERT INTO GNDDocs (accountid, title, titleEng, link, text, textEng, 
			addDate, pic) VALUES (?, ?, ?, 'NO', ?, ? , CURDATE(), ?)")) {
				$pic = '/gnd/pic/' .  basename($_FILES["pictureUpload"]["name"]);
				$stmt->bind_param('isssss', $_SESSION['userid'], $_POST['newsTitleAreaHu'], 
				$_POST['newsTitleAreaEn'], $_POST['newsTextAreaHu'], $_POST['newsTextAreaEn'], $pic);
				$stmt->execute();
			}else{
				echo 'DATABASE ERROR';
				$_SESSION['msg'] = 'DATABASE ERROR';
			}
		}
		else{
			if ($stmt = $mysqli->prepare("INSERT INTO GNDDocs (accountid, title, titleEng, link, text, textEng, 
			addDate, pic) VALUES (?, ?, ?, 'NO', ?, ? , CURDATE(), '/gnd/pic/default.jpg')")) {

				$stmt->bind_param('issss', $_SESSION['userid'], $_POST['newsTitleAreaHu'], 
				$_POST['newsTitleAreaEn'], $_POST['newsTextAreaHu'], $_POST['newsTextAreaEn']);
				$stmt->execute();
			}else{
				echo 'DATABASE ERROR';
				$_SESSION['msg'] = 'DATABASE ERROR';
			}
		}*/
	}
	
	echo '<br><div id="newsEditor">
	<script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script> <script type="text/javascript">
	bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
	</script><br><br>
	<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '" enctype="multipart/form-data">
	Cím (magyar):
	<textarea name="newsTitleAreaHu" style="width: 30%; height: 30px;">
    Cím
	</textarea>
	<br>Hír szövege (magyar):
	<textarea name="newsTextAreaHu" style="width: 50%; height: 300px;">
    Szöveg
	</textarea>
	<br><br>Title (in english):
	<textarea name="newsTitleAreaEn" style="width: 30%; height: 30px;">
    Title not available in english
	</textarea>
	<br>News Article (in english):
	<textarea name="newsTextAreaEn" style="width: 50%; height: 300px;">
    Text not available in english
	</textarea>
	<br>Kép: <input type="file" name="pictureUpload" id="pictureUpload">
	<br><label for="revealDate">Megjelenési dátum: </label>
	<input type="date" id="revealDate" name="newsRevealDate" min="' . date('Y-m-d') . '" value="' . date('Y-m-d') . '">
	<br><br><button type="submit" name="addNews" value="submit" > submit </button>
	</form>
	</div>';
}else{
	echo "NOT ALLOWED";
}
echo '</body>
	</html>';